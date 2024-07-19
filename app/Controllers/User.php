<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\User as UserModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Database\Exceptions\DataException;
use Exception;
use \Firebase\JWT\JWT;


class User extends BaseController
{
    private $secretKey;

    public function __construct()
    {
        $this->secretKey = getenv('JWT_SECRET_KEY'); 
        helper('jwt_helper'); 
    }

    public function register()
    {
        try {
            $data = $this->request->getJSON(true);

            // Validation rules
            $rules = [
                'username' => 'required|min_length[3]|max_length[20]',
                'email'    => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]',
            ];

            if ($this->validate($rules)) {
                $model = new UserModel();
                $newData = [
                    'username' => $data['username'],
                    'email'    => $data['email'],
                    'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                ];
                $model->save($newData);

                $user = $model->where('email', $data['email'])->first();
                $jwt = getJWTForUser($user, $this->secretKey);
                return $this->response->setJSON([
                    'status' => 201,
                    'error' => false,
                    'messages' => 'User created successfully',
                    'token' => $jwt,
                ])->setStatusCode(201);
            } else {
                return $this->response->setJSON([
                    'status' => 400,
                    'error' => true,
                    'messages' => $this->validator->getErrors(),
                ])->setStatusCode(400);
            }
        } catch (Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'error' => true,
                'messages' => 'An unexpected error occurred: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    public function login()
    {
        try {
            $data = $this->request->getJSON(true);

            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required|min_length[6]',
            ];

            if ($this->validate($rules)) {
                $model = new UserModel();
                $user = $model->where('email', $data['email'])->first();

                if (!$user || !password_verify($data['password'], $user['password'])) {
                    return $this->response->setJSON([
                        'status' => 401,
                        'error' => true,
                        'messages' => 'Invalid login credentials',
                    ])->setStatusCode(401);
                }

                $jwt = getJWTForUser($user, $this->secretKey);

                return $this->response->setJSON([
                    'status' => 200,
                    'error' => false,
                    'messages' => 'User logged in successfully',
                    'token' => $jwt,
                ])->setStatusCode(200);
            } else {
                return $this->response->setJSON([
                    'status' => 400,
                    'error' => true,
                    'messages' => $this->validator->getErrors(),
                ])->setStatusCode(400);
            }
        } catch (Exception $e) {
            return $this->response->setJSON([
                'status' => 500,
                'error' => true,
                'messages' => 'An unexpected error occurred: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

}
