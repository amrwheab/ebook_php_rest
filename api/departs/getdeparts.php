<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/department.php';
  include_once '../../helpers/cors.php';

  cors_policy();

  $database = new Database();
  $db = $database->connect();

  $depart = new Department($db);

  $result = $depart->getDeparts();
  $num = $result->rowCount();
  $departs_arr = array();

  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    extract($row);

    $depart_item = array(
      'id' => $id,
      'name' => $name
    );

    array_push($departs_arr, $depart_item);
  }

  echo json_encode($departs_arr);