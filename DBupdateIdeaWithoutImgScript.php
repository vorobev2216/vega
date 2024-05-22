<?php

session_start();
require_once('config/dbFunc.class.php');
include_once('../auth/auth_ssh.class.php');

$db = new dbFunc();
$db = $db->dbConn();

$au = new auth_ssh();

if (isset($_POST['title_req']) && isset($_POST['text_req']) ) {

    // if (!isset($uniq_path)){
    //     $uniq_path = $_POST['oldImg'];
    // }
    $title = $_POST['title_req'];
    $descr = $_POST['text_req'];

    $postTime = date("D M j G:i:s Y T");

    pg_query($db, "UPDATE public.inc_idea SET status = 1, title=" . "'$title'" . ", description=" . "'$descr'" . ", modified=" . "'$postTime'" . ", image=" . "null " . "WHERE id =" . $_POST['postId']);



    echo 'kryto';

    //header('Location:index.php');
}
