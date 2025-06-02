<?php
declare(strict_types=1);

/**
 * IP Geolocation Service
 * 
 * This service uses free IP geolocation APIs to determine geographic location information
 * from IP addresses.
 */
class IPGeolocator {
    /**
     * Cache duration in seconds (1 day)
     */
    private const CACHE_DURATION = 86400;
    
    /**
     * Memory cache to avoid multiple API calls for the same IP within the same request
     */
    private static $memoryCache = [];
    
    /**
     * Get location information from an IP address
     * 
     * @param string $ip IP address to geolocate
     * @return array Location information
     */
    public static function getLocation(string $ip): array {
        // Default values if geolocation fails
        $location = [
            'country' => 'Unknown',
            'city' => 'Unknown',
            'region' => 'Unknown',
            'country_code' => '',
            'latitude' => 0,
            'longitude' => 0
        ];
        
        // Return default values for localhost or private IPs
        if (self::isPrivateIP($ip) || $ip === 'Unknown') {
            $location['country'] = 'Local Network';
            $location['city'] = 'Local';
            return $location;
        }
        
        // Check memory cache first
        if (isset(self::$memoryCache[$ip])) {
            return self::$memoryCache[$ip];
        }
        
        // Check file cache
        $cacheResult = self::getFromCache($ip);
        if ($cacheResult !== null) {
            self::$memoryCache[$ip] = $cacheResult;
            return $cacheResult;
        }
        
        // Try multiple geolocation APIs in case one fails
        $location = self::tryIPAPI($ip) ?? 
                   self::tryIPInfoDB($ip) ?? 
                   self::tryGeoPlugin($ip) ?? 
                   $location;
        
        // Cache the result
        if ($location['country'] !== 'Unknown') {
            self::saveToCache($ip, $location);
            self::$memoryCache[$ip] = $location;
        }
        
        return $location;
    }
    
    /**
     * Check if an IP address is private/local
     * 
     * @param string $ip IP address to check
     * @return bool True if the IP is private
     */
    private static function isPrivateIP(string $ip): bool {
        // Check if IP is valid
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return true;
        }
        
        // Check for localhost or private IP ranges
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }
    
    /**
     * Try to get location from ip-api.com (no API key required)
     * 
     * @param string $ip IP address to locate
     * @return array|null Location data or null if failed
     */
    private static function tryIPAPI(string $ip): ?array {
        try {
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,countryCode,regionName,city,lat,lon");
            
            if ($response === false) {
                return null;
            }
            
            $data = json_decode($response, true);
            
            if (!$data || $data['status'] !== 'success') {
                return null;
            }
            
            return [
                'country' => $data['country'] ?? 'Unknown',
                'country_code' => $data['countryCode'] ?? '',
                'region' => $data['regionName'] ?? 'Unknown',
                'city' => $data['city'] ?? 'Unknown',
                'latitude' => $data['lat'] ?? 0,
                'longitude' => $data['lon'] ?? 0
            ];
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Try to get location from ipinfodb.com
     * Requires an API key if you want to use this service
     * 
     * @param string $ip IP address to locate
     * @return array|null Location data or null if failed
     */
    private static function tryIPInfoDB(string $ip): ?array {
        // You would need to register for an API key at https://ipinfodb.com/
        // $api_key = 'YOUR_API_KEY';
        
        // Since this is a fallback, we'll return null for now
        // If you register for an API key, you can implement this
        return null;
    }
    
    /**
     * Try to get location from geoplugin.net (no API key required)
     * 
     * @param string $ip IP address to locate
     * @return array|null Location data or null if failed
     */
    private static function tryGeoPlugin(string $ip): ?array {
        try {
            $response = @file_get_contents("http://www.geoplugin.net/json.gp?ip={$ip}");
            
            if ($response === false) {
                return null;
            }
            
            $data = json_decode($response, true);
            
            if (!$data || $data['geoplugin_status'] !== 200) {
                return null;
            }
            
            return [
                'country' => $data['geoplugin_countryName'] ?? 'Unknown',
                'country_code' => $data['geoplugin_countryCode'] ?? '',
                'region' => $data['geoplugin_regionName'] ?? 'Unknown',
                'city' => $data['geoplugin_city'] ?? 'Unknown',
                'latitude' => (float)($data['geoplugin_latitude'] ?? 0),
                'longitude' => (float)($data['geoplugin_longitude'] ?? 0)
            ];
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Get location data from cache if available and not expired
     * 
     * @param string $ip IP address
     * @return array|null Location data or null if not cached or expired
     */
    private static function getFromCache(string $ip): ?array {
        $cacheDir = __DIR__ . '/../logs/ip_cache';
        $cacheFile = $cacheDir . '/' . md5($ip) . '.json';
        
        if (!file_exists($cacheFile)) {
            return null;
        }
        
        // Check if cache is expired
        if (filemtime($cacheFile) < time() - self::CACHE_DURATION) {
            return null;
        }
        
        $data = @file_get_contents($cacheFile);
        if ($data === false) {
            return null;
        }
        
        $location = json_decode($data, true);
        return is_array($location) ? $location : null;
    }
    
    /**
     * Save location data to cache
     * 
     * @param string $ip IP address
     * @param array $location Location data
     * @return bool Success status
     */
    private static function saveToCache(string $ip, array $location): bool {
        $cacheDir = __DIR__ . '/../logs/ip_cache';
        
        // Create cache directory if it doesn't exist
        if (!is_dir($cacheDir)) {
            if (!mkdir($cacheDir, 0755, true)) {
                return false;
            }
            
            // Create .htaccess to prevent direct access
            @file_put_contents(
                $cacheDir . '/.htaccess',
                "Order deny,allow\nDeny from all"
            );
        }
        
        $cacheFile = $cacheDir . '/' . md5($ip) . '.json';
        return (bool)@file_put_contents($cacheFile, json_encode($location));
    }
    
    /**
     * Format location as a readable string
     * 
     * @param array $location Location data
     * @return string Formatted location string
     */
    public static function formatLocation(array $location): string {
        $parts = [];
        
        if (!empty($location['city']) && $location['city'] !== 'Unknown') {
            $parts[] = $location['city'];
        }
        
        if (!empty($location['region']) && $location['region'] !== 'Unknown' && $location['region'] !== $location['city']) {
            $parts[] = $location['region'];
        }
        
        if (!empty($location['country']) && $location['country'] !== 'Unknown') {
            $parts[] = $location['country'];
        }
        
        return !empty($parts) ? implode(', ', $parts) : 'Unknown Location';
    }
} 