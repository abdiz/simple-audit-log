<?php


namespace AbdiZbn\SimpleAuditLog;


class ClientData
{
    /**
     * Get the real ip of the client.
     *
     * @return string|null
     */
    static function real_ip()
    {
        $httpHeaderKeys = [
            'X-Real-IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        ];

        foreach ($httpHeaderKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);

                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Return user agent from request
     *
     *
     * @return mixed
     */
    static function user_agent()
    {
        if (array_key_exists('HTTP_USER_AGENT', $_SERVER)) {
            return $_SERVER['HTTP_USER_AGENT'];
        }

        return null;
    }
}
