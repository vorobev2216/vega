<?php

session_start();
require_once('config/dbFunc.class.php');
include_once('../auth/auth_ssh.class.php');

$db = new dbFunc();
$db = $db->dbConn();

$au = new auth_ssh();

if ((isset($_POST['idea_id']))) {

    pg_query($db, "DELETE FROM public.inc_idea_vote WHERE idea_id = " . $_POST['idea_id']);
    pg_query($db, "DELETE FROM public.inc_comment WHERE idea_id = " . $_POST['idea_id']);
    pg_query($db, "DELETE FROM public.inc_executors WHERE idea_id = " . $_POST['idea_id']);
    pg_query($db, "DELETE FROM public.inc_idea_tag WHERE idea_id = " . $_POST['idea_id']);
    pg_query($db, "DELETE FROM public.inc_idea WHERE id = " . $_POST['idea_id']);
}
