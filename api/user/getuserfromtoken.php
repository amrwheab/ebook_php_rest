<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/user.php';

  require __DIR__ . '/../../vendor/autoload.php';
  use \Firebase\JWT\JWT;

  $database = new Database();
  $db = $database->connect();
  $user = new User($db);

  $params = explode('/', $_SERVER['PHP_SELF']);
  $token = end($params);
  $key = "djfheufeirieueurhteieetyui";

  $id = JWT::decode($token, $key, array('HS256'))->id;

  $user_arr = $user->getUserById($id);
  if ($user_arr) {
    echo json_encode($user_arr);
  } else {
    http_response_code(401);
    echo json_encode('Invalid token');
  }