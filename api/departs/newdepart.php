<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/department.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $depart = new Department($db);
  $name = json_decode(file_get_contents('php://input'))->departName;
  $id = $depart->addDepart($name);

  if ($id)  {
    echo json_encode(array(
      'id' => $id,
      'name' => $name
    ));
  } else {
    http_response_code(400);
    echo json_encode(array(
      'message' => 'Some thing went wrong'
    ));
  }
