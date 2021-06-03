<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/rate.php';
  include_once '../../helpers/cors.php';
  
  cors_policy();
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $rate = new Rate($db);

  $body = json_decode(file_get_contents('php://input'));

  $bookId = $body->bookId;
  $userId = $body->userId;
  $value = $body->value;

  $checkRatee = $rate->checkRate($userId, $bookId);

  if ($checkRatee) {
    if ((int)$value == 0) {
      if ($rate->deleteRate($userId, $bookId, $checkRatee['value'])) {
        echo json_encode(array('message' => 'deleted successfully'));
      } else {
        http_response_code(400);
        echo json_encode(array('message' => 'some thing went wrong'));
      }
    } else{
      if ($rate->updateRate($userId, $bookId, $value, $checkRatee['value'])) {
        echo json_encode(array('message' => 'updated successfully'));
      } else {
        http_response_code(400);
        echo json_encode(array('message' => 'some thing went wrong'));
      }
    } 
  } else {
    if ($rate->addRate($userId, $bookId, $value)) {
      echo json_encode(array('message' => 'added successfully'));
    } else {
      http_response_code(400);
      echo json_encode(array('message' => 'some thing went wrong'));
    }
  }
