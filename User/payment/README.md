# VeroSports Payment System

This directory contains the payment processing system for VeroSports e-commerce platform.

## Payment Flow

1. **Checkout (checkout.php)**
   - User reviews order details and shipping information
   - Creates an order in the database
   - Redirects to payment methods selection

2. **Payment Methods (payment_methods.php)**
   - User selects payment method (currently only credit card)
   - Redirects to process payment

3. **Process Payment (process_payment.php)**
   - Handles credit card payment processing using Stripe
   - Records payment attempts in the database
   - Redirects to success page on successful payment

4. **Webhooks (webhook.php)**
   - Receives payment notifications from Stripe
   - Updates order and payment status asynchronously
   - Handles events like payment success, failure, and refunds

## Database Structure

The payment system uses the following database tables:
- `orders`: Stores order information
- `order_items`: Stores items in each order
- `payment`: Records payment attempts and status
- `payment_log`: Detailed logs of payment operations

## Security Measures

- All payment forms include CSRF tokens
- Amounts are stored in cents/pence
- Payment operations are idempotent
- All sensitive operations are logged
- Successful and failed payments are recorded
- XSS protection using htmlspecialchars()

## File Structure

- `db.php`: Database connection and utility methods
- `checkout.php`: Order creation and checkout page
- `payment_methods.php`: Payment method selection
- `process_payment.php`: Credit card payment processing
- `charge.php`: Direct Stripe payment API integration
- `webhook.php`: Stripe webhook handler
- `success.php`: Payment success page
- `cancel.php`: Payment cancellation page
- `secrets.php`: API keys (not committed to version control)

## Logging

Payment logs are stored in:
- Database: `payment_log` table
- File system: `logs/` directory

## Important Notes

- All amounts are stored in cents/pence for precision
- Payment operations are designed to achieve idempotence
- Sensitive operations record audit logs in the payment_log table 