<?php 
  class Carousel {
    private $conn;
    private $table = 'carousel';

    public function __construct($db) {
      $this->conn = $db;
    }

    public function addCarousel($title, $content, $img) {
      $query = 'INSERT INTO '. $this->table . ' 
      SET title = :title,
      content = :content,
      img = :img';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':title', $title);
      $stmt->bindParam(':content', $content);
      $stmt->bindParam(':img', $img);
      if ($stmt->execute()) {
        $id = $this->conn->lastInsertId();
        return $id;
      } else {
      printf("Error: %s.\n", $stmt->error);
      return false;
      }
    }

    public function updateCarousel($id, $title, $content, $img) {
      $checkImg = '';

      if (strlen($img) > 0) {
        global $checkImg;
        $checkImg = ', img = :img';
      }

      $query = 'UPDATE ' . $this->table . '
                  SET  title = :title, 
                  content = :content'
                . $checkImg .' 
                  WHERE id = :id';
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    
    if (strlen($img) > 0) {
      $stmt->bindParam(':img', $img);
    }

    if ($stmt->execute()) {
      return true;
    }
    printf("Error: %s.\n", $stmt->error);

    return false;
    }

    public function getCarousel() {
      $query = 'SELECT * FROM '. $this->table; 
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      return $stmt;
    }

    public function deleteCarousel($id) {
      $query = 'DELETE FROM '. $this->table . ' WHERE id = ?'; 
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $id);
      if($stmt->execute()) {
        return true;
      } else {
        printf("Error: %s.\n", $stmt->error);
        return false;
      }
    }
  }