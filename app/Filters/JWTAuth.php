<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Exception;
use Config\Services;
use App\Services\UserService;
use Firebase\JWT\Key;

class JWTAuth implements FilterInterface
{
    private $secretKey;

    public function __construct()
    {
        $this->secretKey = getenv('JWT_SECRET_KEY'); // Set this in your .env file
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getServer('HTTP_AUTHORIZATION');
        if (!$header) {
            return Services::response()
                ->setJSON(['status' => 401, 'error' => true, 'messages' => 'Access denied'])
                ->setStatusCode(401);
        }

        $token = str_replace('Bearer ', '', $header);

        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            $userService = new UserService();
            $userService->setUser((array) $decoded); // Store user data in the service
            // Optionally, store user service in a global service container
            //Services::injector()->set(UserService::class, $userService);
        } catch (Exception $e) {
            return Services::response()
                ->setJSON(['status' => 401, 'error' => true, 'messages' => 'Invalid token'])
                ->setStatusCode(401);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing here
    }
}
