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


    $au = new auth_ssh();
    $user = $au->loggedIn($_SESSION['hash']);

    if (!$au->loggedIn($_SESSION['hash'])) {
        http_response_code(401);
        header('Location:index.php');
        exit;
    }

    require_once('config/dbFunc.class.php');

    $db = new dbFunc();
    $db = $db->dbConn();

    $user_data = getStudentData($user, $db);

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

    $query = 'SELECT * FROM inc_idea  WHERE id = ' . $_POST['idea'];
    $result = pg_query($db, $query) or die('Ошибка запроса: ' . pg_last_error($db));
    $line = pg_fetch_array($result, null, PGSQL_ASSOC);
    if (!$user) {
        $show_your_like = -1;
    } else {
        if ($au->isAdmin($_SESSION['hash'])) {
            $query = 'SELECT * FROM inc_idea ORDER BY status';
            $result = pg_query($db, $query) or die('Ошибка запроса: ' . pg_last_error($db));
        }

        $show_your_like = $au->getUserId($_SESSION['hash']);
        $user_profile_data = pg_query($db, 'SELECT * FROM public.inc_user_profile WHERE id = ' . $au->getUserId($_SESSION['hash']) . ';');

        $user_profile_data_res = pg_fetch_array($user_profile_data);


        if (!$user_profile_data_res) {
            $user_profile_pic_path = 'assets/images/ffde09eee51c3f288d1ebf5cef6b5c600cf31a3f.jpeg';
            $user_quote = 'Придумайте цитату';
            $user_profile_data = pg_query($db, "INSERT INTO public.inc_user_profile(image, quote, id) VALUES ('assets/images/ffde09eee51c3f288d1ebf5cef6b5c600cf31a3f.jpeg', '" . $user_quote . "', " . $user_data['id'] . ");") or die('Ошибка запроса: ' . pg_last_error($db));
        } else {
            $user_profile_pic_path = $user_profile_data_res['image'];
        }
    }
    ?>

    <div class="container">
        <div class="row justify-content-between" style="margin-top: 2rem;">
            <div class="col-2">
                <a href="index.php">
                    <h6 style="font-family: 'Ubuntu';font-style: normal;font-weight: 700;line-height: 41px;color: #FCA311;display:inline-block;max-width: min-content; font-size: 24px;">Инкубатор идей</h6>
                </a>
            </div>
            <div class="col-7 d-flex align-items-center">


                <?php
                if ($user != '') {
                ?>
                    <div class="new-idea-input" style="width: 83%; margin-bottom:1rem;border-radius: 20px; position: relative;display: flex;flex-direction: column;min-width: 0;word-wrap: break-word;background-color: #fff;background-clip: border-box;border: 2px solid #D9D9D9;">
                        <div style="position: absolute; right: 2.25rem; top: 10px;">
                            <button type="button" class="btn-hide-new-idea d-none" id="btn-to-hide-new-idea" onclick="hideNewIdeaInput(this)"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="15" fill="currentColor" class="bi bi-chevron-up" viewBox="0 0 16 16" style="color: #7F7F7F;">
                                    <path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"></path>
                                </svg></button>
                        </div>
                        <div class="new-idea-body" style="margin-top: 10px;">
                            <div class="row">

                                <div class="col-auto" id="avatar-col-new-idea" style="padding-top: 0px; padding-left: 3.25rem; padding-bottom: 1rem; transition: all 0s ease 0s;">
                                    <img id="newIdeaAvatarImage" src="<?= $user_profile_data_res['image'] ?>" class="d-inline-block align-top" style="object-fit: cover; width: 60px; height: 60px; border-radius: 40px; transition: all 0s ease 0s;" alt="">
                                </div>


                                <div class="col-6 d-flex" id="newIdeaInputColTitle" style="align-items: center; padding: 0px 0px 1rem 1rem; transition: all 0s ease 0s;">
                                    <input class="inputIdeaTitle" id="newIdeaInputHide" type="text" placeholder="Введите название идеи..." onclick="showNewIdeaInput()" style="width: 70%; height: 3rem; line-height: 1.5; background-color: rgb(255, 255, 255); background-clip: padding-box; border: 2px solid white; outline: none; color: rgb(217, 217, 217); transition: all 0s ease 0s; font-size: 24px;">

                                    <ul class="list-group d-none" id="inputList" style="width: 100%; transition: all 0s ease 0s;">
                                        <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 28px;color: #14213D; margin: 0;">
                                            <input class="inputIdeaTitle" id="newIdeaInputTitle" type="text" placeholder="Введите название идеи..." style="width: 70%; height:3rem; line-height: 1.5;background-color: #fff;background-clip: padding-box;border: 2px solid white; outline:none; color: #D9D9D9; font-size: 20px;" maxlength="23">

                                        </li>
                                        <hr style="width: 95%; border: 1px solid; color: #7F7F7F; margin: 0; margin-left: 1.25rem">
                                        <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 28px;color: #14213D; margin: 0;">
                                            <textarea class="form-control" id="newIdeaInputTitleTextarea" placeholder="Введите вашу идею" rows="3" maxlength="1000" oninput="resizeTextarea(this)" style="width: 100%;height: 100px; border: none; resize: none;"></textarea>
                                            <input hidden id="textareaheight" value="100">
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-auto d-flex" id="newIdeaSmile" style="margin-left: auto; align-items: center; transition: all 0s ease 0s;">
                                    <button type="button" class="btn-input" onclick="showNewIdeaInput()">
                                        <svg xmlns=" http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-emoji-smile" viewBox="0 0 16 16" style="color: #BCBCBC;">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"></path>
                                            <path d="M4.285 9.567a.5.5 0 0 1 .683.183A3.498 3.498 0 0 0 8 11.5a3.498 3.498 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.498 4.498 0 0 1 8 12.5a4.498 4.498 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="col-auto d-flex" style="margin-right: 2.25rem; align-items: center; transition: all 0s ease 0s;" id="newIdeaImage">
                                    <button type="button" class="btn-input" onclick="showNewIdeaInput()">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-card-image" viewBox="0 0 16 16" style="color: #BCBCBC;">
                                            <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"></path>
                                            <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54A.505.505 0 0 1 1 12.5v-9a.5.5 0 0 1 .5-.5h13z"></path>
                                        </svg>
                                    </button>
                                </div>

                                <hr id="newIdeaHr" style="width: 87.8%; border: 1px solid; color: rgb(127, 127, 127); margin: 0px 0px 0px 3.25rem; transition: all 0s ease 0s;" class="d-none">
                                <div id="preShowImage" class="row justify-content-center d-none">


                                    <img id="newIdeaPreShowImage" src="assets\images\ffde09eee51c3f288d1ebf5cef6b5c600cf31a3f.jpeg" class="d-inline-block align-top" style="object-fit: cover; max-width: 320px; max-height: 225px; border-radius: 40px; transition: all 0s ease 0s; margin: 1rem 0 1rem 0;" alt="">


                                    <hr id="newIdeaHrpreShowImage" style="width: 88%; border: 1px solid; color: rgb(127, 127, 127); margin-left: 3.25rem;">
                                </div>
                            </div>

                        </div>

                        <div class="new-idea-body d-none" id="newIdeaBody" style="margin-bottom: 1rem; transition: all 0s ease 0s;">


                            <div class="row">
                                <div class="col-auto" style="padding-left: 3.25rem;">
                                    <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 700;padding-top: 1rem;">Выберите категории</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-auto" style="color:#ced4da;font-family: 'Ubuntu';font-style: normal;font-weight: 400; padding-left: 3.25rem;
                            padding-right: 2rem; width: 220px;">
                                    <select class="form-select" aria-label="Default select example" style="border-radius: 10px;">
                                        <option class="option-inc" selected="">Не выбрано</option>
                                        <option class="option-inc" value="1">Мебель</option>
                                        <option class="option-inc" value="2">Занятия</option>
                                        <option class="option-inc" value="3">Да</option>
                                    </select>
                                </div>
                                <div class="col-auto" style="margin-left: auto;">
                                    <button type="button" class="btn-login" style="width: 25rem; border-radius: 20px; width: auto;
                                                         font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 18px;" id="submit-btn" onclick="DBaddReq()">опубликовать</button>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn-input" id="emojiBtn" onclick="smileTrigger(event)">
                                        <svg xmlns=" http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-emoji-smile" viewBox="0 0 16 16" style="color: #BCBCBC;">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"></path>
                                            <path d="M4.285 9.567a.5.5 0 0 1 .683.183A3.498 3.498 0 0 0 8 11.5a3.498 3.498 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.498 4.498 0 0 1 8 12.5a4.498 4.498 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"></path>
                                        </svg>
                                    </button>

                                </div>
                                <div class="col-auto" style="margin-right: 2.25rem;" value="0">
                                    <input class="imageControl " type="file" name="file" id="formFile" accept=".jpg, .jpeg, .png" onchange="changeImagePreShow()" required hidden>
                                    <button type="button" class="btn-input" onclick="inputImageTrigger()">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-card-image" viewBox="0 0 16 16" style="color: #BCBCBC;">
                                            <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"></path>
                                            <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54A.505.505 0 0 1 1 12.5v-9a.5.5 0 0 1 .5-.5h13z"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="w-100" id="w-100" style="margin-left: 2.25rem; margin-top: 1rem;  font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 18px;">
                                    <div id="titleErr" class="d-none" style="color: red;">
                                        Неправильно введен заголовок
                                    </div>
                                    <div id="descrErr" class="d-none" style="color: red;">
                                        Неправильно введено описание
                                    </div>
                                    <div id="fileErr" class="d-none" style="color: red;">

                                    </div>
                                    <div id="successErr" class="d-none" style="color: green;">
                                        Идея добавлена!
                                    </div>
                                </div>
                            </div>
                        </div>




                    </div>
                <?php
                }
                ?>

                <!-- <input class="search" type="text" placeholder="Найдётся любая идея..." style="width: 70%; height:3rem; line-height: 1.5;background-color: #fff;background-clip: padding-box;border: 2px solid #ced4da;border-top-left-radius:20px;
                border-bottom-left-radius:20px; color: #ced4da; border-right-color: white;outline:none;padding-left: 30px; border-right-width: 0px;">
                <button type="button" class="btn-search" style="height:3rem; width: 50px; border: 2px solid #ced4da;border-top-right-radius: 20px;border-bottom-right-radius:20px; background-color:white;border-left-color: white; border-left-width: 0px;"><svg style="padding-bottom: 5px; padding-right: 5px; color: #ced4da;" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                    </svg></button> -->


            </div>
            <div class="col-auto d-flex align-items-center">
                <button type="button" class="btn" style="color: #FCA311"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
                    </svg></button>
            </div>
        </div>
    </div>

    <main>
        <div class="select-bar" style="background-color:#D9D9D9; margin-top: 2rem; height: 3px;">
        </div>

        <div class="container" style="margin-top: 5rem;">

            <div class="row justify-content-between">
                <?php
                if ($user == '') {
                ?>
                    <div class="col-3">
                        <div class="card" style="border-radius: 20px;">
                            <div class="row justify-content-center">
                                <div class="col-auto" style="text-align: center;padding-top: 2rem;">
                                    <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 700;">Привет</p>
                                </div>

                            </div>
                            <div class="row justify-content-center">
                                <div class="col-auto" style="text-align: center;padding-left: 2rem;padding-right: 2rem; color:#ced4da;font-family: 'Ubuntu';font-style: normal;font-weight: 400;">
                                    <p>чтобы добавить идею
                                        <br />

                                        необходимо войти в свой профиль
                                    </p>
                                </div>
                            </div>
                            <ul class="list-group list-group-flush" style="border-bottom-left-radius:20px; border-bottom-right-radius:20px;">
                                <li class="list-group-item" style="display:flex; justify-content: center;"> <!-- Button trigger modal -->
                                    <button type="button" class="btn" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 24px;color: #FCA311;" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                        Войти
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                        <div class="modal-dialog  modal-lg">
                                            <div class="modal-content" style="border-radius: 20px;">
                                                <div class="modal-header" style="background-color: #FCA311; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row justify-content-center">
                                                        <div class="col-auto">
                                                            <!-- assets\images\f4a2a326c836e0b9588a110a431bca1a.png -->
                                                            <img src="assets\images\f4a2a326c836e0b9588a110a431bca1a.png" class="rounded mx-auto d-block" alt="..." style="max-width: 20%; object-fit: cover;">
                                                        </div>

                                                    </div>
                                                    <div class="row justify-content-center">
                                                        <div class="col-auto">
                                                            <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 32px;line-height: 37px;color: #000000;">Введите данные</p>
                                                        </div>
                                                    </div>
                                                    <form id="checkLog" action="../auth/action.php" enctype="multipart/form-data" name="login" method="POST">
                                                        <div class="row justify-content-center" style="margin-top: 2rem;">
                                                            <div class="col-auto">
                                                                <input class="search" type="text" name="login" placeholder="Логин" style="height:3rem; line-height: 1.5;background-color: #fff;background-clip: padding-box;
                                                        border: 2px solid #ced4da;border-radius:20px; color: #BCBCBC;outline:none;padding-left: 30px; box-shadow: 0px 3px 3px #a1a0a0; width: 25rem;">
                                                            </div>
                                                        </div>
                                                        <div class="row justify-content-center" style="margin-top: 2rem;">
                                                            <div class="col-auto">
                                                                <input class="search" type="password" name="password" placeholder="Пароль" style="height:3rem; line-height: 1.5;background-color: #fff;background-clip: padding-box;
                                                        border: 2px solid #ced4da;border-radius:20px; color: #BCBCBC;outline:none;padding-left: 30px; box-shadow: 0px 3px 3px #a1a0a0; width: 25rem;">
                                                            </div>
                                                        </div>

                                                        <div class="row justify-content-center" style="margin-top: 2rem;">
                                                            <div class="col-auto">
                                                                <button type="submit" class="btn-login" style="width: 25rem; border-radius: 20px; box-shadow: 0px 3px 3px #a1a0a0; width: 25rem;
                                                         font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 16px;line-height: 18px;">Войти</button>
                                                                <input type="hidden" name="action" value="login">
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <div class="row justify-content-center" style="margin-top: 3rem; margin-bottom: 3rem;">
                                                        <div class="col-auto">
                                                            <a class="btn" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 24px;color: #BCBCBC;" href="#">Забыли пароль?</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </li>
                            </ul>
                        </div>
                    </div>
                <?php
                } else {

                    $student_quary = pg_query($db, "SELECT id FROM public.students WHERE login = '" . $user_data['login'] . "';") or die('Ошибка запроса: ' . pg_last_error($db));
                    $student_res = pg_fetch_array($student_quary);

                    $user_group_quary = pg_query($db, 'SELECT group_id FROM public.students_to_groups WHERE student_id = ' . $student_res['id'] . ';') or die('Ошибка запроса: ' . pg_last_error($db));
                    $user_group_res = pg_fetch_array($user_group_quary);


                    $user_group_name_quary = pg_query($db, 'SELECT name FROM public.groups WHERE id = ' . $user_group_res['group_id'] . ';') or die('Ошибка запроса: ' . pg_last_error($db));
                    $user_group_name_res = pg_fetch_array($user_group_name_quary);

                    $user_amount_ideas = pg_query($db, 'SELECT count(*) FROM public.inc_idea WHERE author =' . $au->getUserId($_SESSION['hash']) . ';') or die('Ошибка запроса: ' . pg_last_error($db));
                    $user_amount_ideas_res = pg_fetch_array($user_amount_ideas);


                ?>

                    <div class="col-3">
                        <div class="card" style="border-radius: 20px;">
                            <div class="row justify-content-center">
                                <div class="col-auto" style="text-align: center;padding-top: 2rem;">

                                    <img src="<?= $user_profile_pic_path ?>" class="d-inline-block align-top" style="width: 80px;height: 80px; border-radius: 40px; object-fit: cover;" alt="">

                                </div>

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
                            <ul class="list-group list-group-flush" style="border-bottom-left-radius:20px; border-bottom-right-radius:20px;">
                                <li class="list-group-item" style="display:flex; justify-content: center;"> <!-- Button trigger modal -->
                                    <a type="button" class="btn" href="userProfile.php" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 24px;color: #14213D;">
                                        Перейти в профиль
                                    </a>
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

                <?php
                }
                ?>

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

                    $idea_attr = ideaStatus($line['status']);

                    if ($line['image'] != null) {
                        $isPreShowImage = "";
                    } else {
                        $isPreShowImage = "d-none";
                    }

                    ?>
                    <div class="idea" id="idea<?= $line['id'] ?>" value="<?= $line['status'] ?>" style="margin-bottom:2rem;border-radius: 20px; position: relative;display: flex;flex-direction: column;min-width: 0;word-wrap: break-word;background-color: #fff;background-clip: border-box;border: 1.5px solid #D9D9D9; margin-left: 1rem;">
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
                                if ($au->isAdmin($_SESSION['hash'])) {
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
                                                        <div class="modal-header" style="background-color: #FF6B6B; border-top-left-radius: 20px; border-top-right-radius: 20px;">
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
                                <?php
                                }
                                ?>
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
                            <div id="tagsDiv<?= $line['id'] ?>" class="row">
                                <?php $query_tag = pg_query($db, 'SELECT * FROM public.inc_idea_tag WHERE idea_id = ' . $line['id']) or die('Ошибка запроса: ' . pg_last_error());
                                while ($tag_line = pg_fetch_array($query_tag, null, PGSQL_ASSOC)) {

                                ?>
                                    <div class="col-auto">
                                        #<?= $tag_line['tag'] ?>
                                    </div>
                                <?php
                                } ?>
                            </div>
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
                        <?php
                        $query_comments = 'SELECT * FROM inc_comment WHERE idea_id=' . $line['id'] . ' and comment_id = -1 ORDER BY id';
                        $result_comments = pg_query($db, $query_comments) or die('Ошибка запроса: ' . pg_last_error());

                        ?>

                        <div class="idea-body-comment" id="idea-body-comment<?= $line['id'] ?>" style="border-top-style: solid;border-top-width: 2px; color: #D9D9D9; margin-top: 1rem;">
                            <?php
                            if ($comment = pg_fetch_array($result_comments, null, PGSQL_ASSOC)) {

                                $query_comments = 'SELECT * FROM inc_comment WHERE idea_id=' . $line['id'] . ' and comment_id = -1 ORDER BY id LIMIT 4';
                                $result_comments = pg_query($db, $query_comments) or die('Ошибка запроса: ' . pg_last_error());

                                $query_count_comments = 'SELECT count(*) FROM inc_comment WHERE idea_id=' . $line['id'] . ' and comment_id = -1';
                                $result_count_comments = pg_query($db, $query_count_comments) or die('Ошибка запроса: ' . pg_last_error());
                                $count_comments = pg_fetch_array($result_count_comments, null, PGSQL_ASSOC);

                                $show_comment_idx = 0;
                                $last_comment_idx = 0;



                                while ($comment = pg_fetch_array($result_comments, null, PGSQL_ASSOC)) {
                                    $result_author = pg_query($db, 'SELECT * FROM students WHERE id=' . $comment['author_id']) or die('Ошибка запроса: ' . pg_last_error());

                                    $author = pg_fetch_array($result_author, null, PGSQL_ASSOC);

                                    $count_reply_comment_query = pg_query($db, 'SELECT count(*) FROM public.inc_comment WHERE comment_id = ' . $comment['id']) or die('Ошибка запроса: ' . pg_last_error());
                                    $count_reply_comment = pg_fetch_array($count_reply_comment_query, null, PGSQL_ASSOC);

                                    $author_reply_profile_data = pg_query($db, 'SELECT * FROM public.inc_user_profile WHERE id = ' . $author['id'] . ';') or die('Ошибка запроса: ' . pg_last_error($db));
                                    $author_reply_profile_res = pg_fetch_array($author_reply_profile_data);

                                    if ($show_comment_idx < 2) {
                                        $dNoneShowComments = "row justify-content-between";
                                    } else {
                                        $dNoneShowComments = "row justify-content-between d-none";
                                    }
                            ?>
                                    <div class="<?= $dNoneShowComments ?>" value="<?= $comment['id'] ?>" id='comment_id<?= $comment['id'] ?>'>

                                        <div class="col-8" style="padding: 2rem 2rem 0 2rem;">
                                            <div class="row">
                                                <div class="col-auto">

                                                    <img src="<?= $author_reply_profile_res['image'] ?>" class="d-inline-block align-top" style="object-fit: cover; width: 60px;height: 60px; border-radius: 40px; margin-left: 1.25rem;" alt="">

                                                </div>
                                                <div class="col-9">
                                                    <ul class="list-group">
                                                        <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #14213D;">
                                                            <?= $author['middle_name'] ?> <?= $author['first_name'] ?>
                                                        </li>
                                                        <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;">
                                                            <?= $comment['description'] ?>
                                                        </li>
                                                        <li class="list-group-item" id="commsBtns<?= $comment['id'] ?>" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;">
                                                            <button type="button" class="btn btn-outline-secondary" onclick="changeSendBtnAttrToReply(<?= $line['id'] ?>,'<?= $user_data['first_name'] ?>','<?= $user_data['middle_name'] ?>','<?= $user_data['last_name'] ?>','<?= $au->getUserId($_SESSION['hash']) ?>','<?= $user_data['login'] ?>','<?= $author['middle_name'] ?>','<?= $author['first_name'] ?>', <?= $comment['id'] ?>)" style="border-color: white; font-family: Ubuntu;">Ответить</button>
                                                            <?php
                                                            if ($count_reply_comment['count'] > 0) {
                                                            ?>
                                                                <button type="button" class="btn btn-outline-secondary" onclick="showReplyComments(this, <?= $comment['id'] ?>)" style="border-color: white; font-family: Ubuntu;">Показать ответы</button>
                                                            <?php
                                                            }
                                                            ?>
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
                                        <div class="d-none" id="reply_comments<?= $comment['id'] ?>">
                                            <?php
                                            $reply_comment_query = pg_query($db, 'SELECT * FROM public.inc_comment WHERE comment_id = ' . $comment['id']) or die('Ошибка запроса: ' . pg_last_error());
                                                            
                                            while ($reply_comment = pg_fetch_array($reply_comment_query, null, PGSQL_ASSOC)) {
                                                $author_reply_profile_data = pg_query($db, 'SELECT * FROM public.inc_user_profile WHERE id = ' . $reply_comment['author_id'] . ';') or die('Ошибка запроса: ' . pg_last_error($db));
                                            $author_reply_profile_res = pg_fetch_array($author_reply_profile_data);
                                            ?>
                                                <div class="row" style="margin-top: 1rem;">
                                                    <div class="col-8" style="padding-left: 4rem;">
                                                        <div class="row">
                                                            <div class="col-auto">

                                                                <img src="<?= $author_reply_profile_res['image'] ?>" class="d-inline-block align-top" style="object-fit: cover;width: 60px;height: 60px; border-radius: 40px; margin-left: 1.25rem;" alt="">

                                                            </div>
                                                            <div class="col-8">
                                                                <ul class="list-group">
                                                                    <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #14213D;">
                                                                        <?= $author['middle_name'] ?> <?= $author['first_name'] ?>
                                                                    </li>
                                                                    <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;">
                                                                        <?= $reply_comment['description'] ?>
                                                                    </li>
                                                                    <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;">
                                                                        <button type="button" class="btn btn-outline-secondary" onclick="changeSendBtnAttrToReply(<?= $line['id'] ?>,'<?= $user_data['first_name'] ?>','<?= $user_data['middle_name'] ?>','<?= $user_data['last_name'] ?>','<?= $au->getUserId($_SESSION['hash']) ?>','<?= $user_data['login'] ?>','<?= $author['middle_name'] ?>','<?= $author['first_name'] ?>', <?= $comment['id'] ?>)" style="border-color: white; font-family: Ubuntu;">Ответить</button>
                                                                    </li>
                                                                </ul>

                                                            </div>
                                                        </div>

                                                    </div>


                                                    <div class="col-auto d-flex" style="align-items: center;">
                                                        <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 23px;color: #D9D9D9;padding: 2rem;">
                                                            <?= date('d.m.Y H:i:s', strtotime($reply_comment['created'])); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            ?>

                                        </div>
                                        <hr style="width: 90%;margin-left: 5%;border: 1px solid; color: #7F7F7F;">
                                    </div>

                            <?php
                                    $last_comment_idx = $comment['id'];
                                    $show_comment_idx++;
                                }
                            }
                            ?>
                        </div>


                        <?php
                        if (isset($count_comments['count'])) {
                            if ($count_comments['count'] > 2) {
                        ?>
                                <div id="showMoreDiv<?= $line['id'] ?>" style="padding: .5rem 0 0 9rem;">
                                    <button type="button" id="btn-add-two-more<?= $line['id'] ?>" class="btn btn-outline-secondary" style="border-color: white; font-family: Ubuntu;" onclick="showMoreComments(this, <?= $line['id'] ?>)">Показать следующие комментарии...</button>
                                </div>
                        <?php
                            }
                        } ?>


                        <div class="card-body" id="add_two-more-div<?= $line['id'] ?>">
                            <div class="row align-items-center">

                                <?php if ($user) {
                                ?>
                                    <div class="col d-none" id="div-show-all-comments<?= $line['id'] ?>" style="margin-left: 75%; font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 23px; color: #000000;">
                                        <button type="button" id="btn-show-all-comments<?= $line['id'] ?>" class="btn btn-outline-secondary" style="border-color: white;" onclick="showAllComments(<?= $line['id'] ?>)">Развернуть</button>
                                    </div>
                                    <div class="col-auto">
                                        <img id="newIdeaAvatarImage" src="<?= $user_profile_data_res['image'] ?>" class="d-inline-block align-top" style="object-fit: cover;width: 50px; height: 50px; border-radius: 40px; transition: all 0s ease 0s; margin-left: 2rem;" alt="">
                                    </div>
                                    <div class="col">
                                        <div style="position: relative;display: flex;">
                                            <textarea class="form-control" id="commentTextArea<?= $line['id'] ?>" placeholder="Напишите свой комментарий..." rows="3" maxlength="300" onfocus="cursorToEnd(this, <?= $line['id'] ?>)" onblur="defaultTextareaComment(this, <?= $line['id'] ?>)" oninput="resizeTextareaComment(this, <?= $line['id'] ?>)" style="resize: none; font-family: Ubuntu; height: 10px; width: 100%; overflow:hidden;"></textarea>
                                            <button type="button" class="btn-smile-comment" id="btn-smile-comment<?= $line['id'] ?>" style=" position: absolute;top: .15rem;right: 1.25rem;height: 20px;width: 20px; border: 2px solid white; background-color:white;">
                                                <svg xmlns=" http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-emoji-smile" viewBox="0 0 16 16" style="color: #BCBCBC;">
                                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"></path>
                                                    <path d="M4.285 9.567a.5.5 0 0 1 .683.183A3.498 3.498 0 0 0 8 11.5a3.498 3.498 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.498 4.498 0 0 1 8 12.5a4.498 4.498 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"></path>
                                                </svg></button>


                                        </div>
                                    </div>

                                    <div class="col-auto d-flex align-items-center">
                                        <ul class="list-group">
                                            <li class="list-group-item" style="border-color: white; margin: 0 auto 0 auto;">
                                                <button type="button" class="btn-send" id="btnSendComment<?= $line['id'] ?>" onclick="pushComment(<?= $line['id'] ?>,'<?= $user_data['first_name'] ?>','<?= $user_data['middle_name'] ?>','<?= $user_data['last_name'] ?>','<?= $au->getUserId($_SESSION['hash']) ?>','<?= $user_data['login'] ?>', null, null, -1)" style="height:3rem; width: 40px; border: 2px solid white;border-right-width: 0px; background-color:white; border-left-width: 0px; padding-right: 2rem;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16" style="color: #BCBCBC;">
                                                        <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z" />
                                                    </svg></button>
                                            </li>
                                            <li class="list-group-item d-none" id="cancelReplyBtn<?= $line['id'] ?>" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;">
                                                <button type="button" class="btn btn-outline-secondary" onclick="addRemoveAuthorNameToReply(<?= $line['id'] ?>,null, null, this)" style="border-color: white; font-family: Ubuntu;">Отмена</button>
                                            </li>
                                        </ul>
                                    </div>
                                <?php }
                                ?>
                                <!-- <div class="col d-none" id="div-add-two-more<?= $line['id'] ?>" style="margin-left: 70%; font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 23px; color: #000000;">
                                            <button type="button" id="btn-add-two-more<?= $line['id'] ?>" class="btn btn-outline-secondary" style="border-color: white;" onclick="showMoreComments(<?= $line['id'] ?>, <?= $last_comment_idx ?>)">Показать ещё...</button>
                                        </div> -->

                            </div>
                        </div>
                    </div>

                </div>

                <?php
                $queryTeam = pg_query($db, "SELECT * FROM public.inc_executors WHERE idea_id = " . $line['id'] . ";") or die('Ошибка запроса: ' . pg_last_error($db));
                ?>

                <div class="col-3">
                    <div class="card" style="border-radius: 20px;">
                        <?php while ($line_executers = pg_fetch_array($queryTeam, null, PGSQL_ASSOC)) {
                            $query_executer = pg_query($db, "SELECT * FROM public.students WHERE id = " . $line_executers['user_id'] . ";") or die('Ошибка запроса: ' . pg_last_error($db));
                            $line_executer = pg_fetch_array($query_executer, null, PGSQL_ASSOC);
                        ?>
                            <div class="row justify-content-left" style="margin: 1rem 0 0 1rem;">
                                <div class="col-auto" style="color:#ced4da;font-family: 'Ubuntu';font-style: normal;font-weight: 400;">
                                    <div class="row justify-content-center" style="">
                                        <div class="col-auto" style="font-family: 'Ubuntu';font-style: normal;font-weight: 700;font-size: 15px;line-height: 25px;color: #FCA311;">
                                            <p>
                                                <?= $line_executer['middle_name'] ?> <?= $line_executer['first_name'] ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>

    </main>
</body>