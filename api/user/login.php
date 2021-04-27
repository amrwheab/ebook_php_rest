<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/user.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  require __DIR__ . '/../../vendor/autoload.php';
  use \Firebase\JWT\JWT;

  $database = new Database();
  $db = $database->connect();
  $user = new User($db);

  $body = json_decode(file_get_contents('php://input'));

  $email = $body->email;
  $password = $body->password;

  $user_arr = $user->getUserByEmail($email);
  if ($user_arr) {
    if (password_verify($password, $user_arr['password'])) {
      $key = "djfheufeirieueurhteieetyui";
      $payload = array("id" => $user_arr['id']);
    
      $jwt = JWT::encode($payload, $key);
      echo json_encode(array(
        'token' => $jwt,
        'user' => $user_arr
      ));
    } else {
      http_response_code(401);
      echo json_encode('wrong password');
    }
  } else {
    http_response_code(401);
    echo json_encode('email doesn\'t exist');
  }