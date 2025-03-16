<?php
namespace Utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTUtil {
    public static function generateToken($clientData) {
        $issuedAt = time();
        $expirationTime = $issuedAt + JWT_EXPIRATION;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'client_id' => $clientData['client_id'],
            'name' => $clientData['name']
        ];

        return JWT::encode($payload, JWT_SECRET, 'HS256');
    }

    
   // public static function validateToken($token) {
    //     try {
    //         $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
    //         return [
    //             'valid' => true,
    //             'data' => (array) $decoded
    //         ];
    //     } catch (Exception $e) {
    //         return [
    //             'valid' => false,
    //             'error' => $e->getMessage()
    //         ];
    //     }
    // }
    public static function validateToken($token) {
        try {
            if (!is_array($data) || empty($data['token'])) {
                http_response_code(400);
                return ['data' => null, 'mensagens' => ['O campo token é obrigatório']];
            }
    
            $result = Utils\JWTUtil::validateToken($data['token']);
            if (!$result['valid']) {
                http_response_code(401);
                return ['data' => null, 'mensagens' => ['Token inválido: ' . $result['error']]];
            }
    
            http_response_code(200);
            return [
                'data' => [
                    'valid' => true,
                    'token_data' => $result['data']
                ],
                'mensagens' => ['Token válido']
            ];
        } catch (Exception $e) {
            http_response_code(500);
            return ['data' => null, 'mensagens' => ['Erro ao validar token: ' . $e->getMessage()]];
        }
    }
}