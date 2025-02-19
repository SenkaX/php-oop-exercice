<?php

namespace Root\Html;

use PDO;
use PDOException;

class Post {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }

    public function getPostById(int $id): ?array {
        if ($id <= 0) {
            return null; 
        }

        try {
            $sql = "SELECT posts.*, users.name, users.id as user_id
                    FROM posts 
                    INNER JOIN users ON posts.user_id = users.id
                    WHERE posts.id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);

            return $post ?: null;
        } catch (PDOException $e) {
            error_log("Erreur SQL getPostById: " . $e->getMessage());
            return null;
        }
    }

    public function getPostsByUserId(int $userId): array {
        if ($userId <= 0) {
            return []; 
        }

        try {
            $sql = "SELECT posts.id, posts.title, posts.created_at
                    FROM posts 
                    WHERE posts.user_id = :user_id
                    ORDER BY posts.created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur SQL getPostsByUserId: " . $e->getMessage());
            return [];
        }
    }

    public function createPost(string $title, string $content, int $userId): bool {
        if (empty($title) || empty($content) || $userId <= 0) {
            return false; // Vérification de base avant d'insérer
        }

        try {
            $sql = "INSERT INTO posts (title, content, user_id) VALUES (:title, :content, :user_id)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['title' => $title, 'content' => $content, 'user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("Erreur SQL createPost: " . $e->getMessage());
            return false;
        }
    }
}
