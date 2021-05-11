<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/buy.php';
  include_once '../../helpers/cors.php';

  cors_policy();

  $database = new Database();
  $db = $database->connect();

  $buy = new Buy($db);

  $userId = $_GET['userId'];
  $bookId = $_GET['bookId'];
  

  $result = $buy->getMiniBuyed($bookId, $userId)->fetch();
  
  echo json_encode($result);