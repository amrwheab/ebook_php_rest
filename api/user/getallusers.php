<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/user.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  $database = new Database();
  $db = $database->connect();

  $user = new User($db);

  $page = $_GET['page'];
  $search = $_GET['search'];
  $limit = $_GET['limit'];

  $result = $user->getAllUsers($page, $limit, $search);

  $num = $result->rowCount();

    $users_arr = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      extract($row);

      $user_item = array(
        'id' => $id,
        'name' => $name,
        'email' => $email,
        'address' => $address,
        'isAdmin' => $isAdmin === '1' ? true : false,
        'mainAdmin' => $mainAdmin === '1' ? true : false
      );

      array_push($users_arr, $user_item);
    }

    echo json_encode($users_arr);