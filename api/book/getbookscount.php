<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/book.php';

  $database = new Database();
  $db = $database->connect();

  $book = new Book($db);

  $result = $book->getBooksCount();
  $num = $result->rowCount();
  echo json_encode($num);