# Marketfield marketplace

Laravel 11 marketplace starter with MySQL, Sanctum bearer tokens, Spatie roles/permissions, Spatie Media Library uploads, and a Bootstrap 5 Blade storefront.

## Local setup

Create a MySQL database and update `.env` with its actual username and password:

```sql
CREATE DATABASE marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Then run from the `marketplace` project directory:

```powershell
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

For active frontend development, run `npm run dev` in a second terminal. Product uploads accept JPEG, PNG, and WebP images up to 5 MB. GD or Imagick is recommended for image conversions.

Sellers select a currency for each listing. The configured exchange rates in `config/marketplace.php` are demo estimates; cart subtotals are grouped by currency, and checkout stores the converted order total in USD while preserving each line item's listing currency.

The local seed creates `seller@example.com` with password `password` and sample catalog items. Change or remove this account before deploying anywhere shared.

## API

Register or sign in at `POST /api/register` or `POST /api/login`; both return a Sanctum token. Send it as `Authorization: Bearer <token>`.

- `GET /api/products?q=...&category=...`
- `GET /api/products/{slug}`
- `POST /api/checkout` with `items: [{ "product_id": 1, "quantity": 2 }]`
- `POST /api/vendor/products`, `PUT /api/vendor/products/{id}`, and `DELETE /api/vendor/products/{id}` require an authenticated vendor with the relevant product permission. Include `currency_code` when creating or updating a product.
- `POST /api/logout` revokes the current token.

Checkout locks inventory rows, verifies availability, snapshots each unit price and currency, converts item totals to USD using the configured estimates, and writes one order with vendor-attributed line items. This starter does not process payments, shipping, or tax.