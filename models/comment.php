<?php
  class Comment {
    private $conn;
    private $table = 'comment';

    public function __construct($db) {
      $this->conn = $db;
    }

    public function getComments($bookId, $page) {
      $limit = 4;
      $skip = $limit * ((int)$page-1);
      $query = 'SELECT u.name as user_name, c.id as comment_id, body
                FROM '. $this->table .' c 
                LEFT JOIN user u ON u.id = c.userId
                WHERE bookId = ?
                ORDER BY c.id DESC
                LIMIT '.$skip.', '.$limit
                ;
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $bookId);
      $stmt->execute();
      return $stmt;
    }

    public function addComment($bookId, $userId, $body) {
      $query = 'INSERT INTO '.$this->table.' SET userId = ?, bookId = ?, body = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $userId);
      $stmt->bindParam(2, $bookId);
      $stmt->bindParam(3, $body);
      if($stmt->execute()) {
        return true;
      } else {
        return false;
      }
    }
  }