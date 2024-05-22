<?php

session_start();
require_once('config/dbFunc.class.php');
include_once('../auth/auth_ssh.class.php');

$db = new dbFunc();
$db = $db->dbConn();

if (isset($_POST['tag_name'])) {


    $result_tag  = pg_query($db, "SELECT id, idea_id, tag FROM public.inc_idea_tag WHERE tag LIKE '" . $_POST['tag_name'] . "%';");
    $tags_array = array();
    while ($line_tag = pg_fetch_assoc($result_tag)) {
        array_push($tags_array, $line_tag['tag']);
    }

    $distinct = array_unique($tags_array);
}

foreach ($distinct as $oldTag) {
?>
    <li class="list-group-item" style="border-color: white; margin: 0 auto 0 0.5rem; width: 85%;"><a class="dropdown-item" style=" border-radius: 20px;" max_w onclick="fillInputByTag(this, <?= $_POST['idea_id'] ?>)"><?= $oldTag ?></a></li>

<?php
} ?>