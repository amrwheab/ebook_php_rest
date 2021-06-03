<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/rate.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  $database = new Database();
  $db = $database->connect();

  $rate = new Rate($db);

  $bookId = $_GET['bookId'];
  $userIdd = $_GET['userId'];

  $result = $rate->getRate($bookId);
  $rate_arr = array(
    '1star' => 0,
    '2star' => 0,
    '3star' => 0,
    '4star' => 0,
    '5star' => 0,
    'userRate' => 0
  );

  while ($row = $result->fetch()) {
    extract($row);
    if ((int)$value === 5) {
      $rate_arr['5star'] += 1;
    } elseif ((int)$value === 4){
      $rate_arr['4star'] += 1;
    } elseif ((int)$value === 3){
      $rate_arr['3star'] += 1;
    } elseif ((int)$value === 2){
      $rate_arr['2star'] += 1;
    } elseif ((int)$value === 1){
      $rate_arr['1star'] += 1;
    }

    if ($userId === $userIdd) {
      $rate_arr['userRate'] = (int)$value;
    }
  }

  echo json_encode($rate_arr);