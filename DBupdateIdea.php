<?php

session_start();
require_once('config/dbFunc.class.php');
include_once('../auth/auth_ssh.class.php');

$db = new dbFunc();
$db = $db->dbConn();

if (isset($_POST['postId'])) {

    switch ($_POST['status']) {
        case 3:
            if (isset($_POST['vote_start']) && isset($_POST['vote_finish'])) {
                pg_query($db, "UPDATE public.inc_idea SET status=3, vote_start='" . date('d.m.Y H:i:s', strtotime($_POST['vote_start'])) . "', vote_finish='" . date('d.m.Y H:i:s', strtotime($_POST['vote_finish'])) . "' WHERE id = " . $_POST['postId'] . ";");
                print_r("tyt?!");
            } else {
                pg_query($db, "UPDATE public.inc_idea SET status=3");
                print_r("aloooooooooo");
            }
            break;

        case 7:
            pg_query($db, "UPDATE public.inc_idea SET status=7 WHERE id = " . $_POST['postId'] . ";");
            break;

        case 8:
            pg_query($db, "UPDATE public.inc_idea SET status=8 WHERE id = " . $_POST['postId'] . ";");
            break;
        case 9:
            pg_query($db, "UPDATE public.inc_idea SET status=9 WHERE id = " . $_POST['postId'] . ";");
            break;
        case 6:
            // if (isset($_POST['start_freetry']) && isset($_POST['end_freetry'])) {
            pg_query($db, "UPDATE public.inc_idea SET status=6, freetry_start='" . date('d.m.Y H:i:s', strtotime($_POST['start_freetry'])) . "', freetry_finish='" . date('d.m.Y H:i:s', strtotime($_POST['end_freetry'])) . "' WHERE id = " . $_POST['postId'] . ";");
            // }
            // if ($_POST['status'] == 6) {
            //     pg_query($db, "UPDATE public.inc_idea SET status=6");
            // }
            break;
        case 4:
            pg_query($db, "UPDATE public.inc_idea SET status=4 WHERE id = " . $_POST['postId'] . ";");
            break;
        default:
            # code...
            break;
    }
}
