<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: DELETE');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');
  
  include_once '../../config/Database.php';
  include_once '../../models/cart.php';
  include_once '../../helpers/cors.php';
  require __DIR__ . '/../../vendor/autoload.php';
  use \Firebase\JWT\JWT;
  
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  $cart = new Cart($db);

  $bookId = $_GET['bookId'];
  $token = $_GET['token'];
  $key = "djfheufeirieueurhteieetyui";

  $userId = JWT::decode($token, $key, array('HS256'))->id;

  if ($cart->removeFromCart($bookId, $userId)) {
    echo json_encode(array('message' => 'deleted successfully'));
  } else {
    http_response_code(400);
    echo json_encode(array('message' => 'some thing went wrong'));
  }