<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/auther.php';
  include_once '../../models/book.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  $database = new Database();
  $db = $database->connect();

  $auther = new Auther($db);
  $book = new Book($db);

  $page = $_GET['page'];
  $url = $_SERVER['PHP_SELF'];
  
  $url_arr = explode('/', $url);
  $param = end($url_arr);

  $result = $auther->getOneAuther($param);

  $auther_row = $result->fetch(PDO::FETCH_ASSOC);

  extract($auther_row);

  $auther_arr = array(
    'id' => $id,
    'name' => $name,
    'imgUrl' => $imgUrl,
    'info' => $info,
    'slug' => $slug
  );

  $book_res = $book->getAutherBooks($param, $page);
  $books_arr = array();
  while ($row = $book_res->fetch(PDO::FETCH_ASSOC)) {
    extract($row);

    $book_item = array(
      'id' => $id,
      'name' => $name,
      'imgUrl' => $imgUrl,
      'info' => $info,
      'price' => $price
    );

    array_push($books_arr, $book_item);
  }

  echo json_encode(array(
    'auther' => $auther_arr,
    'books' => $books_arr
  ));