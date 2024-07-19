<?php

use \Firebase\JWT\JWT;

if (!function_exists('getJWTForUser')) {
    function getJWTForUser($user, $secretKey)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 999999999999; 
        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
            ]
        ];

        return JWT::encode($payload, $secretKey, 'HS256');
    }
}

if (!function_exists('validateJWTFromRequest')) {
    function validateJWTFromRequest($secretKey)
    {
        $header = getAuthorizationHeader();
        if ($header == null) {
            return false;
        }

        $token = str_replace('Bearer ', '', $header);
        try {
            $decoded = JWT::decode($token, $secretKey);
            return (array) $decoded;
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('getAuthorizationHeader')) {
    function getAuthorizationHeader()
    {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of the fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
}
