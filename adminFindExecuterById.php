<?php

if (isset($_POST['id_student'])) {
    include_once('config\dbFunc.class.php');

    $db = new dbFunc();

    $db = $db->dbConn();

    foreach ($_POST['id_student'] as $studentId) {


        $res_students_names = pg_query($db, "SELECT first_name, middle_name, last_name FROM public.students where id =" . $studentId);
        $res_students_profile_data = pg_query($db, "SELECT * FROM public.inc_user_profile WHERE id = " . $studentId);
        
        $full_name = pg_fetch_assoc($res_students_names);
        $full_profile_data = pg_fetch_assoc($res_students_profile_data);
        if ($full_profile_data){
            $image = $full_profile_data['image'];
        }else{
            $image = 'assets/images/ffde09eee51c3f288d1ebf5cef6b5c600cf31a3f.jpeg';
        }

        // echo $full_name['first_name'] . " " .$full_name['middle_name'] . " ". $full_name['last_name'] ;
?>
        <div class="row" id="in_team_list<?= $_POST['idx'] ?>Idea<?= $_POST['idea_id'] ?>" name='teamList<?= $_POST['idea_id'] ?>' style="margin: 2rem;">
            <div class="col-5">
                <div class="row" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 14px;">
                    <div class="col-3">
                        <img src="<?=$image?>" class="d-inline-block align-top" style="object-fit: cover;width: 50px;height: 50px; border-radius: 40px;" alt="">
                    </div>
                    <div class="col">
                        <ul class="list-group">
                            <li class="list-group-item" style="border-color: white;">
                                <?= $full_name['first_name'] ?> <?= $full_name['middle_name'] ?>
                            </li>
                            <li class="list-group-item" style="border-color: white;">
                                gryppa
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <button type="button" class="btn-edit-idea" id="leadearBtnAgree<?= $_POST['idx'] ?>Idea<?= $_POST['idea_id'] ?>" onclick="selectLeader(<?= $_POST['idea_id'] ?>, <?= $studentId ?>, <?= $_POST['idx'] ?>)" style="border-color: white; font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 14px;">сделать главным</button>
                <button type="button" class="btn-edit-idea d-none" id="leadearBtnDeny<?= $_POST['idx'] ?>Idea<?= $_POST['idea_id'] ?>" onclick="disSelectLeader(<?= $_POST['idea_id'] ?>, <?= $studentId ?>, <?= $_POST['idx'] ?>)" style="border-color: white; color: #BCBCBC; font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 14px;">сделать обычным</button>
            </div>
            <div class="col-3">
                <button type="button" id="deleteFromTeamBtn<?= $_POST['idx'] ?>Idea<?= $_POST['idea_id'] ?>" class="btn-delete-idea" onclick="removeFromTeam(<?= $_POST['idea_id'] ?>, <?= $_POST['idx'] ?>, <?= $studentId ?>)" style="border-color: white; font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 14px;">удалить</button>
            </div>
            <input class="form-check-input d-none" type="checkbox" name="inTeamSwitch<?= $_POST['idea_id'] ?>" value="<?= $studentId ?>" id="isLeaderStudent<?= $_POST['idx'] ?>Idea<?= $_POST['idea_id'] ?>">
        </div>
<?php
        $_POST['idx']++;
    }
}
