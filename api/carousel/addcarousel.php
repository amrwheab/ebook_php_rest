<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/carousel.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $carousel = new Carousel($db);

  $title = '';
  $content = '';
  $img = '';

  function getImgType($name) {
    $res = '';
    for ($i = strlen($name)-1; $i >= 0; $i--) {
      if ($name[$i] !== '.') {
        $res = $name[$i] . $res;
      } else {
        break;
      }
    }
    return $res;
  }

  function uploadingFile($formdata) {
    $file = $_FILES[$formdata];
    $file_name = md5(uniqid($file['name'], true)) . '.' . getImgType($file['name']);
    $file_target = '../../uploads/' . $file_name;
  
    if (move_uploaded_file($file["tmp_name"], $file_target)) {
      global $img;
      $img = strtolower(current(explode('/',$_SERVER['SERVER_PROTOCOL']))) . '://' . $_SERVER['HTTP_HOST'] . '/ebook/uploads'.'/' . $file_name;
      return true;
    } else {
      return false;
    }
  }

  if (uploadingFile('carouselImg')) {
    $title = $_REQUEST['title'];
    $content = $_REQUEST['content'];
    $car_id = $carousel->addCarousel($title, $content, $img);
    if ($car_id) {
      echo json_encode(array(
        'id' => $car_id,
        'title' => $title,
        'content' => $content,
        'img' => $img
      ));
    } else {
      http_response_code(400);
      echo json_encode(array(
        'message' => 'Some thing went wrong'
      ));
    }
  } else {
    http_response_code(400);
    echo json_encode(array(
      'message' => 'Some thing went wrong uploading picture'
    ));
  };