# VeroSports Authentication System

A secure, modern authentication system that supports cross-browser/device login management and IP geolocation tracking.

## Features

### Core Authentication
- **Session Management**: Regular session-based authentication
- **Persistent Login**: "Remember Me" feature for extended authentication periods
- **Cross-Device Support**: Stays logged in across different browsers and devices

### Security
- **Token Security**:
  - High-entropy tokens generated with secure random bytes
  - HttpOnly cookies to prevent JavaScript access
  - HMAC protection for stored tokens
  
- **Session Protection**:
  - Session fingerprinting prevents hijacking
  - Secure session regeneration after login
  - CSRF token validation for sensitive actions
  
- **Activity Monitoring**:
  - IP address tracking with geolocation
  - User agent fingerprinting
  - Comprehensive logging of login activities

### Device Management
- View all active sessions across devices
- Identify current session with visual indicator
- Log out from specific devices remotely
- One-click option to log out from all devices except current one
- Real-time device and browser identification

### IP Geolocation
- Displays physical locations of login attempts
- Shows city, region, and country information
- Visual indicators with country flags
- Efficient caching system to minimize API calls
- Multiple fallback APIs for reliable location data

## How It Works

### Authentication Flow
1. **Regular Login**:
   - Session-based authentication
   - 24-hour token validity
   - Session ends when browser closes

2. **Remember Me Login**:
   - Secure token stored in database
   - Cookie set with extended expiration (30 days)
   - Device information linked to token

3. **Token Validation**:
   - Tokens automatically verified on each request
   - Invalid or expired tokens trigger re-authentication
   - Tokens refreshed when used

## Components

### Core Files
- `auth.php`: Main authentication class for login/logout
- `token.php`: Token generation, validation and revocation
- `ip_geolocator.php`: IP address location services

### User Interface
- `login.php`: Login form with Remember Me option
- `manage_sessions.php`: Device management interface
- `logout.php`: Session termination handler

## Database Schema

The system uses the `user_tokens` table:

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
  KEY `idx_token_validation` (`token`, `expires_at`, `is_revoked`)
)
```

## Usage

### For Users
1. Check "Remember Me" at login to stay authenticated across sessions
2. Visit "Manage Device" in account settings to see all active sessions
3. Log out from any suspicious devices
4. Use "Logout All Other Devices" for complete security

### For Developers
1. Use `Auth::requireAuth()` to protect routes
2. Use `Auth::login($user_id, $data, $remember)` for authentication
3. Token data accessible via `Auth::getActiveSessions()`
4. IP geolocation available via `IPGeolocator::getLocation($ip)` 