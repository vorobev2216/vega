<?php

session_start();
require_once('config/dbFunc.class.php');
include_once('../auth/auth_ssh.class.php');
require_once('ideaPhpFuncs/ideaStatusFunc.php');
require_once('ideaPhpFuncs/DBgetAchive.php');

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
        './assets/images/profilePicsAndQuots/%s.%s',
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

$user_data = getStudentData($au->loggedIn($_SESSION['hash']), $db);

getAchive(7, $db);

if (isset($_POST['quotProfile']) && isset($uniq_path)) {

 

    pg_query($db, "UPDATE public.inc_user_profile SET image='" . $uniq_path . "', quote='" . $_POST['quotProfile'] . "' WHERE id = " . $user_data['id'] . ";");


    //header('Location:index.php');
}

if (isset($uniq_path)) {

    pg_query($db, "UPDATE public.inc_user_profile SET image='" . $uniq_path . "' WHERE id = " . $user_data['id'] . ";");


    //header('Location:index.php');
}


if (isset($_POST['info'])) {



    pg_query($db, "UPDATE public.inc_user_profile SET  info='" . $_POST['info'] . "' WHERE id = " . $user_data['id'] . ";");


    //header('Location:index.php');
}


if (isset($_POST['quotProfile'])) {


    pg_query($db, "UPDATE public.inc_user_profile SET quote='" . $_POST['quotProfile'] . "' WHERE id = " . $user_data['id'] . ";");


    //header('Location:index.php');
}
