<?php
session_start();
include_once('config/dbFunc.class.php');
include_once('../auth/auth_ssh.class.php');
require_once('ideaPhpFuncs/ideaStatusFunc.php');
require_once('ideaPhpFuncs/DBgetAchive.php');
$au = new auth_ssh();

$db = new dbFunc();
$db = $db->dbConn();

$user_id = $au->getUserId($_SESSION['hash']);

$count = pg_fetch_row(pg_query($db, "SELECT count(*) FROM inc_idea_vote"));

$quary = "SELECT * FROM inc_idea_vote WHERE idea_id =" . $_POST['postId'] . " and user_id =" . $user_id . ";";
echo $quary;

$result = pg_query($db, (string) $quary);
$line = pg_fetch_assoc($result);

getAchive(4, $db);

if (!is_array($line)) {
   $quary = "INSERT INTO inc_idea_vote VALUES(" . $_POST['postId'] . "," . $user_id . "," . $_POST['likeBool'] . ")";
   echo $quary;
   pg_query($db, (string) $quary);
} else {
   $quary = "UPDATE inc_idea_vote SET value = " . $_POST['likeBool'] . " WHERE idea_id = " . $_POST['postId'] . " and user_id =" . $user_id . ";";
   echo $quary;
   pg_query($db, (string) $quary);
}
