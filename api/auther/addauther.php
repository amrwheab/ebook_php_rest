<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/auther.php';
  include_once '../../helpers/slugify.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $auther = new Auther($db);

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
      global $auther;
      $auther->imgUrl = strtolower(current(explode('/',$_SERVER['SERVER_PROTOCOL']))) . '://' . $_SERVER['HTTP_HOST'] . '/ebook/uploads'.'/' . $file_name;
      return true;
    } else {
      return false;
    }
  }
  
  if (isset($_FILES['autherImg'])) {
    if (uploadingFile('autherImg')) {
      $auther->name = $_REQUEST['name'];
      if (isset($_REQUEST['info'])) {
        $auther->info = $_REQUEST['info'];
      } else {
        $auther->info = 'There is no information about this auther';
      }
      $auther->slug = slugify($auther->name);

      if ($auther->addAuther()) {
        echo json_encode(array(
          'message' => 'Added successfully'
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
  } else {
    $auther->name = $_REQUEST['name'];
    $auther->imgUrl = 'assets/auther.png';
    if (isset($_REQUEST['info'])) {
      $auther->info = $_REQUEST['info'];
    } else {
      $auther->info = 'There is no information about this auther';
    }
    $auther->slug = slugify($auther->name);

    if ($auther->addAuther()) {
      echo json_encode(array(
        'message' => 'Added successfully'
      ));
    } else {
      http_response_code(400);
      echo json_encode(array(
        'message' => 'Some thing went wrong'
      ));
    }
  }
