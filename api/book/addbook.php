<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/book.php';
  include_once '../../helpers/slugify.php';
  include_once '../../helpers/cors.php';

  cors_policy();
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $book = new Book($db);

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

  $img_path = '';
  $mini_path = '';
  $full_path = '';

  function uploadingFile($formdata) {
    $file = $_FILES[$formdata];
    $file_name = md5(uniqid($file['name'], true)) . '.' . getImgType($file['name']);
    $file_target = '../../uploads/' . $file_name;
  
    if (move_uploaded_file($file["tmp_name"], $file_target)) {
      if ($formdata === 'imgUrl') {
        global $img_path;
        $img_path = strtolower(current(explode('/',$_SERVER['SERVER_PROTOCOL']))) . '://' . $_SERVER['HTTP_HOST'] . '/ebook/uploads'.'/' . $file_name;
      } elseif ($formdata === 'miniPath') {
        global $mini_path;
        $mini_path = strtolower(current(explode('/',$_SERVER['SERVER_PROTOCOL']))) . '://' . $_SERVER['HTTP_HOST'] . '/ebook/uploads'.'/' . $file_name;
      } else {
        global $full_path;
        $full_path = strtolower(current(explode('/',$_SERVER['SERVER_PROTOCOL']))) . '://' . $_SERVER['HTTP_HOST'] . '/ebook/uploads'.'/' . $file_name;
      }
      return true;
    } else {
      return false;
    }
  }

  if (uploadingFile('imgUrl') && uploadingFile('miniPath') && uploadingFile('fullPath')) {
    $book->name = $_REQUEST['name'];
    $book->imgUrl = $img_path;
    $book->info = $_REQUEST['info'];
    $book->price = $_REQUEST['price'];
    $book->department = $_REQUEST['department'];
    $book->buysNum = 0;
    $book->miniPath = $mini_path;
    $book->fullPath = $full_path;
    $book->isFeatured = $_REQUEST['isFeatured'] === "true" ? "1" : "0";
    $book->auther = $_REQUEST['auther'];
    $book->slug = slugify($book->name);

    while ($book->checkSlug($book->slug)) {
      $book->slug .= rand(0, 10);
    }
    
    $id = $book->addBook();
    if($id) {
      echo json_encode(
        array(
          'id' => $id,
          'name' => $book->name,
          'imgUrl' => $book->imgUrl,
          'info' => $book->info,
          'price' => $book->price,
          'department' => $book->department,
          'buysNum' => $book->buysNum,
          'miniPath' => $book->miniPath,
          'fullPath' => $book->fullPath,
          'isFeatured' => $book->isFeatured,
          'auther' => $book->auther,
          'slug' => $book->slug
          )
      );
    } else {
      echo json_encode(
        array('message' => 'Book Not Added')
      );
    }

  } else {
    array('message' => 'Some thing went wrong while loading');
  }