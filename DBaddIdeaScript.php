<?php

session_start();
require_once('config/dbFunc.class.php');
require_once('ideaPhpFuncs/DBgetAchive.php');
require_once('ideaPhpFuncs/ideaStatusFunc.php');
include_once('../auth/auth_ssh.class.php');

$db = new dbFunc();
$db = $db->dbConn();

$au = new auth_ssh();

try {

    // Undefined | Multiple Files | $_FILES Corruption Attack
    // If this request falls under any of them, treat it invalid.
    if (
        !isset($_FILES['file']['error']) ||
        is_array($_FILES['file']['error'])
    ) {
        throw new RuntimeException('Неправильно заданы параметры');
        exit;
    }

    // Check $_FILES['upfile']['error'] value.
    switch ($_FILES['file']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('Файл не отправисля');
            exit;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Файл слишкоом большой');
            exit;
        default:
            throw new RuntimeException('Неизвестная ошибка');
            exit;
    }

    // You should also check filesize here.
    if ($_FILES['file']['size'] > 1000000) {
        throw new RuntimeException('Файл слишком большой');
        exit;
    }

    // DO NOT TRUST $_FILES['upfile']['mime'] VALUE !!
    // Check MIME Type by yourself.
    $wrong_format = false;
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (false === $ext = array_search(
        $finfo->file($_FILES['file']['tmp_name']),
        array(
            'jpg' => 'image/jpg',
            'png' => 'image/png',
            'jpeg' => 'image/jpeg',
        ),
        true
    )) {
        $wrong_format = true;
        throw new RuntimeException('Неправильный формат файла');
        exit;
    }

    // You should name it uniquely.
    // DO NOT USE $_FILES['upfile']['name'] WITHOUT ANY VALIDATION !!
    // On this example, obtain safe unique name from its binary data.

    $uniq_path = sprintf(
        './assets/images/%s.%s',
        sha1_file($_FILES['file']['tmp_name']),
        $ext
    );
    if (!move_uploaded_file(
        $_FILES['file']['tmp_name'],
        $uniq_path
    )) {

        throw new RuntimeException('Не удалось сохранить загруденный файл');
        exit;
    }
} catch (RuntimeException $e) {

    echo $e->getMessage();
}

if (isset($_POST['title_req']) && isset($_POST['text_req']) && isset($uniq_path)) {

    $title = $_POST['title_req'];
    $descr = $_POST['text_req'];
    $postTime = date("D M j G:i:s Y T");
    $postId = pg_fetch_row(pg_query($db, "SELECT count(*) FROM inc_idea"));

    $user_id = $au->getUserId($_SESSION['hash']);

    try {
        pg_query($db, "INSERT into inc_idea VALUES(" . $postId[0] . ", '$title', '$descr'," . $user_id . ", 1, '$postTime', '$postTime', null, null, null, null,'$uniq_path', 0, 0, 0, 0)");

        pg_query($db, "INSERT INTO public.inc_executors(idea_id, user_id, role, role_date)VALUES (" . $postId[0] . ", " . $user_id . ", 3, '" . $postTime . "');");

        getAchive(0, $db);

        echo $postId[0];
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    //header('Location:index.php');
}

// if ($res){
//     echo "kryto";
// }else{
//     echo "ploxo";
// }
