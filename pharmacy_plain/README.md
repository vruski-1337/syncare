# Pharmacy Plain (PHP + MySQL)

Modern, clean Pharmacy Management System built with **plain PHP** and **MySQL** (no Blade, no npm).

## Quick Start

1. Create database:
   - `CREATE DATABASE pharmacy_plain CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
2. Copy env:
   - `cp .env.example .env`
3. Update DB credentials in `.env`.
4. Setup schema + seed admin:
   - `php setup.php`
5. Run app:
   - `php -S 0.0.0.0:8080 -t public`

## Optional SQLite Dev Mode

If you want to run without MySQL:

1. In `.env`, set:
   - `DB_CONNECTION=sqlite`
   - `DB_DATABASE=database/pharmacy_plain.sqlite`
2. Run setup:
   - `php setup.php`
3. Start server:
   - `php -S 0.0.0.0:8080 -t public`

## Fixed Main Admin

- Username: `Vrushab`
- Password: `Fx993ms@vru`

The system enforces this account on every request.

## Role Rules

- `owner`: full company visibility, including profitability reports.
- `manager`: operational access, **no profit analytics**.
- `pharmacist`: clinical, inventory, prescription operations.
- `billing`: POS and payment workflows.
- `admin`: global multi-company administration.
