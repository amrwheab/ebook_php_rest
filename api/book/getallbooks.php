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

  $page = $_GET['page'];
  $search = $_GET['search'];
  $limit = $_GET['limit'];

  $result = $book->getAllBooks($page, $search, $limit);

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
        'slug' => $book_slug,
        'rate' => $rateNum > 0 ? $rate/$rateNum : 0,
        'rateNum' => $rateNum
      );

      array_push($books_arr, $book_item);
    }

    echo json_encode($books_arr);