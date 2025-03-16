<?php

namespace Controllers;

use Models\Client;
use Utils\JWTUtil;
use Utils\UUIDGenerator;
use Exception;
use PDOException; // Adicione isso para validar duplicaçõe de nome da aplicação por exemplo

class ClientController {
    private $client;

    public function __construct() {
        $this->client = new Client();
    }

    public function index() {
    //     try {
    //         $clients = $this->client->getAll();
    //         return ['status' => 'success', 'data' => $clients];
    //     } catch (Exception $e) {
    //         return ['data' => null, 'mensagens' => [$e->getMessage()]];
    //     }
   // // } sem data emensagens
    //try {
    //     $clients = $this->client->getAll();
    //     return ['data' => $clients, 'mensagens' => ['Clients recuperados com sucesso.']];
    // } catch (Exception $e) {
    //     http_response_code(500); // Erro interno do servidor
    //     return ['data' => null, 'mensagens' => ['Erro ao listar clients: ' . $e->getMessage()]];
    // }
    $clients = $this->client->getAll();
    $message = empty($clients) ? 'Nenhum cliente encontrado' : 'Clients recuperados com sucesso';
    return ['data' => $clients, 'mensagens' => [$message]];
}



  



public function getById($id) {
    try {
        $client = $this->client->getById($id);
        if (!$client) {
            http_response_code(404);
            return ['data' => null, 'mensagens' => ['Client não encontrado.']];
        }
        return ['data' => $client, 'mensagens' => ['Client recuperado com sucesso.']];
    } catch (Exception $e) {
        http_response_code(500); // Erro interno, não 400 (400 é pra entrada inválida)
        return ['data' => null, 'mensagens' => ['Erro ao buscar client: ' . $e->getMessage()]];
    }
}
    //public function store($data) {
    //     try {
    //         error_log("Dados recebidos no store: " . print_r($data, true)); // Debug no log
    //         if (!is_array($data) || empty($data['name']) || empty($data['client_id']) || empty($data['client_secret'])) {
    //             http_response_code(400);
    //             return ['data' => null, 'mensagens' => ['Os campos name, client_id e client_secret são obrigatórios.']];
    //         }
    
    //         $id = $this->client->create(
    //             $data['name'],
    //             $data['client_id'],
    //             $data['client_secret'],
    //             $data['description'] ?? ''
    //         );
    
    //         $client = $this->client->getById($id);
    //         http_response_code(201);
    //         return ['data' => $client, 'mensagens' => ['Client criado com sucesso.']];
    //     } catch (Exception $e) {
    //         http_response_code(400);
    //         return ['data' => null, 'mensagens' => [$e->getMessage()]];
    //     }
    // }

    //antes de gerar o uuiid
    public function store($data) {
    //     try {
    //         error_log("Dados recebidos no store: " . print_r($data, true));
    //         if (!is_array($data) || empty($data['name']) || empty($data['client_id']) || empty($data['client_secret'])) {
    //             http_response_code(400);
    //             return ['data' => null, 'mensagens' => ['Os campos name, client_id e client_secret são obrigatórios']];
    //         }
    
    //         $id = $this->client->create(
    //             $data['name'],
    //             $data['client_id'],
    //             $data['client_secret'],
    //             $data['description'] ?? ''
    //         );
    
    //         $client = $this->client->getById($id);
    //         http_response_code(201);
    //         return ['data' => $client, 'mensagens' => ['Client criado com sucesso']];
    //     } catch (Exception $e) {
    //         http_response_code(500); // Erro interno (ex.: falha no banco)
    //         return ['data' => null, 'mensagens' => ['Erro ao criar cliente: ' . $e->getMessage()]];
    //     }
    // }
    
        try {
            // error_log("Dados recebidos no store: " . print_r($data, true));
            // if (!is_array($data) || empty($data['name']) || empty($data['client_id'])) {
            //     http_response_code(400);
            //     return ['data' => null, 'mensagens' => ['Os campos name e client_id são obrigatórios.']];
            // } validação do name abaixo
    
            error_log("Dados recebidos no store: " . print_r($data, true));
                if (!is_array($data) || empty(trim($data['name'])) || empty(trim($data['client_id']))) {
                 http_response_code(400);
                 return ['data' => null, 'mensagens' => ['Os campos name e client_id são obrigatórios e não podem conter apenas espaços.']];
        }
//abaixo criado para validar espaoes em branco do name
        $name = trim($data['name']); // Limpa e armazena o valor
        $clientId = trim($data['client_id']); // Limpa e armazena o valor
        
        if ($name !== $data['name'] || $clientId !== $data['client_id']) {
            http_response_code(400);
            return ['data' => null, 'mensagens' => ['Os campos name e client_id não podem ter espaços extras no início ou fim.']];
        }


            // Gera client_secret como UUID se não for enviado só gerao UUID se comentar ou nao enviar o client_secret
            $clientSecret = isset($data['client_secret']) && !empty(trim($data['client_secret'])) 
                ? $data['client_secret'] 
                : UUIDGenerator::generateV4();
                
                
                error_log("Criando cliente: name=$name, client_id=$clientId, client_secret=$clientSecret, description=" . ($data['description'] ?? ''));

            // $id = $this->client->create(
            //     trim($data['name']), // Remove espaços do name antes de salvar
            //     $data['name'],
            //     $data['client_id'],
            //     $clientSecret,
            //     $data['description'] ?? ''
            // ); estava enviando campos trocados
            $id = $this->client->create(
                $name,          // 1º: name
                $clientId,      // 2º: client_id
                $clientSecret,  // 3º: client_secret
                $data['description'] ?? '' // 4º: description
            );
    
            $client = $this->client->getById($id); /// aqui é o retorno no postamn do envio do POST legal

            http_response_code(201);


            return ['data' => $client, 'mensagens' => ['Client criado com sucesso.']];
        } catch (PDOException $e) {
            error_log("PDOException capturado: " . $e->getMessage());
            if ($e->getCode() === '23000' && strpos($e->getMessage(), 'Duplicate entry') !== false) {
                if (strpos($e->getMessage(), "'client_id'") !== false) {
                    http_response_code(409);
                    return ['data' => null, 'mensagens' => ["O client_id '{$data['client_id']}' já está registrado."]];
                } elseif (strpos($e->getMessage(), "'name'") !== false) {
                    http_response_code(409);
                    return ['data' => null, 'mensagens' => ["O nome '{$data['name']}' já está cadastrado com outro ID client."]];
                }
            }
            http_response_code(500);
            return ['data' => null, 'mensagens' => ['Erro no banco ao criar client: ' . $e->getMessage()]];
        } catch (Exception $e) {
            error_log("Exception genérica capturada: " . $e->getMessage());
            http_response_code(500);
            return ['data' => null, 'mensagens' => ['Erro ao criar client: ' . $e->getMessage()]];
        }
    }


