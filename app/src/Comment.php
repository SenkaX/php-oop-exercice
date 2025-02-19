<?php

namespace Root\Html;

use PDO;

class Comment {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db->getConnection();
    }

    public function getCommentsByPostId(int $postId): array {
        $sql = "SELECT comments.*, users.name as user_name, users.id as user_id 
                FROM comments 
                INNER JOIN users ON comments.user_id = users.id 
                WHERE post_id = :post_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['post_id' => $postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createComment(string $content, int $postId, int $userId): bool {
        $sql = 'INSERT INTO comments (content, post_id, user_id) VALUES (:content, :post_id, :user_id)';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['content' => $content, 'post_id' => $postId, 'user_id' => $userId]);
    }
}