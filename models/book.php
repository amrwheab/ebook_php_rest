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

  // Get Posts
  public function getAllBooks($page, $search) {
    $searchQuery = '';
    if ($search) {
      global $searchQuery;
      $searchQuery = 'WHERE b.name LIKE ? ';
    }

    $limit = 20;
    $total_skip = ((int)$page-1)*20;

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
                '. $searchQuery . '
                LIMIT ' . $total_skip . ', ' . $limit;

    // Prepare statement
    $stmt = $this->conn->prepare($query);

    if ($searchQuery) {
      $stmt->bindValue(1, "%$search%");
    }

    // Execute query
    $stmt->execute();

    return $stmt;
  }

  public function getDepartedBooks($id) {
    $query = 'SELECT b.id as book_id, b.name as book_name, b.info as book_info, b.imgUrl as book_img,b.slug as book_slug,
              price
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
                LIMIT 20
                        ';

    // Prepare statement
    $stmt = $this->conn->prepare($query);
    $is = '1';
    $stmt->bindParam(1, $is);
    // Execute query
    $stmt->execute();

    return $stmt;
  }

  public function getByDepartName($name, $page) {
    $limit = 20;
    $total_skip = ((int)$page-1)*20;
    $query = 'SELECT b.id as book_id, b.name as book_name, b.info as book_info, b.imgUrl as book_img,b.slug as book_slug,
                price,
                d.id as department_id, d.name as department_name
                        FROM ' . $this->table . ' b
                        LEFT JOIN
                        department d ON b.department = d.id
                WHERE
                  d.name = ?
                LIMIT ' . $total_skip . ', ' . $limit;

    // Prepare statement
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $name);
    // Execute query
    $stmt->execute();

    $books_arr = array();
    $depart_id = '';

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      extract($row);
      if (!$depart_id) {
        $depart_id = $department_id;
      }

      $book_item = array(
        'id' => $book_id,
        'name' => $book_name,
        'imgUrl' => $book_img,
        'info' => $book_info,
        'price' => $price,
        'slug' => $book_slug,
      );

      array_push($books_arr, $book_item);
    }

    $num = $this->conn->query('SELECT count(*) FROM '. $this->table . ' b
                                LEFT JOIN 
                                department d ON b.department = d.id
                                WHERE 
                                  d.id = ' . $depart_id)->fetchColumn();

    return array(
      'books' => $books_arr,
      'num' => $num
    );
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

  public function getMostBuyedBooks() {
    $query = 'SELECT * FROM '.$this->table.' WHERE buysNum > 0 ORDER BY buysNum DESC LIMIT 20';

    // Prepare statement
    $stmt = $this->conn->prepare($query);
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

  public function getAutherBooksBySlug($slug, $page) {

    $limit = 20;
    $total_skip = ((int)$page-1)*20;
    
    $query = 'SELECT b.id as book_id, b.name as book_name, b.info as book_info, b.imgUrl as book_img,b.slug as book_slug,
                price, a.id as auther_id
              FROM '. $this->table . ' b
                LEFT JOIN
                auther a ON b.auther = a.id
                WHERE a.slug = ?
                LIMIT ' . $total_skip . ', ' . $limit;
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $slug);
    $stmt->execute();

    $autherId = '';
    $books_arr = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      if (!$autherId) {
        $autherId = $auther_id;
      }

      $book_item = array(
        'id' => $book_id,
        'name' => $book_name,
        'info' => $book_info,
        'imgUrl' => $book_img,
        'slug' => $book_slug,
        'price' => $price
      );

    array_push($books_arr, $book_item);
  }

    $num = $this->conn->query('SELECT count(*) FROM '. $this->table . '
                                    WHERE auther = ' . $autherId)->fetchColumn();

    return array(
      'books' => $books_arr,
      'num' => $num
    );
  }

  public function getBookBySlug($slug) {
    $query = 'SELECT b.id as book_id, b.name as book_name, b.info as book_info, b.imgUrl as book_img,b.slug as book_slug,
    price, buysNum, miniPath, fullPath, isFeatured,
    a.id as auther_id, a.name as auther_name, a.slug as auther_slug,
    d.id as department_id, d.name as department_name
            FROM ' . $this->table . ' b
    LEFT JOIN
    auther a ON b.auther = a.id
    LEFT JOIN
    department d ON b.department = d.id
    WHERE b.slug = ?
    ';

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(1, $slug);
    $stmt->execute();

    return $stmt;
  }

  public function getBooksCount() {
    $query = 'SELECT count(*)
                  FROM ' . $this->table;

    // Prepare statement
    $stmt = $this->conn->query($query);

    // Execute query
    $stmt->fetchColumn();

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
      $id = $this->conn->lastInsertId();
      return $id;
    }
    printf("Error: %s.\n", $stmt->error);

    return false;
  }

  public function addOneBuy($ids) {
    for ($i = 0; $i < count($ids); $i++) {
      $query = 'UPDATE '.$this->table.'
      SET buysNum = buysNum + 1
      WHERE id = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $ids[$i]);
  
      // Execute query
      if (!$stmt->execute()) {
        printf("Error: %s.\n", $stmt->error);
        return false;
      }
    }
    return true;
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
                department = :department, '
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

  public function getBooksPrice($bookIds) {
    $price = 0;
    for ($i = 0; $i < count($bookIds); $i++) {
      $query = 'SELECT price FROM ' . $this->table . ' WHERE id = ?';
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(1, $bookIds[$i]);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        $price += (float)$row['price'];
      } else {
        return -1;
      }
    }
    return $price;
  }
}
