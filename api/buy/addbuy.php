<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/buy.php';
  include_once '../../models/book.php';
  include_once '../../models/cart.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  require __DIR__ . '/../../vendor/autoload.php';
  \Stripe\Stripe::setApiKey('sk_test_51IAtnlKMAtDRnFQfRimKYKmj4Dsv0zyW4OQ4rtLH1uZLRuOvzraz8VbBOoMcS5RbS02wCfJ53d1Q1gIpa3ZeC23z00pNyAw5Ku');
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $buy = new Buy($db);
  $book = new Book($db);
  $cart = new Cart($db);

  $token = json_decode(file_get_contents('php://input'))->stripeToken;
  $userId = json_decode(file_get_contents('php://input'))->userId;
  $bookIds = json_decode(file_get_contents('php://input'))->bookIds;
  $price = $book->getBooksPrice($bookIds);

  if ($price != -1) {
    $charge = \Stripe\Charge::create(
      array(
          'amount' => $price * 100,
          'currency' => 'usd',
          'source' => $token
      )
    );
    if ($charge['status'] === 'succeeded') {
      if ($buy->addBuy($userId, $bookIds)) {
        echo json_encode('added successfully');
        for ($i = 0; $i < count($bookIds); $i++) {
          $cart->makeBuy($userId, $bookIds[$i]);
        }
      } else {
        http_response_code(400);
        echo json_encode('some thing went wrong');
      }
    } else {
      http_response_code(400);
      echo json_encode('some thing went wrong');
    }
  } else {
    http_response_code(400);
    echo json_encode('some thing went wrong');
  }
