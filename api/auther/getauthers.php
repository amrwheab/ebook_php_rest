<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/auther.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  $database = new Database();
  $db = $database->connect();

  $auther = new Auther($db);

  $page = $_GET['page'];
  $search = $_GET['search'];

  $result = $auther->getAuthers($page, $search);
  $num = $result['num'];
  $authers_arr = array();

  while ($row = $result['stmt']->fetch(PDO::FETCH_ASSOC)) {
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