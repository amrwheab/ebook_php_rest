<?php 
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');
  
    include_once '../../config/Database.php';
    include_once '../../models/auther.php';
    include_once '../../helpers/cors.php';

    cors_policy();
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();
  
    // Instantiate blog post object
    $auther = new Auther($db);
    
    $auther->imgUrl = '';

    $id = $_REQUEST['id'];

    $auther->name = $_REQUEST['name'];
    $auther->info = $_REQUEST['info'];

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
      if (!uploadingFile('autherImg')) {
        http_response_code(400);
        echo json_encode(array(
          'message' => 'image not loaded',
          'success' => false
        ));
        die();
      }
    }

    if ($auther->updateAuther($id)) {
      $auth_res = array(
        'message' => 'updated sucessfully',
        'success' => true
      );
      if (isset($_FILES['autherImg'])) {
        $auth_res['imgPath'] =  $auther->imgUrl;
      }
      echo json_encode($auth_res);
    } else {
      http_response_code(400);
      echo json_encode(array(
        'message' => 'Some thing went wrong',
        'success' => false
      ));
    }