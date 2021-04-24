<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/book.php';

  $database = new Database();
  $db = $database->connect();

  $book = new Book($db);


  $result = $book->getAllBooks();

  $num = $result->rowCount();

  if($num > 0) {

    $books_arr = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
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
        'buysNum' => $buysNum,
        'miniPath' => $miniPath,
        'fullPath' => $fullPath,
        'isFeatured' => $isFeatured === "1" ? true : false,
        'auther' => array(
          'id' => $auther_id,
          'name' => $auther_name,
          'info' => $auther_info,
          'imgUrl' => $auther_img,
          'slug' => $auther_slug,
        ),
        'slug' => $book_slug
      );

      array_push($books_arr, $book_item);
    }

    echo json_encode($books_arr);

  } else {
    http_response_code(400);
    echo json_encode(
      array('message' => 'No Books Found')
    );
  }
