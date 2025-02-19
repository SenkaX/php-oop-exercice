<?php

namespace Root\Html;

use PDO;

class User {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }

    public function getUserById(int $id): array {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmail(string $email): array {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser(string $name, string $email, string $password): bool {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['name' => $name, 'email' => $email, 'password' => $hashedPassword]);
    }

    public function updateUser(int $id, string $name, string $email, ?string $password = null): bool {
        $sql = "UPDATE users SET name = :name, email = :email";
        $params = ['name' => $name, 'email' => $email, 'id' => $id];

        if ($password) {
            $sql .= ", password = :password";
            $params['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $sql .= " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}