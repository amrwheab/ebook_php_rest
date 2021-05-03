<?php
  class User {
    private $conn;
    private $table = 'user';

    public function __construct($db) {
      $this->conn = $db;
    }

    public function checkSlug($slug) {
      $query = 'SELECT * FROM ' . $this->table . ' WHERE slug = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $slug);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        return true;
      } else {
        return false;
      }
    }

    public function getUserByEmail($email) {
      $query = 'SELECT * FROM ' . $this->table . ' WHERE email = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $email);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        return $row;
      } else {
        return '';
      }
    }

    public function getUserById($id) {
      $query = 'SELECT id, name, email, address, slug, isAdmin, mainAdmin FROM ' . $this->table . ' WHERE id = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $id);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        return $row;
      } else {
        return '';
      }
    }

    public function addUser($name, $email, $password, $address, $slug) {
      $query = 'INSERT INTO ' . $this->table . ' 
                  SET name = :name,
                  email = :email,
                  password = :password,
                  address = :address,
                  slug = :slug
                  ';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':name', $name);
      $stmt->bindParam(':email', $email);
      $stmt->bindParam(':password', $password);
      $stmt->bindParam(':address', $address);
      $stmt->bindParam(':slug', $slug);

      if ($stmt->execute()) {
        return true;
      } else {
        printf("Error: %s.\n", $stmt->error);
        return false;
      }
    }
  }