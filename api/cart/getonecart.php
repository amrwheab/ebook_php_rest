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
  $bookId = $_GET['bookId'];
  $key = "djfheufeirieueurhteieetyui";
  $userId = $userId = JWT::decode($token, $key, array('HS256'))->id;

  $result = $cart->getOneCart($userId, $bookId);

  $row = $result->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    extract($row);
    $cart_item = array(
      'id' => $id,
      'userId' => $user_id,
      'bookId' => $book_id
    );
    echo json_encode($cart_item);
  } else {
  $cart_item = array(
    'id' => '',
    'userId' => '',
    'bookId' => ''
  );
    echo json_encode($cart_item);
  }