   public function update($id, $data) {
    try {
        if (!is_array($data) || !isset($data['name']) || $data['name'] === '' || !isset($data['client_id']) || $data['client_id'] === '' || !isset($data['client_secret']) || $data['client_secret'] === '') {
            http_response_code(400);
            return ['data' => null, 'mensagens' => ['Todos os campos são obrigatórios']];
        }

        $success = $this->client->update(
            $id,
            $data['name'],
            $data['client_id'],
            $data['client_secret']
        );

        if (!$success) {
            http_response_code(404);
            return ['data' => null, 'mensagens' => ['Client not found']];
        }

        $updatedClient = $this->client->getById($id);
        http_response_code(200);
        return ['data' => $updatedClient, 'mensagens' => ['Client atualizado com sucesso']];
    } catch (Exception $e) {
        http_response_code(500);
        return ['data' => null, 'mensagens' => ['Erro ao atualizar cliente: ' . $e->getMessage()]];
    }
}
//     try {
//         if (!is_array($data) || !isset($data['name']) || $data['name'] === '' || !isset($data['client_id']) || $data['client_id'] === '' || !isset($data['client_secret']) || $data['client_secret'] === '') {
//             http_response_code(400);
//             throw new Exception('Todos os campos são obrigatórios.');
//         }

//         $success = $this->client->update(
//             $id,
//             $data['name'],
//             $data['client_id'],
//             $data['client_secret']
//         );

//         if (!$success) {
//             http_response_code(404);
//             throw new Exception('Client not found');
//         }

//         $updatedClient = $this->client->getById($id); // Pega o cliente atualizado
//         http_response_code(200);
//         return ['data' => $updatedClient, 'mensagens' => ['Client atualizado com sucesso']];
//     } catch (Exception $e) {
//         return ['data' => null, 'mensagens' => [$e->getMessage()]];
//     }
// }
//         http_response_code(200);
//         return ['status' => 'success', 'message' => 'Client atualizado com sucesso.'];
//     } catch (Exception $e) {
//         return ['data' => null, 'mensagens' => [$e->getMessage()]];
//     }
// }


    // public function delete($id) {
    //     try {
    //         $success = $this->client->delete($id);
    //         if (!$success) {
    //             throw new Exception('Client not found');
    //         }

    //         return ['status' => 'success', 'message' => 'Client deleted successfully'];
    //     } catch (Exception $e) {
    //         return ['data' => null, 'mensagens' => [$e->getMessage()]];
    //     }
    // }acima salva mesmo nem existinfo
    public function delete($id) {
        try {
            $client = $this->client->getById($id);
            if (!$client) {
                http_response_code(404);
                return ['data' => null, 'mensagens' => ['Client não encontrado.']];
            }
            $this->client->delete($id);
            return ['data' => null, 'mensagens' => ['Client deletado com sucesso.']];
        } catch (Exception $e) {
            http_response_code(500);
            return ['data' => null, 'mensagens' => ['Erro ao deletar client: ' . $e->getMessage()]];
        }
    }

    public function generateToken($data) {
        try {
            if ($data === null || !is_array($data)) {
                http_response_code(400);
                return ['data' => null, 'mensagens' => ['Nenhum dado enviado no corpo da requisição']];
            }
    
            if (!isset($data['client_id']) || empty(trim($data['client_id'])) || !isset($data['client_secret']) || empty(trim($data['client_secret']))) {
                http_response_code(400);
                return ['data' => null, 'mensagens' => ['Client ID e Secret são obrigatórios e não podem estar vazios']];
            }
    
            $client = $this->client->getByClientId($data['client_id']);
            if (!$client || $client['client_secret'] !== $data['client_secret']) {
                http_response_code(401);
                return ['data' => null, 'mensagens' => ['Credenciais inválidas']];
            }
    
            $token = Utils\JWTUtil::generateToken($client);
            http_response_code(200);
            return ['data' => ['token' => $token], 'mensagens' => ['Token gerado com sucesso']];
        } catch (Exception $e) {
            http_response_code(500);
            return ['data' => null, 'mensagens' => ['Erro ao gerar token: ' . $e->getMessage()]];
        }
    }

    public function validateToken($data) {
        try {
            if (empty($data['token'])) {
                throw new Exception('Token is required');
            }
    
            $result = JWTUtil::validateToken($data['token']);
            return ['status' => 'success', 'data' => $result];
        } catch (Exception $e) {
            return ['data' => null, 'mensagens' => [$e->getMessage()]];
        }
    }
}