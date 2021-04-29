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

  $result = $auther->getAuthersNames();
  $authers_arr = array();

  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    extract($row);

    $auther_item = array(
      'id' => $id,
      'name' => $name,
      'slug' => $slug
    );

    array_push($authers_arr, $auther_item);
  }

  echo json_encode($authers_arr);