<?php 
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: PUT');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');
  
    include_once '../../config/Database.php';
    include_once '../../models/department.php';
  
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();
  
    // Instantiate blog post object
    $depart = new Department($db);

    $id = json_decode(file_get_contents('php://input'))->id;
    $name = json_decode(file_get_contents('php://input'))->name;

    if ($depart->updateDepart($id, $name)) {
      echo json_encode('updated sucessfully');
    } else {
      http_response_code(400);
      echo json_encode('Some thing went wrong');
    }