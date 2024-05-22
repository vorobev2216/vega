<?php

session_start();
require_once('config/dbFunc.class.php');
include_once('../auth/auth_ssh.class.php');

$db = new dbFunc();
$db = $db->dbConn();

if (isset($_POST['postId'])) {

    $query_tag = pg_query($db, 'DELETE FROM public.inc_idea_tag WHERE idea_id = ' .  $_POST['postId']) or die('Ошибка запроса: ' . pg_last_error());
}
if (isset($_POST['tagsArr'])) {
    foreach ($_POST['tagsArr'] as $tag) {
        pg_query($db, "INSERT INTO public.inc_idea_tag (idea_id, tag) VALUES (" . $_POST['postId'] . ", '" . $tag . "');");
    }
}
