<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/user.php';
  include_once '../../helpers/slugify.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  require __DIR__ . '/../../vendor/autoload.php';
  use \Firebase\JWT\JWT;
  

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();
  $user = new User($db);

  $body = json_decode(file_get_contents('php://input'));

  $name = $body->name;
  $email = $body->email;
  $password = $body->password;
  $address = $body->address;
  $slug = slugify($name);

  while ($user->checkSlug($slug)) {
    $slug .= rand(0, 10);
  }

  $hashPass = password_hash($password, PASSWORD_DEFAULT);

  // Instantiate blog post object

  if ($user->getUserByEmail($email)) {
    http_response_code(401);
    echo json_encode('user is exist');
  } else {
    if ($user->addUser($name, $email, $hashPass, $address, $slug)) {
      
      $id = $user->getUserByEmail($email)['id'];
      $key = "djfheufeirieueurhteieetyui";
      $payload = array("id" => $id);
    
      $jwt = JWT::encode($payload, $key);
      echo json_encode(array(
        'token' => $jwt,
        'user' => array(
          'id' => $id,
          'name' => $name,
          'email' => $email,
          'password' => $password,
          'address' => $address,
          'slug' => $slug
        )
      ));
    } else {
      http_response_code(401);
      echo json_encode('some thing went wrong');
    }
  }