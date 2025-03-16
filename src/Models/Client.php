<?php
namespace Models;
use Database\Database;
use PDO;
use PDOException;

class Client {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($name, $clientId, $clientSecret, $description) {
        // try {
        //     $query = "INSERT INTO TB_CLIENTS (name, client_id, client_secret, description) VALUES (:name, :client_id, :client_secret, :description)";
        //     $stmt = $this->db->prepare($query);
        //     $stmt->execute([
        //         ':name' => $name,
        //         ':client_id' => $clientId,
        //         ':client_secret' => $clientSecret,
        //         ':description' => $description
        //     ]);
        //     return $this->db->lastInsertId();
        // } catch (PDOException $e) {
        //    // throw new PDOException("Create failed: " . $e->getMessage()); remover depois de habiltiar o PDOException 

        // }
     
            $stmt = $this->db->prepare("INSERT INTO TB_CLIENTS (name, client_id, client_secret, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $clientId, $clientSecret, $description]);
            return $this->db->lastInsertId();
        }
    

  

    public function getAll() {
        try {
            $query = "SELECT * FROM TB_CLIENTS ORDER BY created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new PDOException("Fetch failed: " . $e->getMessage());
        }
    }
    public function getById($id) {
        // try {
        //     $query = "SELECT * FROM TB_CLIENTS WHERE id = :id";
        //     $stmt = $this->db->prepare($query);
        //     $stmt->execute([':id' => $id]);
        //     return $stmt->fetch();
        // } catch (PDOException $e) {
        //     throw new PDOException("Fetch failed: " . $e->getMessage());
        // } pode ocorrer de trazer diferente entao cria abaixo que rtas exato
        $stmt = $this->db->prepare("SELECT id, name, client_id, client_secret, description, created_at, updated_at FROM TB_CLIENTS WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByClientId($clientId) {
        try {
            $query = "SELECT * FROM TB_CLIENTS WHERE client_id = :client_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':client_id' => $clientId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new PDOException("Fetch failed: " . $e->getMessage());
        }
    }

    public function update($id, $name, $clientId, $clientSecret) {
        try {
            $query = "UPDATE TB_CLIENTS SET name = :name, client_id = :client_id, client_secret = :client_secret WHERE id = :id";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                ':id' => $id,
                ':name' => $name,
                ':client_id' => $clientId,
                ':client_secret' => $clientSecret
            ]);
        } catch (PDOException $e) {
            throw new PDOException("Update failed: " . $e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $query = "DELETE FROM TB_CLIENTS WHERE id = :id";
            $stmt = $this->db->prepare($query);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            throw new PDOException("Delete failed: " . $e->getMessage());
        }
    }
}