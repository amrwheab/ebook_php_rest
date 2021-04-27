<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: DELETE');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');
  
  include_once '../../config/Database.php';
  include_once '../../models/book.php';
  
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $book = new Book($db);
  $id = $_GET['ids'];

  if ($book->deleteBook($id)) {
    echo json_encode('removed successfully');
  } else {
    http_response_code(400);
    echo json_encode(
      array('message' => 'some thing went wrong')
    );
  }