# Syncare Pharmacy

Syncare Pharmacy is a Laravel-based pharmacy management module with role-based access for admins and company users.

## Core Features

- Authentication (login/register/logout)
- Role-based dashboards (`admin`, `owner`, `manager`)
- Admin management:
	- Companies CRUD
	- Subscriptions CRUD
	- System settings (including global footer text)
	- Password reset for user accounts
- Company management:
	- Products CRUD
	- Invoices CRUD
	- Invoice PDF download
- Subscription reminder email flow:
	- Command: `app:send-subscription-alerts`
	- Legacy alias: `app:notify-subscription-expiring`
	- Daily schedule in `routes/console.php`

## Tech Stack

- PHP 8.3
- Laravel 11
- SQLite (default) or MySQL
- Vite + TailwindCSS

## Local Setup

1. Install dependencies:

	 ```bash
	 composer install
	 npm install
	 ```

2. Configure environment:

	 ```bash
	 cp .env.example .env
	 php artisan key:generate
	 ```

3. Configure DB (SQLite default):

	 ```bash
	 touch database/database.sqlite
	 ```

	 Ensure `.env` contains:

	 ```env
	 DB_CONNECTION=sqlite
	 DB_DATABASE=database/database.sqlite
	 ```

4. Run migrations + seeders:

	 ```bash
	 php artisan migrate --seed
	 ```

5. Build frontend assets:

	 ```bash
	 npm run build
	 ```

6. Start server:

	 ```bash
	 php artisan serve --host=0.0.0.0 --port=8080
	 ```

7. Open in browser:

	 ```
	 http://127.0.0.1:8080
	 ```

## Helpful Commands

- Clear caches:

	```bash
	php artisan optimize:clear
	```

- Send subscription reminders manually:

	```bash
	php artisan app:send-subscription-alerts
	```

- List routes:

	```bash
	php artisan route:list
	```

- Run tests:

	```bash
	php artisan test
	```

## Production Notes

- Do not use `php artisan serve` for production.
- Use Nginx + PHP-FPM and point the web root to `public/`.
- Set `APP_ENV=production`, `APP_DEBUG=false`, and configure a real mail provider.
- Configure scheduler with cron:

	```cron
	* * * * * php /path/to/pharmacy/artisan schedule:run >> /dev/null 2>&1
	```
