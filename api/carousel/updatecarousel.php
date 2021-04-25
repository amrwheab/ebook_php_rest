<?php 
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');
  
    include_once '../../config/Database.php';
    include_once '../../models/carousel.php';
  
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();
  
    // Instantiate blog post object
    $carousel = new Carousel($db);
    
    $img = '';

    $id = $_REQUEST['id'];

    $title = $_REQUEST['title'];
    $content = $_REQUEST['content'];

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

    if (isset($_FILES['carouselImg'])) {
      if (!uploadingFile('carouselImg')) {
        http_response_code(400);
        echo json_encode('image not loaded');
        die();
      }
    }

    if ($carousel->updateCarousel($id, $title, $content, $img)) {
      echo json_encode('updated sucessfully');
    } else {
      http_response_code(400);
      echo json_encode('Some thing went wrong');
    }