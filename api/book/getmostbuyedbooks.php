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

  $result = $book->getMostBuyedBooks();

  $books_arr = array();

  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    extract($row);

    $book_item = array(
      'id' => $id,
      'name' => $name,
      'imgUrl' => $imgUrl,
      'info' => $info,
      'price' => $price,
      'slug' => $slug
    );

    array_push($books_arr, $book_item);
  }

  echo json_encode($books_arr);