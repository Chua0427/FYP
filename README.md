# VeroSports Shopping Site

An e-commerce platform for selling sports products (footwear, apparel, equipment) built with PHP, MySQL, and Stripe for payments.

## Features

- Product catalog by gender, category, and brand
- Shopping cart with quantity management and live-upsdated badge
- Secure user authentication (password hashing, token management)
- Order management, checkout, payment processing via Stripe
- Order history, reviews, payment details, PDF invoice generator
- Admin dashboard for managing products, orders, and reports

## Technologies & Standards

- PHP 8.2 with `declare(strict_types=1)`
- MySQL / MariaDB for data storage
- Composer for dependency management (Monolog, Stripe SDK, Dompdf)
- PSR-12 coding standards
- Monolog for structured logging
- Prepared statements (no string concatenation in SQL)
- Output sanitized with `htmlspecialchars()`
- CSRF protection on all forms (token verification)
- Passwords stored with `password_hash()`
- Amounts handled in minor units (cents)
- Idempotent payment operations

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/Chua0427/FYP.git
   cd FYP
   ```
2. Install dependencies:
   ```bash
   composer install
   ```
3. Configure database:
   - Copy `connect_db/config.sample.php` to `connect_db/config.php`
   - Update DB credentials (host, username, password, database)
4. Import schema:
   ```bash
   mysql -u root -p FYP < schema.sql
   ```
5. Set up XAMPP/Apache root to point to **`/FYP/User`** directory or configure virtual host.

## Project Structure

```
/ (root)
  ├── FYP/                # Core application
  │   ├── Admin/          # Admin interface
  │   ├── User/           # User-facing pages (login, product, cart, order, payment)
  │   ├── connect_db/     # Database config
  │   └── app/            # Initialization, auth, services
  ├── vendor/             # Composer packages
  ├── composer.json
  └── schema.sql          # Database schema and seed data
```

## Usage

- Access home: `http://localhost/FYP/FYP/User/HomePage/homePage.php`
- Sign up or log in to add items to cart and checkout
- View order history, payment details, and write reviews
- Admin users (`user_type = 2`) can log in at `/FYP/Admin/Add_Admin/login.php`

## Logging

- Log files are generated under `app/logs/` via Monolog
- No debug output; use log files for troubleshooting

## Security

- All user input validated and sanitized
- CSRF tokens on POST requests
- HTTPS recommended for production to encrypt data in transit

## Contributing

1. Fork the project
2. Create a feature branch (`git checkout -b feature/XYZ`)
3. Commit your changes (`git commit -m "Add XYZ feature"`)
4. Push to the branch (`git push origin feature/XYZ`)
5. Open a pull request

---

Maintained by VeroSports Team. 2025 © All rights reserved. 