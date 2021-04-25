<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/auther.php';

  $database = new Database();
  $db = $database->connect();

  $auther = new Auther($db);

  $page = $_GET['page'];

  $result = $auther->getAuthers($page);
  $num = $result->rowCount();
  $authers_arr = array();

  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    extract($row);

    $auther_item = array(
      'id' => $id,
      'name' => $name,
      'imgUrl' => $imgUrl,
      'info' => $info,
      'slug' => $slug
    );

    array_push($authers_arr, $auther_item);
  }

  echo json_encode(array(
    'authers' => $authers_arr,
    'authersCount' => $num
  ));