<?php

require_once('config/dbFunc.class.php');
include_once('../auth/auth_ssh.class.php');

$au = new auth_ssh();
$db = new dbFunc();
$db = $db->dbConn();

if (isset($_POST['idea_idx']) && isset($_POST['last_show_comment_idx'])) {


    $res = pg_query($db, "SELECT * FROM inc_comment WHERE idea_id=" . $_POST['idea_idx'] . " and comment_id = -1 and id > " . $_POST['last_show_comment_idx'] . " ORDER BY id LIMIT 4");


    while ($comment = pg_fetch_array($res, null, PGSQL_ASSOC)) {
        $result_author = pg_query($db, 'SELECT * FROM students WHERE id=' . $comment['author_id']) or die('Ошибка запроса: ' . pg_last_error());

        $author = pg_fetch_array($result_author, null, PGSQL_ASSOC);
?>
        <div class="row justify-content-between d-none" value="<?= $comment['id'] ?>" id='comment_id<?= $comment['id'] ?>'>

            <div class="col-8" style="padding: 2rem 2rem 0 2rem">
                <div class="row">
                    <div class="col-auto">

                        <img src="assets\images\df7be9dc4f467187783aca68c7ce98e4df2172d0.jpeg" class="d-inline-block align-top" style="width: 60px;height: 60px; border-radius: 40px; margin-left: 1.25rem;object-fit: cover;" alt="">

                    </div>
                    <div class="col-9">
                        <ul class="list-group">
                            <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #14213D;">
                                <?= $author['middle_name'] ?> <?= $author['first_name'] ?>
                            </li>
                            <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;">
                                <?= $comment['description'] ?>
                            </li>
                        </ul>

                    </div>
                </div>

            </div>


            <div class="col-auto d-flex" style="align-items: center;">
                <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 23px;color: #D9D9D9;padding: 2rem;">
                    <?= date('d.m.Y H:i:s', strtotime($comment['created'])); ?>
                </p>
            </div>
            <hr style="width: 90%;margin-left: 5%;border: 1px solid; color: #7F7F7F;">
        </div>
<?php
    }

    //header('Location:index.php');
}
