<?php
class Book {
  // DB stuff
  private $conn;
  private $table = 'book';

  // Post Properties
  public $id;
  public $name;
  public $imgUrl;
  public $info;
  public $price;
  public $department;
  public $buysNum;
  public $miniPath;
  public $fullPath;
  public $isFeatured;
  public $auther;
  public $slug;

  // Constructor with DB
  public function __construct($db) {
    $this->conn = $db;
  }

  // Get Posts
  public function getAllBooks() {
    // Create query
    $query = 'SELECT b.id as book_id, b.name as book_name, b.info as book_info, b.imgUrl as book_img,b.slug as book_slug,
                price, buysNum, miniPath, fullPath, isFeatured,
                a.id as auther_id, a.imgUrl as auther_img, a.info as auther_info, a.name as auther_name, a.slug as auther_slug,
                d.id as department_id, d.name as department_name
                        FROM ' . $this->table . ' b
                        LEFT JOIN
                          auther a ON b.auther = a.id
                        LEFT JOIN
                        department d ON b.department = d.id
                        ';

    // Prepare statement
    $stmt = $this->conn->prepare($query);

    // Execute query
    $stmt->execute();

    return $stmt;
  }

  public function getDepartedBooks($id) {
    $query = 'SELECT b.id as book_id, b.name as book_name, b.info as book_info, b.imgUrl as book_img, price
                        FROM ' . $this->table . ' b
                        WHERE
                          department = ?
                        ';

    // Prepare statement
    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(1, $id);

    // Execute query
    $stmt->execute();

    return $stmt;
  }

  public function getFeatBooks() {
    $query = 'SELECT b.id as book_id, b.name as book_name, b.info as book_info, b.imgUrl as book_img,b.slug as book_slug,
                price, buysNum, miniPath, fullPath,
                a.id as auther_id, a.imgUrl as auther_img, a.info as auther_info, a.name as auther_name, a.slug as auther_slug,
                d.id as department_id, d.name as department_name
                        FROM ' . $this->table . ' b
                        LEFT JOIN
                          auther a ON b.auther = a.id
                        LEFT JOIN
                        department d ON b.department = d.id
                WHERE
                  isFeatured = ?
                        ';

    // Prepare statement
    $stmt = $this->conn->prepare($query);
    $is = '1';
    $stmt->bindParam(1, $is);
    // Execute query
    $stmt->execute();

    return $stmt;
  }

  public function getOneBook($id) {
    $query = 'SELECT b.id as book_id, b.name as book_name, b.info as book_info, b.imgUrl as book_img,b.slug as book_slug,
                price, buysNum, miniPath, fullPath, isFeatured,
                d.id as department_id, d.name as department_name
                        FROM ' . $this->table . ' b
                        LEFT JOIN
                        department d ON b.department = d.id
                WHERE b.id = ?
                        ';

    // Prepare statement
    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(1, $id);

    // Execute query
    $stmt->execute();

    return $stmt;
  }

  public function getAutherBooks($auther, $page) {

    $limit = 50;
    $total_skip = ((int)$page-1)*50;
    
    $query = 'SELECT * FROM '. $this->table . ' 
                WHERE auther = ?
                LIMIT ' . $total_skip . ', ' . $limit;
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $auther);
    $stmt->execute();

    return $stmt;
  }

  public function getBooksCount() {
    $query = 'SELECT *
                  FROM ' . $this->table;

    // Prepare statement
    $stmt = $this->conn->prepare($query);

    // Execute query
    $stmt->execute();

    return $stmt;
  }

  public function addBook() {
    $query = 'INSERT INTO ' . $this->table . ' SET  name = :name, 
                                                      imgUrl = :imgUrl, 
                                                      info = :info, 
                                                      price = :price, 
                                                      department = :department, 
                                                      buysNum = :buysNum, 
                                                      miniPath = :miniPath, 
                                                      fullPath = :fullPath, 
                                                      isFeatured = :isFeatured, 
                                                      auther = :auther, 
                                                      slug = :slug';

    // Prepare statement
    $stmt = $this->conn->prepare($query);

    // Bind data
    $stmt->bindParam(':name', $this->name);
    $stmt->bindParam(':imgUrl', $this->imgUrl);
    $stmt->bindParam(':info', $this->info);
    $stmt->bindParam(':price', $this->price);
    $stmt->bindParam(':department', $this->department);
    $stmt->bindParam(':buysNum', $this->buysNum);
    $stmt->bindParam(':miniPath', $this->miniPath);
    $stmt->bindParam(':fullPath', $this->fullPath);
    $stmt->bindParam(':isFeatured', $this->isFeatured);
    $stmt->bindParam(':auther', $this->auther);
    $stmt->bindParam(':slug', $this->slug);

    // Execute query
    if ($stmt->execute()) {
      return true;
    }
    printf("Error: %s.\n", $stmt->error);

    return false;
  }

  public function updateBook($id) {
    $checkImg = '';
    $checkMini = '';
    $checkFull = '';

    if (strlen($this->imgUrl) > 0) {
      global $checkImg;
      $checkImg = 'imgUrl = :imgUrl, ';
    }
    if (strlen($this->miniPath) > 0) {
      global $checkMini;
      $checkMini = 'miniPath = :miniPath, ';
    }
    if (strlen($this->fullPath) > 0) {
      global $checkFull;
      $checkFull = 'fullPath = :fullPath, ';
    }

    $query = 'UPDATE ' . $this->table . '
                SET  name = :name, 
                info = :info, 
                price = :price, 
                department = :department, 
                buysNum = :buysNum,'
              . $checkImg .  
                $checkMini .  
                $checkFull . ' 
                isFeatured = :isFeatured, 
                auther = :auther
              WHERE id = :id';
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $this->name);
    $stmt->bindParam(':info', $this->info);
    $stmt->bindParam(':price', $this->price);
    $stmt->bindParam(':department', $this->department);
    $stmt->bindParam(':buysNum', $this->buysNum);
    $stmt->bindParam(':isFeatured', $this->isFeatured);
    $stmt->bindParam(':auther', $this->auther);
    
    if (strlen($this->imgUrl) > 0) {
      $stmt->bindParam(':imgUrl', $this->imgUrl);
    }
    if (strlen($this->miniPath) > 0) {
      $stmt->bindParam(':miniPath', $this->miniPath);
    }
    if (strlen($this->fullPath) > 0) {
      $stmt->bindParam(':fullPath', $this->fullPath);
    }

    if ($stmt->execute()) {
      return true;
    }
    printf("Error: %s.\n", $stmt->error);

    return false;
  }

  public function deleteBook($id) {
    $deleteCheck = true;
    for ($j = 0; $j < count($id); $j++) {
      $query = 'DELETE FROM ' . $this->table . ' WHERE id = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $id[$j]);
      if ($stmt->execute()) {
        global $deleteCheck;
        $deleteCheck = true;
      } else {
        printf("Error: %s.\n", $stmt->error);
        break;
        return false;
      }
    }
    return $deleteCheck;
  }
}
