# Cross-Browser Authentication System

This authentication system allows users to stay logged in across different browsers and devices when the "Remember Me" option is checked during login.

## How It Works

1. When a user logs in with "Remember Me" checked:
   - A secure token is generated and stored in the database
   - The token is tied to the user ID, browser information, and IP address
   - The token is set as a cookie with a 30-day expiration

2. When a user doesn't check "Remember Me":
   - No token is created, only session-based authentication is used
   - When the browser is closed, the session ends and the user will need to log in again

3. Cross-browser login:
   - The system maintains separate tokens for each browser/device
   - Users can see all their active sessions and log out from any device
   - Tokens are automatically refreshed when used

## Files and Components

- `auth.php`: Main authentication class that handles login/logout
- `token.php`: Handles token generation, validation, and revocation
- `login.php`: Login form that implements the "Remember Me" functionality
- `manage_sessions.php`: Allows users to view and manage active login sessions

## Security Features

1. **Token Security**:
   - Tokens are generated using secure random bytes (high entropy)
   - Stored with HMAC protection
   - HttpOnly cookie settings prevent JavaScript access
   
2. **Session Security**:
   - Session fingerprinting to prevent session hijacking
   - Secure session regeneration after login
   
3. **Device Management**:
   - Users can log out remotely from any device
   - IP addresses and user agents are tracked
   - Login activity is logged

## Usage

To use the "Remember Me" functionality:

1. Check the "Remember Me" box when logging in to stay logged in across browser sessions
2. Use the "Manage Devices" link in the user dropdown to see and manage active sessions
3. Log out from specific devices or all other devices as needed

## Database Schema

The authentication system uses the `user_tokens` table:

```sql
CREATE TABLE `user_tokens` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `last_used_at` datetime DEFAULT NULL,
  `is_revoked` tinyint(1) NOT NULL DEFAULT 0,
  `user_agent` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`token_id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`),
  KEY `idx_token_validation` (`token`, `expires_at`, `is_revoked`),
  CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
)
``` 