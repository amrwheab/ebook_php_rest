<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/book.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  $name = $_GET['name'];
  $page = $_GET['page'];

  $database = new Database();
  $db = $database->connect();

  $book = new Book($db);


  $result = $book->getByDepartName($name, $page);

  echo json_encode($result);