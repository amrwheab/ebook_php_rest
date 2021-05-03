<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/cart.php';
  include_once '../../helpers/cors.php';
  require __DIR__ . '/../../vendor/autoload.php';
  use \Firebase\JWT\JWT;
  
  cors_policy();
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $cart = new Cart($db);

  $body = json_decode(file_get_contents('php://input'));

  $bookId = $body->bookId;
  $token = $body->token;
  $key = "djfheufeirieueurhteieetyui";

  $userId = JWT::decode($token, $key, array('HS256'))->id;

  if ((int)$cart->getCartCount($userId, $bookId) <= 20) {
    if ($cart->addToCart($bookId, $userId)) {
      echo json_encode(array('message' => 'added successfully'));
    } else {
      http_response_code(400);
      echo json_encode(array('message' => 'some thing went wrong'));
    }
  } else {
    http_response_code(400);
    echo json_encode(array('message' => 'You can\'t add more than 20 book at cart'));
  }