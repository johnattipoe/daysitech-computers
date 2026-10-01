<?php

namespace App\Services;

/**
 * FirebaseService
 * ----------------
 * Thin REST wrapper around:
 *   - Firestore (documents CRUD + simple queries)          -> firestore.googleapis.com
 *   - Firebase Identity Toolkit (email/password auth)       -> identitytoolkit.googleapis.com
 *
 * Server-to-server Firestore calls are authenticated with an OAuth2 access
 * token generated from the service account JSON (JWT Bearer flow), so no
 * Firebase Admin SDK / Composer package is required — just cURL + openssl,
 * both of which ship with PHP by default.
 *
 * If no service account file is present, Firestore calls fall back to
 * unauthenticated requests (only works if your Firestore rules allow it —
 * fine for local prototyping, NOT recommended for production).
 */
class FirebaseService
{
    protected array $cfg;
    protected string $projectId;
    protected ?string $accessToken = null;

    public function __construct()
    {
        $this->cfg = config('firebase');
        $this->projectId = $this->cfg['project_id'];
    }

    // =========================================================
    //  FIRESTORE — low level helpers
    // =========================================================

    protected function firestoreBase(): string
    {
        return "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
    }

    protected function authHeader(): array
    {
        $token = $this->getAccessToken();
        return $token ? ['Authorization: Bearer ' . $token] : [];
    }

    /**
     * Exchanges the service account credentials for a short-lived OAuth2
     * access token via the JWT Bearer grant. Cached per-request.
     */
    protected function getAccessToken(): ?string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $path = $this->cfg['service_account_path'] ?? null;
        if (!$path || !file_exists($path)) {
            return null; // fall back to unauthenticated / rules-based access
        }

        $account = json_decode(file_get_contents($path), true);
        $now = time();

        $header = $this->b64(['alg' => 'RS256', 'typ' => 'JWT']);
        $claims = $this->b64([
            'iss'   => $account['client_email'],
            'scope' => 'https://www.googleapis.com/auth/datastore',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]);

