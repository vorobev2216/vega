<?php
session_start();
require_once('config/dbFunc.class.php');
include_once('../auth/auth_ssh.class.php');
require_once('ideaPhpFuncs/ideaStatusFunc.php');
require_once('ideaPhpFuncs/DBgetAchive.php');

$au = new auth_ssh();
$db = new dbFunc();
$db = $db->dbConn();



if (!isset($_POST['ComIdx'])) {
    $comId = -1;
} else {
    $comId = $_POST['ComIdx'];
}


$descr = $_POST['com'];
$postId = $_POST['idPost'];

$postTime = date("D M j G:i:s Y T");



// $isExistAuthor = pg_query($db, "SELECT count(id) FROM public.students WHERE id = " . $_POST['user_id']);
// $isExistAuthor = pg_fetch_assoc($isExistAuthor);

// if ($isExistAuthor['count'] == 0) {
//     $newAuthor = pg_query($db, "INSERT INTO public.users(id, role) VALUES (" . $_POST['user_id'] . ", 3);");
//     $newAuthor = pg_query($db, "INSERT INTO public.students(id, first_name, middle_name, last_name, login) VALUES (" . $_POST['user_id'] . ",'" . $_POST['first_name'] . "','" . $_POST['middle_name'] . "','" . $_POST['last_name'] . "','" . $_POST['login'] . "')");
// }



$quary = "INSERT INTO public.inc_comment(idea_id, comment_id, author_id, description, created, modified)  VALUES(" . $postId . "," . $comId . "," . $au->getUserId($_SESSION['hash']) . ", '" . $descr . "','" . $postTime . "','" . $postTime . "');";
$res = pg_query($db, (string) $quary);

$quary = "SELECT MAX(id) FROM public.inc_comment;";
$res = pg_query($db, (string) $quary);

$user_data = getStudentData($au->loggedIn($_SESSION['hash']), $db);

$user_image = pg_query($db, "SELECT * FROM public.inc_user_profile WHERE id = " . $_POST['user_id']);
$user_image_res = pg_fetch_assoc($user_image);

$newCommentId = pg_fetch_assoc($res)['max'];

getAchive(3, $db);

if ($comId == -1) {
?>

    <div class="row justify-content-between" value="<?= $newCommentId ?>" id="comment_id<?= $newCommentId ?>">

        <div class="col-8" style="padding: 2rem 2rem 0 2rem;">
            <div class="row">
                <div class="col-auto">

                    <img src="<?= $user_image_res['image'] ?>" class="d-inline-block align-top" style="width: 60px;height: 60px; border-radius: 40px; margin-left: 1.25rem;" alt="">

                </div>
                <div class="col-9">
                    <ul class="list-group">
                        <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #14213D;">
                            <?= $user_data['middle_name'] ?> <?= $user_data['first_name'] ?> </li>
                        <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;">
                            <?= $descr ?> </li>
                        <li class="list-group-item" id="commsBtns<?= $newCommentId ?>" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;">
                            <button type="button" class="btn btn-outline-secondary" onclick="changeSendBtnAttrToReply(<?= $postId ?>,'<?= $_POST['first_name'] ?>','<?= $_POST['middle_name'] ?>','<?= $_POST['last_name'] ?>','<?= $_POST['user_id'] ?>','<?= $_POST['login'] ?>','<?= $_POST['middle_name'] ?>','<?= $_POST['first_name'] ?>', <?= $newCommentId ?>)" style="border-color: white; font-family: Ubuntu;">Ответить</button>
                        </li>

                    </ul>

                </div>
            </div>

        </div>


        <div class="col-auto d-flex" style="align-items: center;">
            <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 23px;color: #D9D9D9;padding: 2rem;">
                <?= date('d.m.Y H:i:s', strtotime($postTime)); ?> </p>
        </div>
        <div class="d-none" id="reply_comments<?= $newCommentId ?>">

        </div>
        <hr style="width: 90%;margin-left: 5%;border: 1px solid; color: #7F7F7F;">
    </div>

<?php } else {
?>

    <div class="row" style="margin-top: 1rem;">
        <div class="col-8" style="padding-left: 4rem;">
            <div class="row">
                <div class="col-auto">

                    <img src="<?= $user_image_res['image'] ?>" class="d-inline-block align-top" style="width: 60px;height: 60px; border-radius: 40px; margin-left: 1.25rem;" alt="">

                </div>
                <div class="col-8">
                    <ul class="list-group">
                        <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #14213D;">
                            <?= $user_data['middle_name'] ?> <?= $user_data['first_name'] ?></li>
                        <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;">
                            <?= $descr ?> </li>
                        <li class="list-group-item" id="commsBtns<?= $newCommentId ?>" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;">
                            <button type="button" class="btn btn-outline-secondary" onclick="changeSendBtnAttrToReply(<?= $postId ?>,'<?= $_POST['first_name'] ?>','<?= $_POST['middle_name'] ?>','<?= $_POST['last_name'] ?>','<?= $_POST['user_id'] ?>','<?= $_POST['login'] ?>','<?= $_POST['author_middle_name'] ?>','<?= $_POST['author_firstname'] ?>', <?= $comId ?>)" style="border-color: white; font-family: Ubuntu;">Ответить</button>
                        </li>
                    </ul>

                </div>
            </div>

        </div>


        <div class="col-auto d-flex" style="align-items: center;">
            <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 23px;color: #D9D9D9;padding: 2rem;">
                <?= date('d.m.Y H:i:s', strtotime($postTime)); ?> </p>
        </div>
    </div>
<?php
}
