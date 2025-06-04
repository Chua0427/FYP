# VeroSports Authentication & Security System

A robust, secure authentication system for VeroSports e-commerce platform with multi-device login management, session protection, and comprehensive security features.

## 🔐 Core Authentication

| Feature | Description |
|---------|-------------|
| Session Management | Secure PHP session handling with fingerprinting and protection |
| Persistent Login | "Remember Me" functionality with secure token storage |
| Cross-Device Support | Seamless authentication across multiple browsers and devices |
| Token Rotation | Regular refreshing of authentication tokens to prevent theft |

## 🛡️ Security Layers

### Token Security
- High-entropy tokens generated using cryptographically secure random bytes
- HttpOnly and SameSite cookie attributes prevent XSS and CSRF attacks
- HMAC protection for token verification and integrity validation
- Automatic token expiration and refresh mechanisms

### Session Protection
- Browser fingerprinting to detect session hijacking attempts
- Secure session regeneration after authentication events
- CSRF token validation for all sensitive operations
- Strict Content-Security-Policy implementation

### Activity Monitoring
- IP address logging with geolocation data
- User-agent fingerprinting and analysis
- Comprehensive audit logs for security events
- Automatic suspicious activity detection

## 💻 Device Management

The system provides users with full visibility and control over their active sessions:

- Real-time monitoring of all active sessions across devices
- Clear identification of current device with visual indicators
- Ability to terminate specific sessions remotely
- One-click option to log out from all devices except current one
- Detailed information about each device (browser, OS, location)

## 🗺️ IP Geolocation

- Automatic tracking of login location data
- City, region, country and ISP information
- Visual indicators including country flags
- Efficient caching system to minimize API calls
- Multiple fallback geolocation services for reliability

## ⚙️ Technical Implementation

### Authentication Flow
1. **Standard Login:**
   - Session-based authentication with 24-hour validity
   - Browser fingerprinting and CSRF token generation
   - Secure session parameters with HttpOnly flags

2. **Remember Me Login:**
   - Secure random token stored with HMAC verification
   - Extended cookie expiration (30 days)
   - Device fingerprinting linked to token data

3. **Continuous Protection:**
   - Tokens automatically validated on each request
   - Expired or invalid tokens trigger re-authentication
   - Regular token rotation for extended sessions

### System Components

| Component | File | Purpose |
|-----------|------|---------|
| Auth Core | `auth.php` | Main authentication class for login/logout operations |
| Token Handler | `token.php` | Manages token lifecycle (creation, validation, revocation) |
| Session Helper | `session_helper.php` | Secure session handling with anti-hijacking measures |
| IP Geolocation | `ip_geolocator.php` | Location services with caching for login tracking |
| CSRF Protection | `csrf.php` | Token generation and validation for form security |

## 💾 Database Schema

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

## 📚 Usage Guidelines

### For Users
- Enable "Remember Me" during login for persistent authentication
- Visit account settings to manage active devices and sessions
- Review and terminate any suspicious login sessions
- Use "Logout All Other Devices" for immediate security response

### For Developers
```php
// Protect routes with authentication check
Auth::requireAuth();

// Authenticate a user (with optional remember me)
Auth::login($user_id, $userData, $rememberMe = false);

// Get all active sessions for management view
$sessions = Auth::getActiveSessions();

// Get location data for display
$location = IPGeolocator::getLocation($ipAddress);

// Generate and validate CSRF tokens
$token = SessionHelper::getCsrfToken();
$valid = SessionHelper::verifyCsrfToken($userToken);
```

## 🔒 Security Best Practices

The system implements multiple security best practices:
- Defense in depth with multiple security layers
- No password storage in cookies or session data
- Minimization of sensitive data exposure
- Secure defaults for all configuration options
- Regular token rotation to limit attack windows 