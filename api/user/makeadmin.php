<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/user.php';
  include_once '../../helpers/cors.php';
  
  cors_policy();
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $user = new User($db);

  $body = json_decode(file_get_contents('php://input'));

  
  $userId = $body->userId;
  $value = $body->value;

  if ($user->makeAdmin($value, $userId)) {
    echo json_encode(array('message' => 'updated successfully'));
  } else {
    http_response_code(400);
    echo json_encode(array('message' => 'some thing went wrong'));
  }