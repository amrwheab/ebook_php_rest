<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/cart.php';
  include_once '../../helpers/cors.php';
  require __DIR__ . '/../../vendor/autoload.php';
  use \Firebase\JWT\JWT;

  cors_policy();

  $database = new Database();
  $db = $database->connect();

  $cart = new Cart($db);

  $token = $_GET['token'];
  $key = "djfheufeirieueurhteieetyui";
  $userId = $userId = JWT::decode($token, $key, array('HS256'))->id;

  $result = $cart->getFullCart($userId);
  $fullprice = 0;
  $cart_arr = array();

  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    extract($row);
    if ($buyed === '0') {
      $fullprice += $price;
    }
    $cart_item = array(
      'id' => $cart_id,
      'book' => array(
        'id' => $book_id,
        'name' => $book_name,
        'info' => $book_info,
        'imgUrl' => $book_img,
        'slug' => $book_slug,
        'price' => $price,
        'buyed' => $buyed === '1' ? true : false
      )
    );

    array_push($cart_arr, $cart_item);
  }

  echo json_encode(array(
    'cart' => $cart_arr,
    'fullprice' => $fullprice
  ));