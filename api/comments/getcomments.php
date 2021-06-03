<?php
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/comment.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  $database = new Database();
  $db = $database->connect();

  $comment = new Comment($db);

  $bookId = $_GET['bookId'];
  $page = $_GET['page'];

  $result = $comment->getComments($bookId, $page);
  $comments_arr = array();


  if ($result) {
    while ($row = $result->fetch()) {
      extract($row);
  
      $comment_item = array(
        'id' => $comment_id,
        'body' => $body,
        'user_name' => $user_name
      );
  
      array_push($comments_arr, $comment_item);
    }
  
    echo json_encode(array_reverse($comments_arr));
  } else {
    http_response_code(400);
    echo json_encode('Some thing went wrong');
  }