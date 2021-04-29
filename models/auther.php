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

    public function getAuthers($page, $search) {

      $searchQuery = '';
      if ($search) {
        global $searchQuery;
        $searchQuery = 'WHERE name LIKE ? ';
      }

      $limit = 8;
      $total_skip = ((int)$page-1)*8;

      $query = 'SELECT * FROM '. $this->table .' 
                  '. $searchQuery . '
                  LIMIT '.$total_skip. ', ' .$limit; 
      $stmt = $this->conn->prepare($query);

      if ($searchQuery) {
        $stmt->bindValue(1, "%$search%");
      }  

      $stmt->execute();
      $nRows = $this->conn->query('SELECT count(*) from '. $this->table)->fetchColumn(); 
      return array(
        'stmt' => $stmt,
        'num' => $nRows
      );
    }

    public function getAuthersNames() {
      $query = 'SELECT id, name, slug FROM '. $this->table . ' 
                  LIMIT 10';
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt;
    }

    public function getOneAuther($slug) {
      $query = 'SELECT * FROM '. $this->table . ' 
                  WHERE slug = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $slug);
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

    public function deleteAuther($id) {
      $query = 'DELETE FROM '. $this->table . ' WHERE id = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $id);
      if ($stmt->execute()) {
        return true;
      } else {
        printf("Error: %s.\n", $stmt->error);
        return false;
      }
    }

  }