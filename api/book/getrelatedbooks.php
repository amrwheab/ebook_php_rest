<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/book.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  $department = $_GET['department'];
  $auther = $_GET['auther'];
  $bookId = $_GET['bookId'];

  $database = new Database();
  $db = $database->connect();

  $book = new Book($db);


  $result = $book->getRelatedBooks($department, $auther, $bookId);

  $books_arr = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $book_item = array(
        'id' => $book_id,
        'name' => $book_name,
        'imgUrl' => $book_img,
        'info' => $book_info,
        'price' => $price,
        'slug' => $book_slug,
        'rate' => $rateNum > 0 ? $rate/$rateNum : 0,
        'rateNum' => $rateNum
      );

      array_push($books_arr, $book_item);
    }

    echo json_encode($books_arr);