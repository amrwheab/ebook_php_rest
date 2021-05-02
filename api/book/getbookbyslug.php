<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/book.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  
  $database = new Database();
  $db = $database->connect();
  
  $book = new Book($db);
  $slug = $_GET['slug'];


  $result = $book->getBookBySlug($slug);

    $row = $result->fetch(PDO::FETCH_ASSOC);
    extract($row);

    $book_item = array(
      'id' => $book_id,
      'name' => $book_name,
      'imgUrl' => $book_img,
      'info' => $book_info,
      'price' => $price,
      'department' => array(
        'id' => $department_id,
        'name' => $department_name,
      ),
      'auther' => array(
        'id' => $auther_id,
        'name' => $auther_name,
        'slug' => $auther_slug
      ),
      'buysNum' => $buysNum,
      'miniPath' => $miniPath,
      'fullPath' => $fullPath,
      'isFeatured' => $isFeatured === "1" ? true : false,
      'slug' => $book_slug
    );
  

    echo json_encode($book_item);
