<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/book.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  $url = $_SERVER['PHP_SELF'];
  $param = '';
  for ($i = strlen($url)-1; $i >= 0; $i--) {
    if ($url[$i] !== '/') {
      $param = $url[$i] . $param;
    } else {
      break;
    }
  }

  $database = new Database();
  $db = $database->connect();

  $book = new Book($db);


  $result = $book->getDepartedBooks($param);

  $num = $result->rowCount();

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
      );

      array_push($books_arr, $book_item);
    }

    echo json_encode($books_arr);