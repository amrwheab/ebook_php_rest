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
  $slug = $_GET['slug'];

  $result = $auther->getOneAuther($slug);

  $auther_row = $result->fetch(PDO::FETCH_ASSOC);

  extract($auther_row);

  $auther_arr = array(
    'id' => $id,
    'name' => $name,
    'imgUrl' => $imgUrl,
    'info' => $info,
    'slug' => $slug
  );

  $book_res = $book->getAutherBooksBySlug($auther_arr['slug'], $page);
  

  echo json_encode(array(
    'auther' => $auther_arr,
    'books' => $book_res['books'],
    'num' => $book_res['num']
  ));