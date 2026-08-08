<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SecurityHelper
{
    /**
     * Generate secure random token
     */
    public static function generateToken($length = 64): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Hash sensitive data
     */
    public static function hash($data): string
    {
        return Hash::make($data);
    }

    /**
     * Verify hash
     */
    public static function verify($data, $hash): bool
    {
        return Hash::check($data, $hash);
    }

    /**
     * Validate password strength
     */
    public static function validatePasswordStrength($password): array
    {
        $score = 0;
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        } else {
            $score++;
        }

        if (preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) {
            $score++;
        } else {
            $errors[] = 'Password must contain both uppercase and lowercase letters.';
        }

        if (preg_match('/[0-9]/', $password)) {
            $score++;
        } else {
            $errors[] = 'Password must contain at least one number.';
        }

        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            $score++;
        } else {
            $errors[] = 'Password must contain at least one special character.';
        }

        // Check against common passwords
        $commonPasswords = ['password', '123456', 'qwerty', 'admin', 'letmein'];
        if (in_array(strtolower($password), $commonPasswords)) {
            $errors[] = 'Password is too common. Please choose a more secure password.';
            $score = 0;
        }

        $strength = match ($score) {
            0, 1 => 'weak',
            2 => 'medium',
            3, 4 => 'strong',
            default => 'weak',
        };

        return [
            'strength' => $strength,
            'score' => $score,
            'errors' => $errors,
            'valid' => empty($errors),
        ];
    }

    /**
     * Sanitize input
     */
    public static function sanitize($input)
    {
        if (is_string($input)) {
            return strip_tags(trim($input));
        }
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return $input;
    }

    /**
     * Escape output
     */
    public static function escape($output)
    {
        if (is_string($output)) {
            return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
        }
        if (is_array($output)) {
            return array_map([self::class, 'escape'], $output);
        }
        return $output;
    }

    /**
     * Generate secure CSRF token
     */
    public static function csrfToken(): string
    {
        return Str::random(40);
    }

    /**
     * Rate limit key generation
     */
    public static function rateLimitKey(string $action, $identifier): string
    {
        return "rate_limit:{$action}:{$identifier}";
    }

    /**
     * Check if IP is blacklisted
     */
    public static function isIpBlacklisted($ip): bool
    {
        $blacklist = SettingsService::get('ip_blacklist', '');
        if (empty($blacklist)) {
            return false;
        }

        $ips = explode(',', $blacklist);
        return in_array($ip, array_map('trim', $ips));
    }

    /**
     * Log security event
     */
    public static function logSecurityEvent($event, $data = [])
    {
        \Log::channel('security')->warning($event, array_merge($data, [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toDateTimeString(),
        ]));
    }
}