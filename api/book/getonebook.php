<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/book.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  $url = $_SERVER['PHP_SELF'];
  $param = '';
  for ($i = strlen($url)-1; $i >= 0; $i--) {
    if ($url[$i] !== '/') {
      $param = $url[$i] . $param;
    } else {
      break;
    }
  }

  $database = new Database();
  $db = $database->connect();

  $book = new Book($db);


  $result = $book->getOneBook($param);

  $num = $result->rowCount();

  if($num > 0) {

    $row = $result->fetch(PDO::FETCH_ASSOC);
    extract($row);

    $book_item = array(
      'id' => $book_id,
      'name' => $book_name,
      'imgUrl' => $book_img,
      'info' => $book_info,
      'price' => $price,
      'department' => array(
        'id' => $department_id,
        'name' => $department_name,
      ),
      'buysNum' => $buysNum,
      'miniPath' => $miniPath,
      'fullPath' => $fullPath,
      'isFeatured' => $isFeatured === "1" ? true : false,
      'slug' => $book_slug
    );
  

    echo json_encode($book_item);

  } else {
    http_response_code(400);
    echo json_encode(
      array('message' => 'No Books Found')
    );
  }
