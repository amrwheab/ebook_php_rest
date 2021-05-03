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

  $result = $cart->getMiniCart($userId);
  $cart_arr = array();

  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    extract($row);

    $cart_item = array(
      'id' => $id,
      'userId' => $user_id,
      'bookId' => $book_id
    );

    array_push($cart_arr, $cart_item);
  }

  echo json_encode($cart_arr);