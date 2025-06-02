# VeroSports Payment System

A secure, Stripe-powered payment processing module for the VeroSports e-commerce platform, designed with idempotence, precision, and robust logging.

---

## Features

- **Checkout & Order Creation**
  - `checkout.php`: Review cart, shipping info, create an order record
- **Payment Methods**
  - `payment_methods.php`: Select payment (currently Stripe card)
- **Idempotent Payment Processing**
  - `process_payment.php`: Charge via Stripe SDK
  - Safe to retry without duplicate charges
- **Webhook Handling**
  - `webhook.php`: Listen for Stripe events (success, failure, refund)
  - Asynchronous status updates on `orders` & `payment` tables
- **Logging & Auditing**
  - Monolog logs to `logs/payment.log` + `payment_log` table
  - Detailed audit trail for all payment-related events
- **Security & Compliance**
  - CSRF protection on all forms
  - Amounts stored in **cents/pence** for monetary precision
  - Prepared statements for **SQL** (no string concatenation)
  - XSS defense via `htmlspecialchars()` on output
  - PSR-12 coding standards with `declare(strict_types=1);`

---

## Architecture & Flow

### 1. Checkout (`checkout.php`)
- Display order summary and shipping form
- Insert pending record in `orders` table
- Redirect to `payment_methods.php`

### 2. Payment Methods (`payment_methods.php`)
- Choose Stripe card payment
- Submit to `process_payment.php`

### 3. Process Payment (`process_payment.php`)
- Initialize Stripe with secret key
- Create or confirm PaymentIntent
- Record attempt in `payment` table
- Log success/failure with Monolog
- On success: mark `orders.delivery_status` as `pending`

### 4. Webhook Listener (`webhook.php`)
- Verify Stripe signature header
- Handle events: `payment_intent.succeeded`, `payment_intent.payment_failed`, `charge.refunded`
- Update `payment` and `orders` accordingly
- Ensure idempotence by checking existing `payment_id`

### 5. Result Pages
- `success.php`: Thank you & order summary
- `cancel.php`: Payment canceled or failed notice

---

## Database Schema

**Tables:**
- `orders`            (order_id, user_id, total_price, shipping_address, delivery_status, order_at)
- `order_items`      (order_item_id, order_id, product_id, quantity, price)
- `payment`           (payment_id, order_id, total_amount, payment_status, payment_method, stripe_id, currency, payment_at)
- `payment_log`       (log_id, payment_id, log_level, log_message, log_time)

```sql
-- Example: idempotent insert
INSERT INTO payment (payment_id, order_id, total_amount, ...) 
VALUES (:payment_id, :order_id, :amount, ...)
ON DUPLICATE KEY UPDATE last_error = VALUES(last_error);
```

---

## Configuration & Setup

1. **Clone & Install**
   ```bash
   git clone <repo-url>
   cd FYP/User/payment
   composer install
   ```

2. **Environment**
   Copy `.env.example -> .env` and configure:
   ```ini
   STRIPE_SECRET_KEY=sk_test_...
   STRIPE_WEBHOOK_SECRET=whsec_...
   DB_DSN=mysql:host=localhost;dbname=verosports;charset=utf8mb4
   DB_USER=root
   DB_PASS=
   ```

3. **Logs Directory**
   ```bash
   mkdir -p logs
   chmod 775 logs
   ```

4. **Database**
   - Import schema (see `schema.json`)
   - Ensure tables `payment` and `payment_log` exist

5. **Local Webhook Testing**
   ```bash
   npm install -g stripe-cli
   stripe login
   stripe listen --forward-to "http://localhost/FYP/User/payment/webhook.php"
   ```

---

## Security & Best Practices

- **CSRF**: Validate `csrf_token` on all POST forms
- **SQL**: Use PDO prepared statements
- **XSS**: Escape output with `htmlspecialchars()`
- **Logging**: Use Monolog for all sensitive actions
- **PSR-12**: All PHP files start with `declare(strict_types=1);`

---

## Developer Notes

- **Idempotence**: Payment processing must detect and reuse existing transaction IDs
- **Error Handling**: Log exceptions and errors in `logs/payment.log`
- **Precision**: Always store money in integer cents/pence

---
