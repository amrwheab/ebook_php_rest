<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/user.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  require __DIR__ . '/../../vendor/autoload.php';
  use \Firebase\JWT\JWT;

  $database = new Database();
  $db = $database->connect();
  $user = new User($db);

  $token = $_GET['token'];
  $key = "djfheufeirieueurhteieetyui";

  $id = JWT::decode($token, $key, array('HS256'))->id;

  $user_arr = $user->getUserById($id);
  $user_arr['isAdmin'] === '1' ? $user_arr['isAdmin'] = true : $user_arr['isAdmin'] = false;
  $user_arr['mainAdmin'] === '1' ? $user_arr['mainAdmin'] = true : $user_arr['mainAdmin'] = false;
  if ($user_arr) {
    echo json_encode($user_arr);
  } else {
    echo json_encode('Invalid token');
  }