        $signatureInput = "{$header}.{$claims}";
        openssl_sign($signatureInput, $signature, $account['private_key'], 'sha256WithRSAEncryption');
        $jwt = $signatureInput . '.' . rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        $response = $this->curl('https://oauth2.googleapis.com/token', 'POST', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ], [], true);

        $this->accessToken = $response['access_token'] ?? null;
        return $this->accessToken;
    }

    protected function b64(array $data): string
    {
        return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
    }

    protected function curl(string $url, string $method = 'GET', array $body = [], array $headers = [], bool $asForm = false)
    {
        $ch = curl_init($url);
        $defaultHeaders = $asForm ? ['Content-Type: application/x-www-form-urlencoded'] : ['Content-Type: application/json'];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => array_merge($defaultHeaders, $headers),
            CURLOPT_TIMEOUT        => 15,
        ]);

        if (!empty($body)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $asForm ? http_build_query($body) : json_encode($body));
        }

        $raw = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            log_message('error', "FirebaseService cURL error ({$url}): {$err}");
            return null;
        }

        return json_decode($raw, true);
    }

    // Converts a plain PHP array into Firestore's typed document field format
    protected function toFirestoreFields(array $data): array
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[$key] = $this->toFirestoreValue($value);
        }
        return $fields;
    }

    protected function toFirestoreValue(mixed $value): array
    {
        return match (true) {
            is_null($value)    => ['nullValue' => null],
            is_bool($value)    => ['booleanValue' => $value],
            is_int($value)     => ['integerValue' => (string) $value],
            is_float($value)   => ['doubleValue' => $value],
            is_array($value) && array_is_list($value) => [
                'arrayValue' => ['values' => array_map([$this, 'toFirestoreValue'], $value)],
            ],
            is_array($value)   => ['mapValue' => ['fields' => $this->toFirestoreFields($value)]],
            default            => ['stringValue' => (string) $value],
        };
    }

    // Converts a Firestore document (with typed fields) back into a plain PHP array
    protected function fromFirestoreDocument(array $doc): array
    {
        $result = $this->fromFirestoreFields($doc['fields'] ?? []);
        if (isset($doc['name'])) {
            $parts = explode('/', $doc['name']);
            $result['id'] = end($parts);
        }
        if (isset($doc['createTime'])) {
            $result['_createTime'] = $doc['createTime'];
        }
        return $result;
    }

    protected function fromFirestoreFields(array $fields): array
    {
        $out = [];
        foreach ($fields as $key => $value) {
            $out[$key] = $this->fromFirestoreValue($value);
        }
        return $out;
    }

    protected function fromFirestoreValue(array $value)
    {
        return match (true) {
            array_key_exists('stringValue', $value)  => $value['stringValue'],
            array_key_exists('integerValue', $value) => (int) $value['integerValue'],
            array_key_exists('doubleValue', $value)  => (float) $value['doubleValue'],
            array_key_exists('booleanValue', $value) => (bool) $value['booleanValue'],
            array_key_exists('nullValue', $value)    => null,
            array_key_exists('timestampValue', $value) => $value['timestampValue'],
            array_key_exists('arrayValue', $value)   => array_map([$this, 'fromFirestoreValue'], $value['arrayValue']['values'] ?? []),
            array_key_exists('mapValue', $value)     => $this->fromFirestoreFields($value['mapValue']['fields'] ?? []),
            default => null,
        };
    }

    // =========================================================
    //  FIRESTORE — public CRUD API
    // =========================================================

    /** Fetch a single document by collection + id. Returns null if missing. */
    public function get(string $collection, string $id): ?array
    {
        $res = $this->curl("{$this->firestoreBase()}/{$collection}/{$id}", 'GET', [], $this->authHeader());
        if (!$res || isset($res['error'])) return null;
        return $this->fromFirestoreDocument($res);
    }

    /** List documents in a collection (optionally with basic pagination). */
    public function all(string $collection, int $pageSize = 100, ?string $pageToken = null): array
    {
        $url = "{$this->firestoreBase()}/{$collection}?pageSize={$pageSize}";
        if ($pageToken) $url .= "&pageToken={$pageToken}";

        $res = $this->curl($url, 'GET', [], $this->authHeader());
        $docs = $res['documents'] ?? [];
        return array_map([$this, 'fromFirestoreDocument'], $docs);
    }

    /**
     * Run a structured query with simple equality/range filters.
     * $filters = [['field' => 'category', 'op' => 'EQUAL', 'value' => 'Laptops']]
     */
    public function query(string $collection, array $filters = [], ?string $orderBy = null, int $limit = 50): array
    {
        $structuredQuery = [
            'from'  => [['collectionId' => $collection]],
            'limit' => $limit,
        ];

        if (!empty($filters)) {
            $compositeFilters = array_map(function ($f) {
                return [
                    'fieldFilter' => [
                        'field' => ['fieldPath' => $f['field']],
                        'op'    => $f['op'] ?? 'EQUAL',
                        'value' => $this->toFirestoreValue($f['value']),
                    ],
                ];
            }, $filters);

            $structuredQuery['where'] = count($compositeFilters) === 1
                ? $compositeFilters[0]
                : ['compositeFilter' => ['op' => 'AND', 'filters' => $compositeFilters]];
        }

        if ($orderBy) {
            [$field, $dir] = array_pad(explode(':', $orderBy), 2, 'ASCENDING');
            $structuredQuery['orderBy'] = [[
                'field' => ['fieldPath' => $field],
                'direction' => strtoupper($dir) === 'DESC' ? 'DESCENDING' : 'ASCENDING',
            ]];
        }

        $projectPath = str_replace('/documents', '', $this->firestoreBase());
        $res = $this->curl("{$projectPath}/documents:runQuery", 'POST', ['structuredQuery' => $structuredQuery], $this->authHeader());

        if (!is_array($res)) return [];

        $out = [];
        foreach ($res as $row) {
            if (isset($row['document'])) {
                $out[] = $this->fromFirestoreDocument($row['document']);
            }
        }
        return $out;
    }

    /** Create a document. If $id is null, Firestore auto-generates one. */
    public function create(string $collection, array $data, ?string $id = null): array
    {
        $url = "{$this->firestoreBase()}/{$collection}";
        if ($id) $url .= "?documentId={$id}";

        $res = $this->curl($url, 'POST', ['fields' => $this->toFirestoreFields($data)], $this->authHeader());
        return $res ? $this->fromFirestoreDocument($res) : [];
    }

    /** Update (merge) fields on an existing document. */
    public function update(string $collection, string $id, array $data): array
    {
        $mask = implode('&', array_map(fn($f) => 'updateMask.fieldPaths=' . urlencode($f), array_keys($data)));
        $url = "{$this->firestoreBase()}/{$collection}/{$id}?{$mask}";

        $res = $this->curl($url, 'PATCH', ['fields' => $this->toFirestoreFields($data)], $this->authHeader());
        return $res ? $this->fromFirestoreDocument($res) : [];
    }

    public function delete(string $collection, string $id): bool
    {
        $res = $this->curl("{$this->firestoreBase()}/{$collection}/{$id}", 'DELETE', [], $this->authHeader());
        return $res !== null;
    }

    // =========================================================
    //  FIREBASE AUTH — Identity Toolkit (email/password)
    // =========================================================

    protected function identityUrl(string $action): string
    {
        return "https://identitytoolkit.googleapis.com/v1/accounts:{$action}?key={$this->cfg['api_key']}";
    }

    public function signUp(string $email, string $password): array
    {
        return (array) $this->curl($this->identityUrl('signUp'), 'POST', [
            'email' => $email, 'password' => $password, 'returnSecureToken' => true,
        ]);
    }

    public function signIn(string $email, string $password): array
    {
        return (array) $this->curl($this->identityUrl('signInWithPassword'), 'POST', [
            'email' => $email, 'password' => $password, 'returnSecureToken' => true,
        ]);
    }

    public function sendPasswordResetEmail(string $email): array
    {
        return (array) $this->curl($this->identityUrl('sendOobCode'), 'POST', [
            'requestType' => 'PASSWORD_RESET', 'email' => $email,
        ]);
    }

    public function sendEmailVerification(string $idToken): array
    {
        return (array) $this->curl($this->identityUrl('sendOobCode'), 'POST', [
            'requestType' => 'VERIFY_EMAIL', 'idToken' => $idToken,
        ]);
    }

    public function deleteAuthUser(string $idToken): array
    {
        return (array) $this->curl($this->identityUrl('delete'), 'POST', ['idToken' => $idToken]);
    }
}
