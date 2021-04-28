<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: DELETE');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');
  // include_once '../../helpers/cors.php';

  // cors_policy();
  
  include_once '../../config/Database.php';
  include_once '../../models/book.php';
  include_once '../../models/auther.php';
  
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  $book = new Book($db);
  $auther = new Auther($db);

  $id = $_GET['id'];
  $result = $book->getAutherBooks($id, 1);
  $num = $result->rowCount();

  if ($num > 0) {
    http_response_code(400);
    echo json_encode(array(
      'message' => 'This Author has books',
      'success' => false
    ));
  } else {
    if ($auther->deleteAuther($id)) {
      echo json_encode(array(
        'message' => 'deleted successfully',
        'success' => true
      ));
    } else {
      http_response_code(400);
      echo json_encode(array(
        'message' => 'Some thing went wrong',
        'success' => false
      ));
    }
  }