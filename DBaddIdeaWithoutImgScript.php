<?php

session_start();
require_once('config/dbFunc.class.php');
require_once('ideaPhpFuncs/DBgetAchive.php');
require_once('ideaPhpFuncs/ideaStatusFunc.php');
include_once('../auth/auth_ssh.class.php');

$db = new dbFunc();
$db = $db->dbConn();

$au = new auth_ssh();


if (isset($_POST['title_req']) && isset($_POST['text_req'])) {

    $title = $_POST['title_req'];
    $descr = $_POST['text_req'];
    $postTime = date("D M j G:i:s Y T");
    $postId = pg_fetch_row(pg_query($db, "SELECT count(*) FROM inc_idea"));

    $user_id = $au->getUserId($_SESSION['hash']);

    try {
        pg_query($db, "INSERT into inc_idea (id, title, description, author, status, created, modified, vote_start, vote_finish, freetry_start, freetry_finish, image, \"pro_cost real\", contra_cost, exec_cost, points)
        VALUES(" . $postId[0] . ", '" . $title . "', '" . $descr . "'," . $user_id . ", 1, '" . $postTime . "', '" . $postTime . "', null, null, null, null,null, 0, 0, 0, 0)") or die('Ошибка запроса: ' . pg_last_error($db));

        pg_query($db, "INSERT INTO public.inc_executors(idea_id, user_id, role, role_date)VALUES (" . $postId[0] . ", " . $user_id . ", 3, '" . $postTime . "');");

        getAchive(0, $db);
        echo $postId[0];
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
   

    //header('Location:index.php');
}
