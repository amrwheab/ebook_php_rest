<?php 
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: DELETE');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');
  
    include_once '../../config/Database.php';
    include_once '../../models/carousel.php';
    
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();
  
    // Instantiate blog post object
    $carousel = new Carousel($db);
    $params = explode('/' ,$_SERVER['PHP_SELF']);
    $id = end($params);

    if ($carousel->deleteCarousel($id)) {
      echo json_encode('deleted successfully');
    } else {
      http_response_code(400);
      echo json_encode('some thing went wrong');
    }