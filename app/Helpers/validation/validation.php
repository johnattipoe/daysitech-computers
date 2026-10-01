<?php
/**
 * Lightweight validation helper.
 *
 * Usage:
 *   $v = Validator::make($_POST, [
 *       'email' => 'required|email',
 *       'password' => 'required|min:8',
 *   ]);
 *   if ($v->fails()) { $errors = $v->errors(); }
 */

class Validator
{
    protected array $data;
    protected array $rules;
    protected array $errors = [];

    protected function __construct(array $data, array $rules)
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->run();
    }

    public static function make(array $data, array $rules): self
    {
        return new self($data, $rules);
    }

    protected function run(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $params = [];
                if (str_contains($rule, ':')) {
                    [$rule, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                }
                $this->applyRule($field, $value, $rule, $params);
            }
        }
    }

    protected function applyRule(string $field, mixed $value, string $rule, array $params): void
    {
        $label = ucfirst(str_replace('_', ' ', $field));

        switch ($rule) {
            case 'required':
                if ($value === null || trim((string) $value) === '') {
                    $this->addError($field, "{$label} is required.");
                }
                break;
            case 'email':
                if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "Enter a valid email address.");
                }
                break;
            case 'min':
                if ($value !== null && strlen((string) $value) < (int) $params[0]) {
                    $this->addError($field, "{$label} must be at least {$params[0]} characters.");
                }
                break;
            case 'max':
                if ($value !== null && strlen((string) $value) > (int) $params[0]) {
                    $this->addError($field, "{$label} must not exceed {$params[0]} characters.");
                }
                break;
            case 'numeric':
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    $this->addError($field, "{$label} must be a number.");
                }
                break;
            case 'confirmed':
                $confirmField = $field . '_confirmation';
                if (($this->data[$confirmField] ?? null) !== $value) {
                    $this->addError($field, "{$label} confirmation does not match.");
                }
                break;
            case 'phone':
                if ($value && !preg_match('/^[0-9+\-\s()]{7,20}$/', $value)) {
                    $this->addError($field, "Enter a valid phone number.");
                }
                break;
            case 'in':
                if ($value !== null && $value !== '' && !in_array($value, $params, true)) {
                    $this->addError($field, "{$label} is invalid.");
                }
                break;
            case 'strong_password':
                if ($value && !preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $value)) {
                    $this->addError($field, "Password must be at least 8 characters and include a letter and a number.");
                }
                break;
        }
    }

    protected function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0];
        }
        return null;
    }
}
