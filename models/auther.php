<?php
  class Auther {
    private $conn;
    private $table = 'auther';

    public $id;
    public $name;
    public $imgUrl;
    public $info;
    public $slug;

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

    public function getAuthers($page) {
      $limit = 8;
      $total_skip = ((int)$page-1)*8;

      $query = 'SELECT * FROM '. $this->table .' 
                  LIMIT '.$total_skip. ', ' .$limit; 
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt;
    }

    public function getAuthersNames() {
      $query = 'SELECT id, name FROM '. $this->table . ' 
                  LIMIT 10';
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt;
    }

    public function getOneAuther($id) {
      $query = 'SELECT * FROM '. $this->table . ' 
                  WHERE id = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $id);
      $stmt->execute();
      return $stmt;
    }

    public function addAuther() {
      $query = 'INSERT INTO '. $this->table . ' 
                  SET name = :name,
                  imgUrl = :imgUrl,
                  info = :info,
                  slug = :slug';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':name', $this->name);
      $stmt->bindParam(':imgUrl', $this->imgUrl);
      $stmt->bindParam(':info', $this->info);
      $stmt->bindParam(':slug', $this->slug);
      if ($stmt->execute()) {
        $id = $this->conn->lastInsertId();
        return $id;
      } else {
        printf("Error: %s.\n", $stmt->error);
        return false;
      }
    }

    public function updateAuther($id) {
      $checkImg = '';

      if (strlen($this->imgUrl) > 0) {
        global $checkImg;
        $checkImg = ', imgUrl = :imgUrl';
      }

      $query = 'UPDATE ' . $this->table . '
                  SET  name = :name, 
                  info = :info'
                . $checkImg .' 
                  WHERE id = :id';
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $this->name);
    $stmt->bindParam(':info', $this->info);
    
    if (strlen($this->imgUrl) > 0) {
      $stmt->bindParam(':imgUrl', $this->imgUrl);
    }

    if ($stmt->execute()) {
      return true;
    }
    printf("Error: %s.\n", $stmt->error);

    return false;
    }

  }