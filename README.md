# Daysitech Computers

A combined **E-Commerce + Computer Repair + Inventory + Customer + Admin Management System**, built in plain PHP (no framework) with **Firebase (Firestore + Authentication)** as the backend, following the project structure you specified.

---

## ✨ What's included

- **Storefront**: product catalog with search/filter/sort, product detail pages with specs & reviews, product comparison, categories, cart, checkout (cash on delivery, mobile money & card via Paystack).
- **Repair booking**: public booking form, ticket-number tracking with a visual status timeline, customer repair history.
- **Customer accounts**: registration/login (Firebase Auth), dashboard, order history, repair history, profile management.
- **Admin panel** (`/admin`): dashboard with revenue chart & low-stock alerts, product CRUD, categories/brands, inventory management with movement log, order management, repair ticket management, customer management, staff accounts, payment log, review moderation, sales/inventory/repair reports, business settings.
- **Design system**: a custom navy/graphite + copper accent theme (not a generic Bootstrap look) defined in `public/assets/scss`, compiled to `public/assets/css`.
- **CDN libraries wired in**: Bootstrap 5 (grid/offcanvas/modals), Font Awesome 6 (icons), Google Fonts (Space Grotesk / Inter / JetBrains Mono), AOS (scroll animations), SweetAlert2 (toasts & confirm dialogs), Chart.js (admin analytics).

---

## 🗂 Architecture

Plain PHP, PSR-4 autoloaded via Composer, MVC-ish:

```
app/Controllers   → request handling for storefront routes
app/Models        → one class per Firestore collection (thin data layer)
app/Services      → business logic (auth, orders, repairs, inventory, payments, email, notifications)
app/Middleware    → auth / admin / role guards
app/Helpers       → global functions, Router, Validator, security helpers
routes/           → web.php (storefront), api.php (JSON endpoints), admin.php (admin panel)
views/            → storefront templates
admin/            → self-contained admin panel pages (each fetches its own data + renders admin/layout.php)
public/           → the real document root: assets (css/js/scss/images) + front controller
```

There's no ORM — Firestore is a document database, so `app/Services/FirebaseService/FirebaseService.php` is a small REST wrapper (cURL + a JWT service-account flow) that every model builds on.

---

## ⚙️ Setup

### 1. Requirements
- PHP 8.1+
- Composer
- A Firebase project (Firestore + Authentication enabled, Email/Password sign-in method turned on)
- A Paystack account (for card/mobile money checkout) — optional, cash-on-delivery works without it

### 2. Install dependencies
```bash
composer install
```

### 3. Configure environment
Edit `.env` (already scaffolded with sane defaults):
```
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_API_KEY=your-web-api-key
FIREBASE_SERVICE_ACCOUNT_PATH=storage/firebase-service-account.json
PAYSTACK_SECRET_KEY=sk_test_xxxxx
PAYSTACK_PUBLIC_KEY=pk_test_xxxxx
```

### 4. Add your Firebase service account
Download a service account JSON key from **Firebase Console → Project Settings → Service Accounts**, save it to `storage/firebase-service-account.json` (already gitignored). This lets the server authenticate to Firestore without exposing credentials to the browser.

> Without this file, Firestore calls fall back to unauthenticated requests — only useful if your Firestore security rules explicitly allow public read/write, which is **not recommended** beyond local prototyping.

### 5. Firestore collections
No migrations needed — collections are created automatically on first write. Collection names are centralized in `config/firebase.php`:
`users`, `products`, `categories`, `brands`, `orders`, `repairs`, `inventory_movements`, `payments`, `reviews`, `notifications`, `carts`.

Seed at least one **category**, **brand**, and a few **products** from `/admin/products` after logging in as an admin (see below).

### 6. Create your first admin account
The registration form only creates `customer` accounts. To get your first admin:
1. Register a normal account at `/register`.
2. In the Firebase Console → Firestore → `users` collection, find your user document and change `role` from `customer` to `admin`.
3. Log out and back in — you'll now see the **Admin Panel** link in your account menu.

### 7. Run it
Point your web server's document root at `/public` (recommended), **or** just serve the project root — `index.php` at the root forwards every request into `/public` automatically, so shared hosting without docroot control still works.

Local quick start:
```bash
php -S localhost:8000 -t public
```

### Run with Docker

After configuring `.env` and placing the Firebase service-account JSON at the configured path, build and start the app:

```bash
docker compose up --build
```

Open `http://localhost:8000`. The container serves `/public` through Apache, enables URL rewriting, and installs the PHP cURL extension used by the Firebase and payment services. Environment files and the Firebase private key are excluded from the image build; Compose supplies `.env` and mounts the service-account file read-only. This Compose configuration is for local development; set production credentials, `APP_DEBUG=false`, and HTTPS at your production host before deployment.

---

## 💳 Payments

Card and mobile money payments are processed via **Paystack** (`app/Services/PaymentService/PaymentService.php`), which supports Ghanaian mobile money out of the box. Swap in Flutterwave/Stripe by editing that one file — nothing else in the app needs to change.

## 📧 Email

`app/Services/EmailService/EmailService.php` uses PHP's native `mail()` by default (zero dependencies). For production deliverability, swap `send()` in that file for SMTP (PHPMailer) or an HTTP email API (Resend/SendGrid/Postmark) — every other part of the app calls the semantic methods (`sendOrderConfirmation`, `sendRepairStatusUpdate`, etc.), so only this one file needs to change.

## 🔒 Security notes

- CSRF tokens on every state-changing form (`csrf_field()` / `require_csrf()`).
- Passwords are handled by Firebase Authentication (never stored locally).
- Basic session-based rate limiting on login attempts.
- All output is escaped via `e()` (htmlspecialchars) by default.

---

## 🎨 Customizing the look

Edit the design tokens in `public/assets/scss/_variables.scss` (colors, spacing, radii, type scale), then recompile:
```bash
npm install -g sass
sass public/assets/scss/main.scss public/assets/css/style.css
sass public/assets/scss/_admin-entry.scss public/assets/css/admin.css
```
(Pre-compiled CSS is already checked in, so this step is optional unless you change the SCSS.)
