<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/buy.php';
  include_once '../../helpers/cors.php';

  cors_policy();

  $database = new Database();
  $db = $database->connect();

  $buy = new Buy($db);

  $userId = $_GET['userId'];
  $page = $_GET['page'];
  
  $result = $buy->getFullBuyed($userId, $page);
  $num = $buy->getCountBuyed($userId);
  $buyed_arr = array();

  while ($row = $result->fetch()) {
    extract($row);
    array_push($buyed_arr, array(
      'id' => $book_id,
      'name' => $book_name,
      'info' => $book_info,
      'imgUrl' => $book_img,
      'slug' => $book_slug,
      'price' => $price
    ));
  }
  
  echo json_encode(
                    array(
                      'num' => $num,
                      'books' => $buyed_arr
                    )
                  );