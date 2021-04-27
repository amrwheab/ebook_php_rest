<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/carousel.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  $database = new Database();
  $db = $database->connect();

  $carousel = new Carousel($db);

  $result = $carousel->getCarousel();
  $num = $result->rowCount();
  $carousel_arr = array();

  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    extract($row);

    $carousel_item = array(
      'id' => $id,
      'title' => $title,
      'content' => $content,
      'img' => $img
    );

    array_push($carousel_arr, $carousel_item);
  }

  echo json_encode($carousel_arr);