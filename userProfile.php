<!DOCTYPE html>
<html lang="en">

<head>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- <script type="text/javascript" src="jquery.js"></script> -->
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script type="text/javascript" src="https://unpkg.com/popper.js"></script>
    <script type="text/javascript" src="https://unpkg.com/tooltip.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;500;700&display=swap" rel="stylesheet">
    <script src="../idea/jsScripts/DBComments.js"></script>
    <script src="../idea/jsScripts/pageAnimation.js"></script>
    <script src="../idea/jsScripts/DBAddVotes.js"></script>
    <script src="../idea/jsScripts/DBaddReq.js"></script>
    <script src="../idea/jsScripts/DBEditIdea.js"></script>



    <link rel="stylesheet" type="text/css" href="jquery-emoji-picker-master/css/jquery.emojipicker.css">
    <script type="text/javascript" src="../idea/jsScripts/jquery.emojipicker.js"></script>

    <!-- Emoji Data -->
    <link rel="stylesheet" type="text/css" href="jquery-emoji-picker-master/css/jquery.emojipicker.g.css">
    <script type="text/javascript" src="../idea/jsScripts/jquery.emojis.js"></script>

    <title>Инкубатор идей</title>

</head>

<body>

    <?php

    include_once('../auth/auth_ssh.class.php');


    session_start();

    require_once('ideaPhpFuncs/ideaStatusFunc.php');

    require_once('config/dbFunc.class.php');

    $db = new dbFunc();
    $db = $db->dbConn();

    $au = new auth_ssh();
    $user = $au->loggedIn($_SESSION['hash']);


    $user_data = getStudentData($user, $db);

    if (!$au->loggedIn($_SESSION['hash'])) {
        http_response_code(401);
        header('Location:index.php');
        exit;
    }


    require_once('ideaPhpFuncs/DBgetAchive.php');

    // if (!isset($_GET['flag'])) {
    //     $flag = 0;
    // } else {
    //     $flag = $_GET['flag'];
    // }


    // echo $this->getElementById('flag_id')->value . "\n";


    // switch ($flag) {
    //     case 0:
    //         $query = 'SELECT * FROM inc_idea  WHERE status != 1 and status != 9 ORDER BY status';
    //         break;
    //     case 1:
    //         $query = 'SELECT * FROM inc_idea WHERE status = 6';
    //         break;
    //     case 2:
    //         $query = 'SELECT * FROM inc_idea WHERE status = 5 or status = 8';
    //         break;
    //     case 3:
    //         $query = 'SELECT * FROM inc_idea WHERE status = 2 or status = 3';
    //         break;
    // }


    $query = 'SELECT * FROM inc_idea  WHERE author =' . $au->getUserId($_SESSION['hash']) . ' ORDER BY status';
    $result = pg_query($db, $query) or die('Ошибка запроса: ' . pg_last_error($db));


    if (!isset($_GET['value'])) {
        $_GET['value'] = 1;
    }
    ?>

    <div class="container">
        <div class="row justify-content-between" style="margin-top: 2rem;">
            <div class="col-2">
                <a href="index.php">
                    <h6 style="font-family: 'Ubuntu';font-style: normal;font-weight: 700;line-height: 41px;color: #FCA311;display:inline-block;max-width: min-content; font-size: 24px;">Инкубатор идей</h6>
                </a>
            </div>
            <!-- <div class="col-6 d-flex align-items-center">


                <input class="search" type="text" placeholder="Найдётся любая идея..." style="width: 70%; height:3rem; line-height: 1.5;background-color: #fff;background-clip: padding-box;border: 2px solid #ced4da;border-top-left-radius:20px;
                border-bottom-left-radius:20px; color: #ced4da; border-right-color: white;outline:none;padding-left: 30px; border-right-width: 0px;">
                <button type="button" class="btn-search" style="height:3rem; width: 50px; border: 2px solid #ced4da;border-top-right-radius: 20px;border-bottom-right-radius:20px; background-color:white;border-left-color: white; border-left-width: 0px;"><svg style="padding-bottom: 5px; padding-right: 5px; color: #ced4da;" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                    </svg></button>


            </div> -->
            <div class="col-auto d-flex align-items-center">
                <button type="button" class="btn" style="color: #FCA311"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" onclick="showHideNavIdea()" class="bi bi-list" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
                    </svg></button>
            </div>
        </div>
    </div>

    <main>
        <div class="container" style="margin-top: 5rem;">
            <div class="row justify-content-around">
                <div class="col-12 col-sm-12 col-md-12 col-lg-auto col-xl-auto mb-sm-2 mb-md-2 mb-lg-0">
                    <button class="btn" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 24px;color: #D9D9D9;" value="0" id="ideasBtn0" onclick="changePageInProfile(this)">О себе</button>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-auto col-xl-auto mb-sm-2 mb-md-2 mb-lg-0">
                    <button class="btn" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 24px;color: #D9D9D9;" value="1" id="ideasBtn1" onclick="changePageInProfile(this)">Идеи</button>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-auto col-xl-auto mb-sm-2 mb-md-2 mb-lg-0">
                    <button class="btn" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 24px;color: #D9D9D9;" value="2" id="ideasBtn2" onclick="changePageInProfile(this)">Достижения</button>

                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-auto col-xl-auto mb-sm-2 mb-md-2 mb-lg-0">
                    <button class="btn" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 24px;color: #D9D9D9;" value="3" id="ideasBtn3" onclick="changePageInProfile(this)">Команды</button>

                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-auto col-xl-auto mb-sm-2 mb-md-2 mb-lg-0">
                    <button class="btn" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 24px;color: #D9D9D9;" value="4" id="ideasBtn4" onclick="changePageInProfile(this)">Ответы</button>

                </div>

            </div>


        </div>
        <div class="select-bar" style="background-color:#D9D9D9; margin-top: 2rem; height: 5px;">
            <div class="select-scroll d-flex" id="progress_id_offset" style="background-color:#FCA311; margin-top: 2rem; height: 5px; width: 82.2px; border-radius: 5px; margin-left: 454.3px;"></div>
        </div>

        <div class="container" style="margin-top: 5rem;">

            <div class="row justify-content-between">
                <div class="col-3">


                    <?php
                    // $student_quary = pg_query($db, "SELECT id FROM public.students WHERE login = '" . $user_data['login'] . "';") or die('Ошибка запроса: ' . pg_last_error($db));
                    // $student_res = pg_fetch_array($student_quary);

                    $user_group_quary = pg_query($db, 'SELECT group_id FROM public.students_to_groups WHERE student_id = ' . $user_data['id'] . ';') or die('Ошибка запроса: ' . pg_last_error($db));
                    $user_group_res = pg_fetch_array($user_group_quary);


                    $user_group_name_quary = pg_query($db, 'SELECT name FROM public.groups WHERE id = ' . $user_group_res['group_id'] . ';') or die('Ошибка запроса: ' . pg_last_error($db));
                    $user_group_name_res = pg_fetch_array($user_group_name_quary);

                    $user_amount_ideas = pg_query($db, 'SELECT count(*) FROM public.inc_idea WHERE author =' . $au->getUserId($_SESSION['hash']) . ';') or die('Ошибка запроса: ' . pg_last_error($db));
                    $user_amount_ideas_res = pg_fetch_array($user_amount_ideas);

                    $user_amount_ideas_in_progress = pg_query($db, 'SELECT count(*) FROM public.inc_idea WHERE (status=3 or status = 6 or status = 4) and author =' . $au->getUserId($_SESSION['hash']) . ';') or die('Ошибка запроса: ' . pg_last_error($db));
                    $user_amount_ideas_in_progress_res = pg_fetch_array($user_amount_ideas_in_progress);

                    $user_amount_ideas_done = pg_query($db, 'SELECT count(*) FROM public.inc_idea WHERE status=7 and author =' . $au->getUserId($_SESSION['hash']) . ';') or die('Ошибка запроса: ' . pg_last_error($db));
                    $user_amount_ideas_done_res = pg_fetch_array($user_amount_ideas_done);

                    $user_profile_data = pg_query($db, 'SELECT * FROM public.inc_user_profile WHERE id = ' . $user_data['id'] . ';') or die('Ошибка запроса: ' . pg_last_error($db));
                    $user_profile_data_res = pg_fetch_array($user_profile_data);

                    if (!$user_profile_data_res) {
                        $user_profile_pic_path = 'assets/images/ffde09eee51c3f288d1ebf5cef6b5c600cf31a3f.jpeg';
                        $user_quote = 'Придумайте цитату';
                        $user_info = 'Напишите что-нибудь о себе';
                        $user_profile_data = pg_query($db, "INSERT INTO public.inc_user_profile(image, quote, id) VALUES ('assets/images/ffde09eee51c3f288d1ebf5cef6b5c600cf31a3f.jpeg', " . $user_quote . ", " . $user_data['id'] . ");") or die('Ошибка запроса: ' . pg_last_error($db));
                    } else {
                        $user_profile_pic_path = $user_profile_data_res['image'];
                        $user_quote =  $user_profile_data_res['quote'];
                        $user_info = $user_profile_data_res['info'];
                    }

                    ?>
                    <div class="card" style="border-radius: 20px;">
                        <div class="row justify-content-center">
                            <div class="col-auto d-none" style="text-align: center;padding-top: 2rem; position: relative;" id="editProfilePic">

                                <input class="card-img-top d-none" type="image" onclick="inputImageTrigger()" id="imageEdit" style="object-fit: cover;width: 80px;height: 80px; border-radius: 40px; opacity: 1;" src="<?= $user_profile_pic_path ?>" alt="Card image cap">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" onclick="inputImageTrigger()" height="30" fill="currentColor" class="bi bi-camera" viewBox="0 0 16 16" style="cursor: pointer; z-index: 1; position: absolute; color: white; left: 36px; top: 48px;">
                                    <path d="M15 12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h1.172a3 3 0 0 0 2.12-.879l.83-.828A1 1 0 0 1 6.827 3h2.344a1 1 0 0 1 .707.293l.828.828A3 3 0 0 0 12.828 5H14a1 1 0 0 1 1 1v6zM2 4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-1.172a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 9.172 2H6.828a2 2 0 0 0-1.414.586l-.828.828A2 2 0 0 1 3.172 4H2z" />
                                    <path d="M8 11a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5zm0 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7zM3 6.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0z" />
                                </svg>
                                <input class="imageControl " type="file" name="file" id="formFile" accept=".jpg, .jpeg, .png" onchange="uplodNewImage()" required hidden>

                            </div>

                            <div class="col-auto" style="text-align: center;padding-top: 2rem; position: relative;" id="profilePic">

                                <img src="<?= $user_profile_pic_path ?>" id="image" class="d-inline-block align-top" style="object-fit: cover;width: 80px;height: 80px; border-radius: 40px; opacity: 1;" alt="">
                                <input hidden id="curUserId" value="<?= getStudentData($_SESSION['hash'], $db) ?>">

                            </div>

                        </div>
                        <div class="row justify-content-center d-none" id = "profileErr" style="margin-top: 1rem; margin-bottom:1rem; color: red;">
                        </div>
                        <div class="row justify-content-center" style="margin-top: 1rem;">
                            <div class="col-auto" style="text-align: center;padding-left: 2rem;padding-right: 2rem; font-family: 'Ubuntu';font-style: normal;font-weight: 700;font-size: 24px;line-height: 28px;color: #FCA311;">
                                <p>
                                    <?= $user_data['middle_name'] ?> <?= $user_data['first_name'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-auto" style="text-align: center;padding-left: 2rem;padding-right: 2rem; 
                                font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 16px;line-height: 18px;color: #000000;">
                                <p>
                                    <?= $user_group_name_res['name'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="row justify-content-center" style="margin-top: 3rem; margin-bottom:3rem;">
                            <div class="col-auto" style="font-family: 'Ubuntu';font-style: italic;font-weight: 400;font-size: 14px;line-height: 18px;color: #BCBCBC; text-align: center;">
                                <p id="quotProfile"><?= $user_quote ?></p>
                                <textarea class="form-control d-none" id="editQuotProfile" placeholder="Измените вашу идею" rows="3" maxlength="100" oninput="resizeTextarea(this)" style="width: 90%;height: 100px; border: none; resize: none; margin-left: 1.25rem;"></textarea>
                            </div>
                        </div>
    
                        <ul class="list-group list-group-flush d-none" id="btnsEditProfile" style="border-bottom-left-radius:20px; border-bottom-right-radius:20px;">
                            <li class="list-group-item" style="display:flex; justify-content: center;"> <!-- Button trigger modal -->

                                <!-- <button type="button" class="btn-edit-idea d-none" onclick="" style="padding: .25rem .5rem .25rem .5rem; margin-right: 1rem;">Сохранить</button> -->

                                <button type="button" class="btn-login-out" id="editSaveBtn" onclick="editPropile(this)" style="padding: .25rem .5rem .25rem .5rem;">Редактировать</button>

                                <button type="button" class="btn-login-out d-none" onclick="closeEditProfile(this)" id="cancelEditPrifileBtn" style="margin-left:1rem; padding: .25rem .5rem .25rem .5rem;">Отмена</button>

                            </li>
                        </ul>
                    </div>

                    <form id="checkInOut" action="../auth/action.php" enctype="multipart/form-data" method="POST">
                        <input type="hidden" value="logout" name="action">
                        <div class="card" style="border-radius: 20px; border-color: white; margin-top: 1rem;">
                            <input type="submit" class="btn-login-out" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 24px;" value="выйти из профиля">
                        </div>
                    </form>
                </div>

                <div class="col-6 d-none" id="page0">
                    <div class="idea" id="" style="margin-bottom:2rem;border-radius: 20px; position: relative;display: flex;flex-direction: column;min-width: 0;word-wrap: break-word;background-color: #fff;background-clip: border-box;border: 2px solid #D9D9D9;">
                        <div style="position: absolute; margin-top: -14px; left: 10%; background-color: white; padding:2px 10px; font-family:Ubuntu;font-style: italic; font-weight: 400; font-size: 14px; color: #D9D9D9;">
                            Краткая информация
                        </div>
                        <div class="idea-body" style="">
                            <div class="row justify-content-between">

                                <div class="col-11">
                                    <ul class="list-group" style="padding: 1rem 1rem 1rem 1rem;">
                                        <li class="list-group-item" id="profileInfo" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;"><?= $user_info ?></li>
                                        <li class="list-group-item d-none" id="profileInfoEdit" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #D9D9D9;">
                                            <textarea class="form-control" id="textAreaInfo" placeholder="Напишите что-нибудь о себе" rows="3" maxlength="1000" oninput="resizeTextarea(this)" style="width: 100%;height: 100px; border: none; resize: none; color: #D9D9D9;"></textarea>
                                        </li>

                                    </ul>
                                </div>
                                <div class="col-1" style="padding: 1rem 3rem 0 0;">
                                    <div class="btn-group dropend">
                                        <button type="button" class="btn-hide-new-idea" id="btn-to-hide-new-idea dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="35" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16" style="color: #D9D9D9;">
                                                <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                                            </svg></button>
                                        <ul class="dropdown-menu" style="padding: 1rem; border-radius: 20px;">
                                            <li>
                                                <button type="button" class="btn-login-out" onclick="showEditInfo()" style="padding: .25rem .5rem .25rem .5rem; border-color: white;">
                                                    Редактировать
                                                </button>
                                            </li>
                                            <!-- <li>
                                                <button type="button" class="btn-login-out" onclick="" style="padding: .25rem .5rem .25rem .5rem; border-color: white;" data-bs-toggle="modal" data-bs-target="#staticBackdrop<?= $line['id'] ?>">
                                                    Удалить
                                                </button>
                                            </li> -->
                                        </ul>

                                    </div>
                                </div>
                            </div>
                            <div class="row d-none" style="margin-bottom: 1rem;" id="editSaveCanselBtns">
                                <div class="col-auto" style="margin-left: 35%;">
                                    <button type="button" class="btn-edit-idea" onclick="saveEditInfo()" style="padding: .25rem .5rem .25rem .5rem;">Сохранить</button>
                                </div>
                                <div class="col-auto" style="margin-right: 10%;">
                                    <button type="button" class="btn-login-out" onclick="hideEditInfo()" style="padding: .25rem .5rem .25rem .5rem;">Отмена</button>
                                </div>
                            </div>
                        </div>


                    </div>

                </div>
                <div class="col-6" id="page1">

                    <?php
                    while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
                        $idea_attr = ideaStatus($line['status']);

                        if ($line['image'] != null) {
                            $isPreShowImage = "";
                        } else {
                            $isPreShowImage = "d-none";
                        }

                    ?>
                        <div class="idea" id="idea<?= $line['id'] ?>" value="<?= $line['status'] ?>" style="margin-bottom:2rem;border-radius: 20px; position: relative;display: flex;flex-direction: column;min-width: 0;word-wrap: break-word;background-color: #fff;background-clip: border-box;border: 1.5px solid <?= $idea_attr['color'] ?>;">
                            <div style="position: absolute; margin-top: -14px; left: 10%; background-color: white; padding:2px 10px; font-family:Ubuntu;font-style: italic; font-weight: 400; font-size: 14px; color: <?= $idea_attr['color'] ?>;">
                                <?= $idea_attr['status_name'] ?>
                            </div>
                            <div class="idea-body" style="">
                                <div class="row justify-content-between">
                                    <div class="col-11">
                                        <ul class="list-group" style="padding: 1rem 1rem 1rem 1rem;">
                                            <li class="list-group-item" id="ideaTitleEdit<?= $line['id'] ?>" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 700;color: #000000; font-size: 16px; line-height: 18px;"><?= $line['title'] ?></li>
                                            <li class="list-group-item d-none" id="editIdeaInputTitle<?= $line['id'] ?>" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 700;color: #000000; font-size: 16px; line-height: 18px;">
                                                <input class="inputIdeaTitle" id="newIdeaInputTitle<?= $line['id'] ?>" type="text" placeholder="Измените название идеи..." style="width: 70%; height:3rem; line-height: 1.5;background-color: #fff;background-clip: padding-box;border: 2px solid white; outline:none; color: #D9D9D9; font-size: 20px;" maxlength="23">
                                            </li>
                                            <li class="list-group-item" id="ideaInputTextareaEdit<?= $line['id'] ?>" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;"><?= $line['description'] ?></li>
                                            <li class="list-group-item d-none" id="editIdeaInputTextarea<?= $line['id'] ?>" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;">
                                                <textarea class="form-control" id="newIdeaInputTitleTextarea<?= $line['id'] ?>" placeholder="Измените вашу идею" rows="3" maxlength="1000" oninput="resizeTextarea(this)" style="width: 100%;height: 100px; border: none; resize: none;"></textarea>

                                            </li>
                                        </ul>
                                    </div>
                                    <?php
                                    ?>
                                    <div class="col-1" style="padding: 1rem 3rem 0 0;">
                                        <div class="btn-group dropend">
                                            <button type="button" class="btn-hide-new-idea" id="btn-to-hide-new-idea dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="35" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16" style="color: #D9D9D9;">
                                                    <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                                                </svg></button>
                                            <ul class="dropdown-menu" style="padding: 1rem; border-radius: 20px;">
                                                <li>
                                                    <button type="button" class="btn-login-out" onclick="showEditIdeaUserInputs(<?= $line['id'] ?>)" style="padding: .25rem .5rem .25rem .5rem; border-color: white;">
                                                        Редактировать
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="btn-login-out" onclick="" style="padding: .25rem .5rem .25rem .5rem; border-color: white;" data-bs-toggle="modal" data-bs-target="#staticBackdrop<?= $line['id'] ?>">
                                                        Удалить
                                                    </button>
                                                </li>
                                            </ul>
                                            <!-- Modal -->
                                            <div class="modal fade" id="staticBackdrop<?= $line['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                <div class="modal-dialog  modal-lg">
                                                    <div class="modal-content" style="border-radius: 20px;">
                                                        <div class="modal-header" style="background-color: #FF6B6B; border-top-left-radius: 20px; border-top-right-radius: 20px;">changeImagePreShow
                                                            <button type="button" class="btn-close btn-close-white" id="closeModalbtn<?= $line['id'] ?>" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row justify-content-center" style="margin: 3rem 0 3rem 0;">
                                                                <div class="col-auto">
                                                                    <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 32px;line-height: 37px;color: #000000; text-align: center;">Вы точно хотите удалить идею<br> "<?= $line['title'] ?>"?</p>
                                                                </div>
                                                            </div>
                                                            <div class="row justify-content-center" style="margin: 3rem 0 3rem 0;">
                                                                <div class="col-auto">
                                                                    <button type="button" class="btn-delete-idea" onclick="deleteIdea(<?= $line['id'] ?>)" style="padding: .25rem 1rem .25rem 1rem;">Удалить</button>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <button type="button" class="btn-login-out" onclick="triggerModalExit(<?= $line['id'] ?>)" style="padding: .25rem 1rem .25rem 1rem; font-size: 16px;">Отмена</button>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="preShowImage<?= $line['id'] ?>" class="row justify-content-center <?= $isPreShowImage ?>">

                                        <div class="col-auto d-none" id="deleteImgBtn<?= $line['id'] ?>" style="margin-left: auto; margin-right: 2rem;">
                                            <button type="button" class="btn-delete-idea" onclick="deleteImageFromEditInputs(<?= $line['id'] ?>)" style="padding: .1rem .5rem .25rem .5rem;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="idea-image" style="padding: 1rem 5% 2rem 5%;">
                                            <img class="card-img-top" id="newIdeaPreShowImage<?= $line['id'] ?>" style="width: 100%;height: 40vh;object-fit: cover;" src="<?= $line['image'] ?>" value="1" alt="Card image cap">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-none" id="hideEditBtn<?= $line['id'] ?>" style="margin-bottom: 1rem;">
                                <div class="col-auto">
                                    <button type="button" class="btn-input" id="emojiBtn" onclick="smileTrigger(event)" style="margin-left: 3rem">
                                        <svg xmlns=" http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-emoji-smile" viewBox="0 0 16 16" style="color: #BCBCBC;">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"></path>
                                            <path d="M4.285 9.567a.5.5 0 0 1 .683.183A3.498 3.498 0 0 0 8 11.5a3.498 3.498 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.498 4.498 0 0 1 8 12.5a4.498 4.498 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"></path>
                                        </svg>
                                    </button>

                                </div>
                                <div class="col-auto" style="margin-right: 2.25rem;" value="0">
                                    <input class="imageControl " type="file" name="file" id="formFile<?= $line['id'] ?>" accept=".jpg, .jpeg, .png" onchange="changeImagePreShow(<?= $line['id'] ?>)" required hidden>
                                    <button type="button" class="btn-input" onclick="inputImageTrigger(<?= $line['id'] ?>)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-card-image" viewBox="0 0 16 16" style="color: #BCBCBC;">
                                            <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"></path>
                                            <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54A.505.505 0 0 1 1 12.5v-9a.5.5 0 0 1 .5-.5h13z"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="col-auto" style="margin-left: auto;">
                                    <button type="button" class="btn-login-out" onclick="hideEditIdeaUserInputs(<?= $line['id'] ?>)" style="padding: .25rem .5rem .25rem .5rem;">Отмена</button>

                                </div>
                                <div class="col-auto" style="margin-right: 3rem;">
                                    <button type="button" class="btn-edit-idea" onclick="saveEditIdeaUserInputs(<?= $line['id'] ?>)" style="padding: .25rem .5rem .25rem .5rem;">Сохранить</button>
                                </div>


                            </div>
                            <div class="w-100" id="w-100" style="margin-left: 2.25rem; margin-top: 1rem; margin-bottom: 1rem; font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 18px;">
                                <div id="titleErr<?= $line['id'] ?>" class="d-none" style="color: red;">
                                    Неправильно введен заголовок
                                </div>
                                <div id="descrErr<?= $line['id'] ?>" class="d-none" style="color: red;">
                                    Неправильно введено описание
                                </div>
                                <div id="fileErr<?= $line['id'] ?>" class="d-none" style="color: red;">

                                </div>
                                <div id="successErr<?= $line['id'] ?>" class="d-none" style="color: green;">
                                    Изменения отправилены на проверку!
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="col-6 d-none" id="page2">
                    <?php
                    tamplateAchive($db);
                    ?>

                </div>
                <div class="col-6 d-none" id="page3">
                    <?php
                    $user_team_query = pg_query($db, 'SELECT idea_id FROM public.inc_executors WHERE user_id = ' . $user_data['id'] . ' and (role = 2 or role = 1);') or die('Ошибка запроса: ' . pg_last_error($db));

                    while ($user_team_res = pg_fetch_array($user_team_query)) {
                        $team_line_query = pg_query($db, 'SELECT * FROM public.inc_idea WHERE id =' . $user_team_res['idea_id'] . ';') or die('Ошибка запроса: ' . pg_last_error($db));
                        $team_with_user = pg_query($db, 'SELECT user_id, role FROM public.inc_executors WHERE role != 0 and idea_id = ' . $user_team_res['idea_id'] . ';');
                        $team_line_res = pg_fetch_array($team_line_query)
                    ?>
                        <div class="idea" id="idea<?= $team_line_res['id'] ?>" value="<?= $team_line_res['status'] ?>" style="margin-bottom:2rem;border-radius: 20px; position: relative;display: flex;flex-direction: column;min-width: 0;word-wrap: break-word;background-color: #fff;background-clip: border-box;border: 1.5px solid #D9D9D9;">
                            <div class="idea-body" style="">
                                <div class="row justify-content-between">
                                    <div class="col-11">
                                        <ul class="list-group" style="padding: 1rem 1rem 1rem 1rem;">
                                            <li class="list-group-item" id="ideaTitleEdit<?= $team_line_res['id'] ?>" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 700;color: #000000; font-size: 16px; line-height: 18px;"><?= $team_line_res['title'] ?></li>
                                            <li class="list-group-item d-none" id="editIdeaInputTitle<?= $team_line_res['id'] ?>" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 700;color: #000000; font-size: 16px; line-height: 18px;">
                                                <input class="inputIdeaTitle" id="newIdeaInputTitle<?= $team_line_res['id'] ?>" type="text" placeholder="Измените название идеи..." style="width: 70%; height:3rem; line-height: 1.5;background-color: #fff;background-clip: padding-box;border: 2px solid white; outline:none; color: #D9D9D9; font-size: 20px;" maxlength="23">
                                            </li>
                                            <li class="list-group-item" id="ideaInputTextareaEdit<?= $team_line_res['id'] ?>" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;"><?= $team_line_res['description'] ?></li>
                                            <li class="list-group-item d-none" id="editIdeaInputTextarea<?= $team_line_res['id'] ?>" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;">
                                                <textarea class="form-control" id="newIdeaInputTitleTextarea<?= $team_line_res['id'] ?>" placeholder="Измените вашу идею" rows="3" maxlength="1000" oninput="resizeTextarea(this)" style="width: 100%;height: 100px; border: none; resize: none;"></textarea>

                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="row justify-content-between d-none" id = "profileTeamIdea<?= $team_line_res['id'] ?>">
                                    <div class="col-11">
                                        <ul class="list-group" style="padding: 1rem 1rem 1rem 1rem;">
                                            <li class="list-group-item" id="ideaTitleEdit<?= $team_line_res['id'] ?>" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 700;color: #000000; font-size: 16px; line-height: 18px;">Команда</li>
                                            <?php
                                            while ($team_with_user_line = pg_fetch_array($team_with_user)) {
                                                $team_with_user_data = pg_query($db, 'SELECT * FROM public.students WHERE id = ' . $team_with_user_line['user_id'] . ';');
                                                $team_with_user_data_line = pg_fetch_assoc($team_with_user_data)
                                            ?>
                                                <li class="list-group-item" id="ideaInputTextareaEdit<?= $team_line_res['id'] ?>" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;"><?=$team_with_user_data_line['middle_name'] ?> <?=$team_with_user_data_line['first_name'] ?> </li>
                                            <?php
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>

                                <div class="row justify-content-end">
                                    <div class="col-auto" style="margin-right: 1rem; margin-bottom: 1rem;">
                                        <a type="button" id="showUserTeamBtn<?= $team_line_res['id'] ?>" class="btn-login" onclick="showUserTeam(<?= $team_line_res['id'] ?>)" style="padding: .25rem .5rem .25rem .5rem; border-radius: 20px;">
                                            Подробнее
                                        </a>
                                    </div>
                                </div>
                            </div>


                        </div>
                    <?php } ?>
                </div>
                <div class="col-6 d-none" id="page4">


                </div>
                <div class="col-3">
                    <div class="card" style="border-radius: 20px;">
                        <div class="row justify-content-center">
                            <div class="col-auto" style="color:#ced4da;font-family: 'Ubuntu';font-style: normal;font-weight: 400; padding-left: 2rem;
                            padding-right: 2rem; padding-bottom: 1rem;">
                                <div class="row justify-content-center" style="margin-top: 3rem; margin-bottom:3rem;">
                                    <div class="col-auto" style="font-family: 'Ubuntu';font-style: normal;font-weight: 700;font-size: 40px;line-height: 45px;color: #FCA311;">
                                        <p>
                                            <?= $user_amount_ideas_res['count'] ?>
                                        </p>
                                    </div>
                                    <div class="col-auto" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 18px;color: #000000;display:inline-block;max-width: min-content; font-size: 16px;">
                                        <p>
                                            идеи в инкубаторе
                                        </p>
                                    </div>
                                </div>
                                <div class="row justify-content-center" style="margin-top: 3rem; margin-bottom:3rem;">
                                    <div class="col-auto" style="font-family: 'Ubuntu';font-style: normal;font-weight: 700;font-size: 38px;line-height: 45px;color: #FCA311;">
                                        <p>
                                            <?= $user_amount_ideas_in_progress_res['count'] ?>
                                        </p>
                                    </div>
                                    <div class="col-auto" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 16px;color: #000000;display:inline-block;max-width: min-content; font-size: 16px;">
                                        <p>
                                            идей в разработке
                                        </p>
                                    </div>
                                </div>
                                <div class="row justify-content-center" style="margin-top: 3rem; margin-bottom:3rem;">
                                    <div class="col-auto" style="font-family: 'Ubuntu';font-style: normal;font-weight: 700;font-size: 38px;line-height: 45px;color: #FCA311;">
                                        <p>
                                            <?= $user_amount_ideas_done_res['count'] ?>
                                        </p>
                                    </div>
                                    <div class="col-auto" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 16px;color: #000000;display:inline-block;max-width: min-content; font-size: 16px;">
                                        <p>
                                            идей реализовано
                                        </p>
                                    </div>
                                </div>
                                <div class="row justify-content-center" style="margin-top: 3rem; margin-bottom:3rem;">
                                    <div class="col-auto" style="font-family: 'Ubuntu';font-style: normal;font-weight: 700;font-size: 38px;line-height: 45px;color: #FCA311;">
                                        <p>
                                            0
                                        </p>
                                    </div>
                                    <div class="col-auto" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 16px;color: #000000;display:inline-block;max-width: min-content; font-size: 16px;">
                                        <p>
                                            достижений получено
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ctmNavIdeaMenuDis" id="navIdea">
                        <div class="row justify-content-center" style="background-color: white;">
                            <div class="row justify-content-center">
                                <div class="col-2" style="margin: 1rem 1rem 0 auto;">
                                    <button type="button" class="btn-login-out" onclick="showHideNavIdea()" style="padding: .25rem .5rem .25rem .5rem; border-color: white;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="row" style="background-color: white;">
                                <div class="col-auto" style="margin: 0 auto 0 1rem;">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item" style="border: white;"><a class="btn-login-out" style="border: white; padding: 1rem; font-family: 'Ubuntu';font-style: normal;font-weight: 700;line-height: 26px;color: #000000;" href="userProfile.php?value=1">Мой профиль</a>
                                        <li class="list-group-item" style="border: white;"><a class="btn-login-out" style="border: white; padding: 1rem;font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 26px;color: #000000;" href="userProfile.php?value=0">О себе</a>
                                        <li class="list-group-item" style="border: white;"><a class="btn-login-out" style="border: white; padding: 1rem;font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 26px;color: #000000;" href="userProfile.php?value=1">Мои идеи</a>
                                        <li class="list-group-item" style="border: white;"><a class="btn-login-out" style="border: white; padding: 1rem;font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 26px;color: #000000;" href="userProfile.php?value=2">Мои достижения</a>
                                        <li class="list-group-item" style="border: white;"><a class="btn-login-out" style="border: white; padding: 1rem;font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 26px;color: #000000;" href="userProfile.php?value=3">Мои команды</a>
                                        <li class="list-group-item" style="border: white;"><a class="btn-login-out" style="border: white; padding: 1rem;font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 26px;color: #000000;" href="userProfile.php?value=4">Ответы</a>

                                    </ul>
                                </div>
                            </div>
                            <!-- <div class="row" style="background-color: white;">
                                <div class="col-auto" style="margin: 0 auto 0 1rem;">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item" style="border: white;"><button class="btn-login-out" style="border: white; padding: 1rem; font-family: 'Ubuntu';font-style: normal;font-weight: 700;line-height: 26px;color: #000000;" value="2" onclick="changePageInProfile(this)">Коллектив</button>
                                        <li class="list-group-item" style="border: white;"><button class="btn-login-out" style="border: white; padding: 1rem;font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 26px;color: #000000;" value="2" onclick="changePageInProfile(this)">Студенты</button>
                                        <li class="list-group-item" style="border: white;"><button class="btn-login-out" style="border: white; padding: 1rem;font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 26px;color: #000000;" value="2" onclick="changePageInProfile(this)">Преподаватели</button>
                                        <li class="list-group-item" style="border: white;"><button class="btn-login-out" style="border: white; padding: 1rem;font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 26px;color: #000000;" value="2" onclick="changePageInProfile(this)">Таблица лидеров</button>
                                    </ul>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>

    </main>
    <script>
        $(document).ready(function() {
            moveProgressBar(document.getElementById('ideasBtn' + <?= $_GET['value'] ?>));
            changePageInProfile(null, <?= $_GET['value'] ?>);

        });
    </script>
</body>