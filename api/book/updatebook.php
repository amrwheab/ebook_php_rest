<?php 
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');
  
    include_once '../../config/Database.php';
    include_once '../../models/book.php';
  
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();
  
    // Instantiate blog post object
    $book = new Book($db);
    
    $book->imgUrl = '';
    $book->miniPath = '';
    $book->fullPath = '';

    $id = $_REQUEST['id'];

    $book->name = $_REQUEST['name'];
    $book->info = $_REQUEST['info'];
    $book->price = $_REQUEST['price'];
    $book->department = $_REQUEST['department'];
    $book->buysNum = $_REQUEST['buysNum'];
    $book->isFeatured = $_REQUEST['isFeatured'] === "true" ? "1":"0";
    $book->auther = $_REQUEST['auther'];

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
        global $book;
        if ($formdata === 'imgUrl') {
          $book->imgUrl = strtolower(current(explode('/',$_SERVER['SERVER_PROTOCOL']))) . '://' . $_SERVER['HTTP_HOST'] . '/ebook/uploads'.'/' . $file_name;
        } elseif ($formdata === 'miniPath') {
          $book->miniPath = strtolower(current(explode('/',$_SERVER['SERVER_PROTOCOL']))) . '://' . $_SERVER['HTTP_HOST'] . '/ebook/uploads'.'/' . $file_name;
        } else {
          $book->fullPath = strtolower(current(explode('/',$_SERVER['SERVER_PROTOCOL']))) . '://' . $_SERVER['HTTP_HOST'] . '/ebook/uploads'.'/' . $file_name;
        }
        return true;
      } else {
        return false;
      }
    }

    if (isset($_FILES['imgUrl'])) {
      if (!uploadingFile('imgUrl')) {
        http_response_code(400);
        echo json_encode('image not loaded');
        die();
      }
    }
    if (isset($_FILES['miniPath'])) {
      if (!uploadingFile('miniPath')) {
        http_response_code(400);
        echo json_encode('mini not loaded');
        die();
      }
    }
    if (isset($_FILES['fullPath'])) {
      if (!uploadingFile('fullPath')) {
        http_response_code(400);
        echo json_encode('full not loaded');
        die();
      }
    }

    if ($book->updateBook($id)) {
      echo json_encode(array(
            'id' => $id,
            'name' => $book->name,
            'info' => $book->info,
            'price' => $book->price,
            'department' => $book->department,
            'buysNum' => $book->buysNum,
            'isFeatured' => $book->isFeatured,
            'auther' => $book->auther
          ));
    } else {
      http_response_code(400);
      echo json_encode('Some thing went wrong');
    }