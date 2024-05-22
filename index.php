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
    <script src="../idea/jsScripts/DBAdminsTools.js"></script>
    <script src="../idea/jsScripts/DBExecutersTool.js"></script>



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


    if (array_key_exists('hash', $_SESSION)) {
        $user = $au->loggedIn($_SESSION['hash']);
        $user_data = getStudentData($user, $db);
    } else {
        $user = false;
    }

    if (!$user) {
        $show_your_like = -1;
    } else {
        $show_your_like = getStudentData($user, $db);
    }


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


    $query = 'SELECT * FROM inc_idea  WHERE status != 1 and status != 9 ORDER BY status';
    $result = pg_query($db, $query) or die('Ошибка запроса: ' . pg_last_error($db));

    if (!$user) {
        $show_your_like = -1;
    } else {
        if ($au->isAdmin($_SESSION['hash'])) {
            $query = 'SELECT * FROM inc_idea ORDER BY status';
            $result = pg_query($db, $query) or die('Ошибка запроса: ' . pg_last_error($db));
        }

        $show_your_like = $user_data['id'];
        $user_profile_data = pg_query($db, 'SELECT * FROM public.inc_user_profile WHERE id = ' . $user_data['id'] . ';');

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
        <div class="row">
            <div class="col-3">
                <a href="index.php">
                    <h6 style="font-family: 'Ubuntu';font-style: normal;font-weight: 700;line-height: 41px;color: #FCA311;display:inline-block;max-width: min-content; font-size: 24px;">Инкубатор идей</h6>
                </a>
            </div>
            <div class="col-xl">
            <!-- <input class="inputIdeaTitle w-100" id="newIdeaInputHide" type="text" placeholder="Введите название идеи..." onclick="showNewIdeaInput()" style="height: 3rem; line-height: 1.5; background-color: rgb(255, 255, 255); background-clip: padding-box; border: 2px solid white; outline: none; color: rgb(217, 217, 217); transition: all 0s ease 0s; font-size: 24px;"> -->


                <?php
                if ($user != '') {
                ?>
                    <div class="new-idea-input" style="width: 85%; margin-bottom:1rem;border-radius: 20px; position: relative;display: flex;flex-direction: column;min-width: 0;word-wrap: break-word;background-color: #fff;background-clip: border-box;border: 2px solid #D9D9D9;">
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


                                    <img id="newIdeaPreShowImage" src="" class="d-inline-block align-top" style="object-fit: cover; max-width: 320px; max-height: 225px; border-radius: 40px; transition: all 0s ease 0s; margin: 1rem 0 1rem 0;" alt="">
                                    <input hidden id="newIdeaPreShowImageBool" value="0">


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
                            padding-right: 2rem; width: auto;">
                                    <div class="input-group">
                                        <select class="form-select" id="tagSelectPublish" name="tagSelectPublish" aria-label="Default select example" style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                                            <option class="option-inc" selected="">Не выбрано</option>
                                            <?php
                                            $tagsQuery = pg_query($db, "SELECT DISTINCT tag FROM public.inc_idea_tag;");
                                            $curTagIdx = 0;
                                            while ($tag = pg_fetch_array($tagsQuery, null, PGSQL_ASSOC)) {
                                            ?>
                                                <option class="option-inc" style=";font-family: 'Ubuntu';font-style: normal;font-weight: 400;" value="<?= $curTagIdx++ ?>"><?= $tag['tag'] ?></option>

                                            <?php
                                            } ?>
                                        </select>
                                        <button class="btn btn-outline-secondary" type="button" id="button-addon" style="border-color: #BCBCBC; border-top-right-radius: 10px; border-bottom-right-radius: 10px;" onclick="addSuggestTag()">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16" style="color: #006D77;">
                                                <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"></path>
                                            </svg>
                                        </button>
                                    </div>
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
                            <div class="row">
                                <div class="row" id="addedTags">

                                </div>
                            </div>
                            <div class="row d-none" id="errs">
                                <div class="col-auto" style="margin-left: 3rem; color: red;font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 13px;">
                                    Такой тег уже добавлен!
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
                    </svg></button>

            </div>
            <div class="col-auto">
                <button type="button" class="btn" style="color: #FCA311" onclick="showHideNavIdea()"><svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-list" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z" />
                    </svg></button>
            </div>
        </div>
    </div> -->

    <main>
        <!-- <div class="container" style="margin-top: 5rem;">
            <div class="row justify-content-around">
                <div class="col-12 col-sm-12 col-md-12 col-lg-auto col-xl-auto mb-sm-2 mb-md-2 mb-lg-0">
                    <button class="btn" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 24px;color: #D9D9D9;" onclick="moveProgressBar(this, [2, 3, 4, 5, 6, 7, 8])">Все идеи</button>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-auto col-xl-auto mb-sm-2 mb-md-2 mb-lg-0">
                    <button class="btn" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 24px;color: #D9D9D9;" onclick="moveProgressBar(this, [2, 3])">Предложенные идеи</button>

                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-auto col-xl-auto mb-sm-2 mb-md-2 mb-lg-0">
                    <button class="btn" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 24px;color: #D9D9D9;" onclick="moveProgressBar(this, [6])">Идеи в процессе</button>

                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-auto col-xl-auto mb-sm-2 mb-md-2 mb-lg-0">
                    <button class="btn" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 24px;color: #D9D9D9;" onclick="moveProgressBar(this, [5, 8])">Отклонённые идеи</button>

                </div>


            </div>


        </div> -->
        <div class="select-bar" style="background-color:#D9D9D9; margin-top: 2rem; height: 3px;">
            <!-- <div class="select-scroll d-flex" id="progress_id_offset" style="background-color:#FCA311; margin-top: 2rem; height: 5px; width: 77.20001220703125px; border-radius: 5px; margin-left: 394.8999938964844px;"></div> -->
        </div>

        <div class="container" style="margin-top: 5rem;">

            <div class="row justify-content-between">
                <?php
                if ($user == '') {
                ?>
                    <div class="col-3 offset-md-5" >
                        <div class="card mt-3" style="border-radius: 20px;">
                            <!-- <div class="row justify-content-center">
                                <div class="col-auto" style="text-align: center;padding-top: 2rem;">
                                    <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 700;">Привет</p>
                                </div>

                            </div>
                            <div class="row justify-content-center">
                                <div class="col-auto" style="text-align: center;padding-left: 2rem;padding-right: 2rem; color:#ced4da;font-family: 'Ubuntu';font-style: normal;font-weight: 400;">
                                    <p>чтобы добавить идеюа
                                        <br />

                                        необходимо войти в свой профиль
                                    </p>
                                </div> -->
                            </div> 
                            <ul class="list-group list-group-flush" style="border-bottom-left-radius:20px; border-bottom-right-radius:20px;">
                                <li class="list-group-item" style="display:flex; justify-content: center;"> <!-- Button trigger modal -->
                                    <button type="button" class="btn" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 70px;color: #FCA311; font-size: 30px" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
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
                                                            <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 24px;color: #BCBCBC;text-align: center;">Забыли пароль?<br> Обратитесь к администратору</p>
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

                    // $student_quary = pg_query($db, "SELECT id FROM public.students WHERE login = '" . $user_data['login'] . "';") or die('Ошибка запроса: ' . pg_last_error($db));
                    // $student_res = pg_fetch_array($student_quary);

                    $user_group_quary = pg_query($db, 'SELECT group_id FROM public.students_to_groups WHERE student_id = ' . $user_data['id'] . ';') or die('Ошибка запроса: ' . pg_last_error($db));
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
                <div class="row-6">


                    <?php
                    while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {

                        $status_attr = ideaStatus($line['status']);

                        $likes = 0;
                        $dislikes = 0;
                        $query_count_vote = 'SELECT * FROM inc_idea_vote WHERE inc_idea_vote.idea_id=' . $line['id'];
                        $result_count_vote = pg_query($db, $query_count_vote) or die('Ошибка запроса: ' . pg_last_error());

                        while ($line_count_vote = pg_fetch_array($result_count_vote, null, PGSQL_ASSOC)) {

                            if ($line_count_vote['value'] == 1) {
                                $likes++;
                            }

                            if ($line_count_vote['value'] == -1) {
                                $dislikes++;
                            }
                        }

                        $query_count_comments = 'SELECT count(*) FROM public.inc_comment WHERE idea_id = ' . $line['id'];
                        $result_count_comments = pg_query($db, $query_count_comments) or die('Ошибка запроса: ' . pg_last_error());
                        $count_comments = pg_fetch_array($result_count_comments, null, PGSQL_ASSOC);


                        $query_likes = 'SELECT * FROM inc_idea_vote WHERE inc_idea_vote.user_id= ' . $show_your_like . ' and inc_idea_vote.idea_id=' . $line['id'];
                        $result_likes = pg_query($db, $query_likes) or die('Ошибка запроса: ' . pg_last_error());
                        $likes_line = pg_fetch_array($result_likes, null, PGSQL_ASSOC);

                        $author_data = pg_query($db, 'SELECT * FROM public.students WHERE id =' . $line['author'] . ';') or die('Ошибка запроса: ' . pg_last_error($db));
                        $author_data_res = pg_fetch_array($author_data);

                        $author_group_quary = pg_query($db, 'SELECT group_id FROM public.students_to_groups WHERE student_id = ' . $line['author'] . ';') or die('Ошибка запроса: ' . pg_last_error($db));
                        $author_group_res = pg_fetch_array($author_group_quary);

                        $author_group_name_quary = pg_query($db, 'SELECT name FROM public.groups WHERE id = ' . $author_group_res['group_id'] . ';');
                        $author_group_name_res = pg_fetch_array($author_group_name_quary);

                        $author_profile_data = pg_query($db, 'SELECT * FROM public.inc_user_profile WHERE id = ' . $author_data_res['id'] . ';');
                        $author_profile_data_res = pg_fetch_array($author_profile_data);

                        $query_post_time_from_bd =  pg_query($db, 'SELECT * FROM public.inc_idea WHERE id = ' . $line['id'] . ';');
                        $result_post_time_from_bd = pg_fetch_array($query_post_time_from_bd);

                        if ($likes_line == false) {
                            $put_like = '#D9D9D9';
                            $put_dislike = '#D9D9D9';
                            $likeBool = 0;
                            $disBool = 0;
                        } else {

                            if ($likes_line['value'] == 1) {
                                $put_like = '#FCA311';
                                $put_dislike = '#D9D9D9';
                                $likeBool = 1;
                                $disBool = 0;
                            } else if ($likes_line['value'] == -1) {
                                $put_like = '#D9D9D9';
                                $put_dislike = '#FCA311';
                                $likeBool = 0;
                                $disBool = 1;
                            } else if ($likes_line['value'] == 0) {
                                $put_like = '#D9D9D9';
                                $put_dislike = '#D9D9D9';
                                $likeBool = 0;
                                $disBool = 0;
                            }
                        }


                    ?>
                        <div class="idea col" id="idea<?= $line['id'] ?>" value="<?= $line['status'] ?>" style="margin-bottom:1rem;border-radius: 20px; position: relative;display: flex;flex-direction: column;min-width: 0;word-wrap: break-word;background-color: #fff;background-clip: border-box;border: 2px solid #D9D9D9;">
                            <div class="idea-body" style="">
                                <div class="row justify-content-between">

                                    <div class="row-12" >
                                        <div class="row">
                                            <div class="row-12">

                                                <img src="<?= $author_profile_data_res['image'] ?>" class="d-inline-block align-top" style="object-fit: cover;width: 80px;height: 80px; border-radius: 40px; margin-left: 1.25rem;" alt="">

                                            </div>
                                            <div class="col-12 d-flex" style="align-items: center;">

                                                <ul class="list-group">
                                                    <li class="list-group-item fs-2" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 28px;color: #14213D;">
                                                        <?= $author_data_res['middle_name'] ?> <?= $author_data_res['first_name'] ?>
                                                    </li>
                                                    <li class="list-group-item fs-2" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 18px;color: #000000; padding-top: 0">
                                                        <?= $author_group_name_res['name'] ?>
                                                    </li>
                                                    <li class="list-group-item fs-2" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 18px;color: #D9D9D9; padding-top: 0">

                                                    </li>
                                                </ul>
                                            </div>

                                        </div>
                                    </div>
                                    <?php if ($line['author'] == $user_data['id'] || $au->isAdmin($_SESSION['hash'])) { ?>
                                        <div class="col-1" style="padding: 1rem 3rem 0 0;">
                                            <div class="btn-group dropend">
                                                <button type="button" class="btn-hide-new-idea" id="btn-to-hide-new-idea dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="35" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16" style="color: #D9D9D9;">
                                                        <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                                                    </svg></button>
                                                <ul class="dropdown-menu" style="padding: 1rem; border-radius: 20px;">

                                                    <?php
                                                    if ($line['status'] == 6) {
                                                    ?>
                                                        <li>
                                                            <button type="button" class="btn-login-out" onclick="" style="padding: .25rem .5rem .25rem .5rem; border-color: white;" data-bs-toggle="modal" data-bs-target="#makeTeaBackdrop<?= $line['id'] ?>">
                                                                Команда
                                                            </button>
                                                        </li>
                                                    <?php
                                                    }
                                                    ?>
                                                    <li>
                                                        <?php
                                                        if ($au->isAdmin($_SESSION['hash'])) {
                                                        ?>
                                                            <button type="button" class="btn-login-out" onclick="" style="padding: .25rem .5rem .25rem .5rem; border-color: white;" data-bs-toggle="modal" data-bs-target="#acceptIdeaBackdrop<?= $line['id'] ?>">
                                                                Редактировать
                                                            </button>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <a href="userProfile.php" class="btn-login-out" role="button" style="padding: .25rem .5rem .25rem .5rem; border-color: white;" aria-pressed="true">Редактировать</a>
                                                        <?php

                                                        }
                                                        ?>
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
                                    <?php } ?>

                                    <!-- <div class="col-auto d-flex" style="align-items: center; padding: 2rem;">
                                        <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;line-height: 28px;color: #D9D9D9; padding-right: 1rem;">
                                            3 часа назад
                                        </p>
                                    </div> -->
                                </div>
                                <div style="border: 2px solid;border-top-color: currentcolor;border-right-color: currentcolor;border-right-width: 2px;border-left-color: currentcolor;border-left-width: 2px;color: <?= $status_attr['color'] ?>;margin: 0 2.5rem 1rem 2.5rem;border-right-color: white;border-left-color: white;border-top-color: white;border-right-width: 0;border-left-width: 0;">
                                    <div class="row justify-content-between">
                                        <div class="col">
                                            <p style="font-family: 'Ubuntu';font-style: italic;font-weight: 400;line-height: 28px;color: <?= $status_attr['color'] ?>;font-size: 25px; margin-left: 0; margin-bottom: 0;">
                                                <?= $status_attr['status_name'] ?>
                                            </p>
                                        </div>
                                        <?php if ($line['status'] == 3) { ?>
                                            <div class="col-auto">
                                                <p style="font-family: 'Ubuntu';font-style: italic;font-weight: 400;line-height: 28px;color: <?= $status_attr['color'] ?>;font-size: 25px; margin-left: 0; margin-bottom: 0;">
                                                    <?= voteLeftToStartFinish($line['id'], $db) ?>
                                                </p>
                                                <!-- <hr style="width: 90%;margin-left: 5%;border: 1px solid; color: #006D77; margin-top: 0;" id="ideaStatusHr<?= $line['id'] ?>"> -->
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>

                            <div class="idea-body">



                                <h5 class="idea-title" style="font-family: 'Ubuntu';font-style: normal;font-weight: 700;font-size: 25px;line-height: 136.4%;color: #14213D; padding-left: 3rem;">
                                    <?= $line['title'] ?></h5>
                                <p class="idea-text d-flex" style="max-width: 670px;font-family: 'Ubuntu';font-size: 25px;font-style: normal;font-weight: 400;line-height: 136.4%;color: #000000; padding-left: 3rem; padding-right: 3rem;">
                                    <?= $line['description'] ?></p>
                            </div>
                            <?php
                            if ($line['image'] != null) {

                            ?>
                                <div class="idea-image" style="padding: 1rem 5% 1rem 5%;">
                                    <img class="card-img-top" style="width: 100%;height: 40vh;object-fit: cover;" src="<?= $line['image'] ?>" alt="Card image cap">
                                </div>
                            <?php
                            }
                            if ($line['status'] != 1 && $line['status'] != 9 && $line['status'] != 7 && $line['status'] != 8 && $line['status'] != 5 && voteLeftToStartFinish($line['id'], $db) != '') {
                            ?>
                                <hr style="width: 90%;margin-left: 5%;border: 1px solid; color: #7F7F7F;">
                                <div class="idea-body" style="padding-left: 3rem;">
                                    <button class="btn" id="like_btn<?= $line['id'] ?>" style="color: <?= $put_like ?>" value="<?= $likeBool ?>" style="border-radius: 15px;" onclick="DBAddLike(<?= $line['id'] ?>)"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-hand-thumbs-up-fill" viewBox="0 0 16 16">
                                            <path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a9.84 9.84 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733.058.119.103.242.138.363.077.27.113.567.113.856 0 .289-.036.586-.113.856-.039.135-.09.273-.16.404.169.387.107.819-.003 1.148a3.163 3.163 0 0 1-.488.901c.054.152.076.312.076.465 0 .305-.089.625-.253.912C13.1 15.522 12.437 16 11.5 16H8c-.605 0-1.07-.081-1.466-.218a4.82 4.82 0 0 1-.97-.484l-.048-.03c-.504-.307-.999-.609-2.068-.722C2.682 14.464 2 13.846 2 13V9c0-.85.685-1.432 1.357-1.615.849-.232 1.574-.787 2.132-1.41.56-.627.914-1.28 1.039-1.639.199-.575.356-1.539.428-2.59z" />
                                        </svg><span style="padding-left: 10px;" id="like-span<?= $line['id'] ?>"><?= $likes ?></button>
                                    <button class="btn" id="dis_btn<?= $line['id'] ?>" style="color: <?= $put_dislike ?>" value=" <?= $disBool ?>" style="border-radius: 15px;" onclick="DBAddDislike(<?= $line['id'] ?>)"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-hand-thumbs-down-fill" viewBox="0 0 16 16">
                                            <path d="M6.956 14.534c.065.936.952 1.659 1.908 1.42l.261-.065a1.378 1.378 0 0 0 1.012-.965c.22-.816.533-2.512.062-4.51.136.02.285.037.443.051.713.065 1.669.071 2.516-.211.518-.173.994-.68 1.2-1.272a1.896 1.896 0 0 0-.234-1.734c.058-.118.103-.242.138-.362.077-.27.113-.568.113-.856 0-.29-.036-.586-.113-.857a2.094 2.094 0 0 0-.16-.403c.169-.387.107-.82-.003-1.149a3.162 3.162 0 0 0-.488-.9c.054-.153.076-.313.076-.465a1.86 1.86 0 0 0-.253-.912C13.1.757 12.437.28 11.5.28H8c-.605 0-1.07.08-1.466.217a4.823 4.823 0 0 0-.97.485l-.048.029c-.504.308-.999.61-2.068.723C2.682 1.815 2 2.434 2 3.279v4c0 .851.685 1.433 1.357 1.616.849.232 1.574.787 2.132 1.41.56.626.914 1.28 1.039 1.638.199.575.356 1.54.428 2.591z" />
                                        </svg><span style="padding-left: 10px;" id="dis-span<?= $line['id'] ?>"><?= $dislikes ?></span></button>
                                    <button type="button" class="btn" style="color: #D9D9D9"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-chat-right-fill" viewBox="0 0 16 16">
                                            <path d="M14 0a2 2 0 0 1 2 2v12.793a.5.5 0 0 1-.854.353l-2.853-2.853a1 1 0 0 0-.707-.293H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h12z" />
                                        </svg><span style="padding-left: 10px;" id="countCommentSpan<?= $line['id'] ?>"><?= $count_comments['count'] ?></span></button>

                                </div>

                                <?php
                                $query_comments = 'SELECT * FROM inc_comment WHERE idea_id=' . $line['id'] . ' and comment_id = -1 ORDER BY id LIMIT 4';
                                $result_comments = pg_query($db, $query_comments) or die('Ошибка запроса: ' . pg_last_error());

                                ?>ча

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
                                                                <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-size: 25px;font-weight: 400;color: #14213D;">
                                                                    <?= $author['middle_name'] ?> <?= $author['first_name'] ?>
                                                                </li>
                                                                <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-size: 20px;font-style: normal;font-weight: 400;color: #000000;">
                                                                    <?= $comment['description'] ?>
                                                                </li>
                                                                <li class="list-group-item" id="commsBtns<?= $comment['id'] ?>" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;">
                                                                    <button type="button" class="btn btn-outline-secondary" onclick="changeSendBtnAttrToReply(<?= $line['id'] ?>,'<?= $user_data['first_name'] ?>','<?= $user_data['middle_name'] ?>','<?= $user_data['last_name'] ?>','<?= $user_data['id'] ?>','<?= $user_data['login'] ?>','<?= $author['middle_name'] ?>','<?= $author['first_name'] ?>', <?= $comment['id'] ?>)" style="border-color: white;font-size: 20px; font-family: Ubuntu;">Ответить</button>
                                                                    <?php
                                                                    if ($count_reply_comment['count'] > 0) {
                                                                    ?>
                                                                        <button type="button" class="btn btn-outline-secondary" onclick="showReplyComments(this, <?= $comment['id'] ?>)" style="border-color: white;font-size: 20px; font-family: Ubuntu;">Показать ответы</button>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </li>

                                                            </ul>

                                                        </div>
                                                    </div>

                                                </div>


                                                <div class="col-auto d-flex" style="align-items: center;">
                                                    <p style="font-family: 'Ubuntu';font-size: 16px;font-style: normal;font-weight: 400;line-height: 23px;color: #D9D9D9;padding: 2rem;">
                                                        <?= date('d.m.Y H:i:s', strtotime($comment['created'])); ?>
                                                    </p>
                                                </div>
                                                <div class="d-none" id="reply_comments<?= $comment['id'] ?>">
                                                    <?php
                                                    $reply_comment_query = pg_query($db, 'SELECT * FROM public.inc_comment WHERE comment_id = ' . $comment['id']) or die('Ошибка запроса: ' . pg_last_error());
                                                    while ($reply_comment = pg_fetch_array($reply_comment_query, null, PGSQL_ASSOC)) {
                                                    ?>
                                                        <div class="row" style="margin-top: 1rem;">
                                                            <div class="col-8" style="padding-left: 4rem;">
                                                                <div class="row">
                                                                    <div class="col-auto">

                                                                        <img src="<?= $author_reply_profile_res['image'] ?>" class="d-inline-block align-top" style="object-fit: cover;width: 60px;height: 60px; border-radius: 40px; margin-left: 1.25rem;" alt="">

                                                                    </div>
                                                                    <div class="col-8">
                                                                        <ul class="list-group">
                                                                            <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-size: 25px;font-style: normal;font-weight: 400;color: #14213D;">
                                                                                <?= $author['middle_name'] ?> <?= $author['first_name'] ?>
                                                                            </li>
                                                                            <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;">
                                                                                <?= $reply_comment['description'] ?>
                                                                            </li>
                                                                            <li class="list-group-item" style="border-color: white;font-family: 'Ubuntu';font-style: normal;font-weight: 400;color: #000000;">
                                                                                <button type="button" class="btn btn-outline-secondary" onclick="changeSendBtnAttrToReply(<?= $line['id'] ?>,'<?= $user_data['first_name'] ?>','<?= $user_data['middle_name'] ?>','<?= $user_data['last_name'] ?>','<?= $user_data['id'] ?>','<?= $user_data['login'] ?>','<?= $author['middle_name'] ?>','<?= $author['first_name'] ?>', <?= $comment['id'] ?>)" style="border-color: white; font-family: Ubuntu;">Ответить</button>
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



                                <div id="showMoreDiv<?= $line['id'] ?>" style="padding: .5rem 0 0 5rem;">
                                    <form action="ideaView.php" method="post">
                                        <button type="submit" id="btn-add-two-more<?= $line['id'] ?>" name="idea" class="btn btn-outline-secondary" value="<?= $line['id'] ?>" style="border-color: white; font-family: Ubuntu;">Показать подробнее</button>
                                    </form>
                                </div>



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
                                                        <button type="button" class="btn-send" id="btnSendComment<?= $line['id'] ?>" onclick="pushComment(<?= $line['id'] ?>,'<?= $user_data['first_name'] ?>','<?= $user_data['middle_name'] ?>','<?= $user_data['last_name'] ?>','<?= $user_data['id'] ?>','<?= $user_data['login'] ?>', null, null, -1)" style="height:3rem; width: 40px; border: 2px solid white;border-right-width: 0px; background-color:white; border-left-width: 0px; padding-right: 2rem;">
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
                            <?php }
                            if ($line['status'] == 1 && $au->isAdmin($_SESSION['hash'])) {
                            ?>

                                <div class="row" style="margin:1rem 0 1rem 0;">

                                    <div class="btn-group dropend">

                                        <div class="col-auto" style="margin-left: 2rem;">
                                            <button type="button" class="btn-edit-idea" style="padding: .25rem .5rem .25rem .5rem;" data-bs-toggle="modal" data-bs-target="#acceptIdeaBackdrop<?= $line['id'] ?>">Принять</button>

                                        </div>
                                        <div class="col-auto" style="margin-right: auto; margin-left:1rem;">
                                            <button type="button" class="btn-delete-idea" style="padding: .25rem .5rem .25rem .5rem; font-size: 14px;" data-bs-toggle="modal" data-bs-target="#denyIdeaBackdrop<?= $line['id'] ?>">Отклонить</button>
                                        </div>

                                        <!-- Modal deny-->
                                        <div class="modal fade" id="denyIdeaBackdrop<?= $line['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                            <div class="modal-dialog  modal-lg">
                                                <div class="modal-content" style="border-radius: 20px;">
                                                    <div class="modal-header" style="background-color: #FF6B6B; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                                                        <div class="col-auto" style="margin-left: 1rem;">
                                                            <p style="font-family: 'Ubuntu';font-style: italic;font-weight: 400;font-size: 16px;line-height: 24px;color: #FFFFFF;">Отклонить идею</p>
                                                        </div>
                                                        <button type="button" class="btn-close btn-close-white" id="closeModalbtnDeny<?= $line['id'] ?>" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row" style="margin: 3rem 1rem 3rem 1rem;">
                                                            <div class="row">
                                                                <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 16px;line-height: 37px;color: #000000;"><?= $line['title'] ?></p>
                                                            </div>
                                                            <div class="row">
                                                                <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 16px;line-height: 24px;color: #000000;"><?= $line['description'] ?></p>
                                                            </div>
                                                        </div>
                                                        <div class="row justify-content-center" style="margin: 3rem 0 3rem 0;">
                                                            <div class="col-auto">
                                                                <button type="button" class="btn-delete-idea" onclick="denyIdea(<?= $line['id'] ?>)" style="padding: .25rem 1rem .25rem 1rem;">Отклонить</button>
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
                            <?php }
                            if (($line['status'] == 4 || $line['status'] == 6) && $au->isAdmin($_SESSION['hash'])) {
                                $result_executers = pg_query($db, "SELECT * FROM public.inc_executors WHERE idea_id =" . $line['id'] . " and role = 3;");
                                $result_current_executers = pg_query($db, "SELECT * FROM public.inc_executors WHERE idea_id =" . $line['id'] . " and (role = 2 or role = 1);");
                                $idx = array();
                                $stedent_id = array();
                                $i = 0;
                                while ($line_current_executers = pg_fetch_array($result_current_executers, null, PGSQL_ASSOC)) {
                                    array_push($idx, $i);
                                    array_push($stedent_id, $line_current_executers['user_id']);
                                    $i++;
                                }
                            ?>

                                <div class="row" style="margin:1rem 0 1rem 0;">

                                    <div class="btn-group dropend">

                                        <?php
                                        if ($line['status'] == 4) {
                                        ?>
                                            <div class="col-auto" style="margin-right: 2rem; margin-left: auto;">
                                                <button type="button" class="btn-edit-idea" style="padding: .25rem .5rem .25rem .5rem;" data-bs-toggle="modal" data-bs-target="#makeTeaBackdrop<?= $line['id'] ?>">Добавить команду</button>

                                            </div>
                                        <?php
                                        }
                                        ?>
                                        <!-- Modal deny-->
                                        <div class="modal fade" id="makeTeaBackdrop<?= $line['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                            <div class="modal-dialog  modal-lg">
                                                <div class="modal-content" style="border-radius: 20px;">
                                                    <div class="modal-header" style="background-color: #006D77; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                                                        <div class="col-auto" style="margin-left: 1rem;">
                                                            <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 16px;line-height: 37px;color: white;">Команда</p>
                                                        </div>
                                                        <button type="button" class="btn-close btn-close-white" id="closeModalbtnAccept<?= $line['id'] ?>" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <div class="row justify-content-between d-flex align-items-center" id="teamList<?= $line['id'] ?>" style="margin: 3rem 1rem 1rem 1rem;">
                                                            <div class="col-4">
                                                                <div style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 14px;color: #000000;">Команда</div>
                                                                <script>
                                                                    $(document).ready(function() {
                                                                        addToTeam(<?= $line['id'] ?>,[
                                                                            <?php
                                                                            foreach ($idx as &$value) {
                                                                                print_r($value . ",");
                                                                            }
                                                                            ?>], [<?php
                                                                            foreach ($stedent_id as &$value) {
                                                                                print_r($value . ",");
                                                                            }
                                                                            ?>]);
                                                                    });
                                                                </script>
                                                            </div>
                                                            <div class="col-5 d-flex justify-content-center">
                                                                <button type="button" class="btn-edit-idea" style="border-color: white; font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 14px;">Сделать всех главными</button>
                                                            </div>
                                                            <div class="col-3">
                                                                <button type="button" class="btn-delete-idea" onclick="removeFromTeamAll(<?= $line['id'] ?>)" style="border-color: white; font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 14px;">Удалить всех</button>
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row justify-content-between d-flex align-items-center" style="margin: 3rem 1rem 1rem 1rem;">
                                                            <div class="col-4">
                                                                <div style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 14px;color: #000000;">Список желающих</div>
                                                            </div>
                                                            <div class="col-5 d-flex justify-content-center">
                                                                <button type="button" class="btn-edit-idea" onclick="addAllFromWishlist(<?= $line['id'] ?>)" style="border-color: white; font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 14px;">Принять всех</button>
                                                            </div>
                                                            <div class="col-3">
                                                                <button type="button" class="btn-delete-idea" onclick="removeFromWishListAll(<?= $line['id'] ?>)" style="border-color: white; font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 14px;">Отклонить всех</button>
                                                            </div>
                                                        </div>
                                                        <div class="row justify-content-center" style="margin: 3rem 1rem 1rem 1rem;">


                                                            <div class="row justify-content-between d-flex align-items-center" id="student_list<?= $line['id'] ?>" style=" margin: 0rem 1rem 1rem 0rem;">
                                                                <?php
                                                                $cur_id = 0;
                                                                $allIdInput = array();
                                                                while ($line_executers = pg_fetch_array($result_executers, null, PGSQL_ASSOC)) {
                                                                    $result_user = pg_query($db, "SELECT * FROM public.students WHERE id = " . $line_executers['user_id']);
                                                                    $line_user = pg_fetch_assoc($result_user);

                                                                    if ($line['author'] == $line_executers['user_id']) {
                                                                        $is_leader = " list-group-item-primary";
                                                                        $is_leader_status = " Предложил заявку";
                                                                    } else {
                                                                        $is_leader = "";
                                                                        $is_leader_status = "";
                                                                    }
                                                                ?>
                                                                    <div class="row" id="exuters_list<?= $cur_id ?>Idea<?= $line['id'] ?>" name='exutersList<?= $line['id'] ?>' style="margin: 1rem;">
                                                                        <div class="col-6">
                                                                            <div class="row" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 14px;">
                                                                                <div class="col-3">
                                                                                    <img src="assets/images/ffde09eee51c3f288d1ebf5cef6b5c600cf31a3f.jpeg" class="d-inline-block align-top" style="object-fit: cover;width: 50px;height: 50px; border-radius: 40px;" alt="">
                                                                                </div>
                                                                                <div class="col">
                                                                                    <ul class="list-group">
                                                                                        <li class="list-group-item" style="border-color: white;">
                                                                                            <?= $line_user['first_name'] ?> <?= $line_user['middle_name'] ?>
                                                                                        </li>
                                                                                        <li class="list-group-item" style="border-color: white;">
                                                                                            gryppa
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-3">
                                                                            <button type="button" name="addToteamBtns<?= $line['id'] ?>" class="btn-edit-idea" id="addToteamBtn<?= $cur_id ?>Idea<?= $line['id'] ?>" onclick="addToTeam(<?= $line['id'] ?>, [<?= $cur_id ?>], [<?= $line_executers['user_id'] ?>], true)" style="border-color: white; font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 14px;">Принять</button>
                                                                        </div>

                                                                        <div class="col-3">
                                                                            <button type="button" class="btn-delete-idea" onclick="removeFromWishlist(<?= $line['id'] ?>, <?= $cur_id ?>)" style="border-color: white; font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 14px;">Отклонить</button>
                                                                        </div>
                                                                        <input class="form-check-input d-none" type="checkbox" name="wishSwitch<?= $line['id'] ?>" value="<?= $line_executers['user_id'] ?>" id="wishStudent<?= $cur_id ?>Idea<?= $line['id'] ?>">
                                                                    </div>

                                                                <?php
                                                                    $cur_id++;
                                                                    array_push($allIdInput,  $line_executers['user_id']);
                                                                } ?>
                                                                <input hidden id="allIdInput<?= $line['id'] ?>" value="
                                                                <?php
                                                                foreach ($allIdInput as &$value) {
                                                                    print_r($value . ",");
                                                                }
                                                                ?>">
                                                            </div>
                                                            <hr>
                                                            <div style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 14px;color: #000000; margin:1rem 0 1rem 0;">Добавить команду</div>

                                                            <select class="form-select" id="groupSelect<?= $line['id'] ?>" aria-label="Default select example" style="width: 80%;" onchange="selectStudentsFromGroups(<?= $line['id'] ?>)">
                                                                <option selected value="0">Выберите группу</option>
                                                                <?php $result_groups = pg_query($db, 'SELECT name, id FROM public."groups";');
                                                                $id_group = 0;
                                                                while ($line_groups = pg_fetch_array($result_groups, null, PGSQL_ASSOC)) {

                                                                ?>

                                                                    <option id="option_select<?= $line_groups['id'] ?>" value="<?= $line_groups['id'] ?>"><?= $line_groups['name'] ?></option>
                                                                <?php
                                                                    $id_group = $line_groups['id'];
                                                                } ?>
                                                            </select>


                                                            <div id="add_div_student<?= $line['id'] ?>" hidden style="margin-top: 1rem; width: 80%;">
                                                                <div class="w-100">
                                                                    <ul class="list-group" id="student_add_list<?= $line['id'] ?>" style="overflow-y: scroll; max-height: 120px;">
                                                                    </ul>
                                                                </div>
                                                                <div class="row justify-content-center" style="margin-top: 1rem;">
                                                                    <div class="col-auto">
                                                                        <div class="form-check form-switch">
                                                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefaultAll<?= $line['id'] ?>" onchange="chooseAll(<?= $line['id'] ?>)">
                                                                            <label class="form-check-label" for="flexSwitchCheckDefaultAll">Выбрать всех</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row justify-content-center">
                                                            <div class="col-3">
                                                                <ul class="list-group">
                                                                    <li class="list-group-item" style="border-color: white; margin: 0 auto 0 auto;">
                                                                        <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 16px;color: #BCBCBC;">Начало исполнения</p>
                                                                    </li>
                                                                    <li class="list-group-item" style="border-color: white; margin: 0 auto 0 auto;">
                                                                        <input type="date" class="form-control" id="start_freetry_field<?= $line['id'] ?>">
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="col-3">
                                                                <ul class="list-group">
                                                                    <li class="list-group-item" style="border-color: white; margin: 0 auto 0 auto;">
                                                                        <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 16px;color: #BCBCBC;">Конец исполнения</p>
                                                                    </li>
                                                                    <li class="list-group-item" style="border-color: white; margin: 0 auto 0 auto;">
                                                                        <input type="date" class="form-control" id="end_freetry_field<?= $line['id'] ?>">
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="row justify-content-center d-none" id="acceptErr<?= $line['id'] ?>">
                                                            <div class="col-auto">
                                                                <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 13px;line-height: 16px;color: red;">Неправильно поставлена дата</p>
                                                            </div>
                                                        </div>
                                                        <div class="row justify-content-center d-none" id="leaderErr<?= $line['id'] ?>">
                                                            <div class="col-auto">
                                                                <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 13px;line-height: 16px;color: red;">Не выбран лидер</p>
                                                            </div>
                                                        </div>
                                                        <div class="row justify-content-center d-none" id="denyErr<?= $line['id'] ?>">
                                                            <div class="col-auto">
                                                                <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 13px;line-height: 16px;color: red;">Не выбраны исполнители</p>
                                                            </div>
                                                        </div>
                                                        <div class="row justify-content-center" style="margin: 3rem 0 3rem 0;">
                                                            <div class="col-auto">
                                                                <button type="button" class="btn-edit-idea" onclick="submitExecuters(<?= $line['id'] ?>)" style="padding: .25rem 1rem .25rem 1rem;">Принять</button>
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

                            <?php }
                            ?>
                            <div class="row" style="margin:1rem 0 1rem 0;">
                                <div class="col-auto" style="margin-left: 2.5rem;">
                                    <div class="row" name="tagIdeaRow">
                                        <?php
                                        $tagsQuery = pg_query($db, "SELECT * FROM public.inc_idea_tag WHERE idea_id = " . $line['id'] . ";");
                                        while ($tag = pg_fetch_array($tagsQuery, null, PGSQL_ASSOC)) {
                                        ?>
                                            <div class="col-auto" style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 13px;line-height: 16px;">
                                                #<?= $tag['tag'] ?>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>

                                <div class="col-auto" style="margin-right: 2rem; margin-left: auto;">
                                    <?php

                                    if ($user) {
                                        $isExecuterQuery = pg_query($db, "SELECT * FROM public.inc_executors WHERE idea_id = " . $line['id'] . " and user_id = " . $user_data['id'] . ";");
                                        if ($isExecuterQuery) {
                                            $isExecuterRes = pg_fetch_array($isExecuterQuery, null, PGSQL_ASSOC);
                                        }
                                        if (($line['status'] == 6 || $line['status'] == 3 || $line['status'] == 4 || $line['status'] == 1) && $isExecuterQuery && $isExecuterRes['role'] == 0) {
                                    ?>

                                            <button type="button" class="btn-edit-idea" id="wantInTeamBtn<?= $line['id'] ?>" style="padding: .25rem .5rem .25rem .5rem;" onclick="addUserWhichWantedInTeam(<?= $line['id'] ?>, <?= $au->getUserId($_SESSION['hash']) ?>)">Хочу в команду</button>



                                        <?php } else {
                                        ?>

                                            <button type="button" class="btn-login-out" id="wantInTeamBtn<?= $line['id'] ?>" style="padding: .25rem .5rem .25rem .5rem;" onclick="DBinsetExecutor(<?= $line['id'] ?>, <?= $au->getUserId($_SESSION['hash']) ?>, 0)">Уйти из команды</button>


                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                            if ($line['status'] == 6 && $au->isAdmin($_SESSION['hash'])) {
                            ?>
                                <div class="row" style="margin:1rem 0 1rem 0;">
                                    <div class="col-auto" style="margin-right: 2rem; margin-left: auto;">
                                        <button type="button" class="btn-login" style="padding: .25rem .5rem .25rem .5rem; font-size: 14px; border-radius: 20px;" data-bs-toggle="modal" data-bs-target="#freetryIdeaBackdrop<?= $line['id'] ?>">Закончить выполнение</button>
                                    </div>
                                </div>
                                <div class="modal fade" id="freetryIdeaBackdrop<?= $line['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog  modal-lg">
                                        <div class="modal-content" style="border-radius: 20px;">
                                            <div class="modal-header" style="background-color: #006D77; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                                                <div class="col-auto" style="margin-left: 1rem;">
                                                    <p style="font-family: 'Ubuntu';font-style: italic;font-weight: 400;font-size: 16px;line-height: 24px;color: #FFFFFF;">Выполнена ли идея?</p>
                                                </div>
                                                <button type="button" class="btn-close btn-close-white" id="closeModalbtnDeny<?= $line['id'] ?>" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row" style="margin: 3rem 1rem 3rem 1rem;">
                                                    <div class="row">
                                                        <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 16px;line-height: 37px;color: #000000;"><?= $line['title'] ?></p>
                                                    </div>
                                                    <div class="row">
                                                        <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 16px;line-height: 24px;color: #000000;"><?= $line['description'] ?></p>
                                                    </div>
                                                </div>
                                                <div class="row justify-content-center" style="margin: 3rem 0 3rem 0;">
                                                    <div class="col-auto">
                                                        <button type="button" class="btn-edit-idea" onclick="passedFreetryIdea(<?= $line['id'] ?>)" style="padding: .25rem 1rem .25rem 1rem;">Выполнена</button>
                                                    </div>
                                                    <div class="col-auto">
                                                        <button type="button" class="btn-login-out" onclick="notPassedFreetryIdea(<?= $line['id'] ?>)" style="padding: .25rem 1rem .25rem 1rem; font-size: 16px;">Не выполнена</button>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>

                            <!-- Modal accept-->
                            <div class="modal fade" id="acceptIdeaBackdrop<?= $line['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog  modal-lg">
                                    <div class="modal-content" style="border-radius: 20px;">
                                        <div class="modal-header" style="background-color: #006D77; border-top-left-radius: 20px; border-top-right-radius: 20px;">
                                            <div class="col-auto" style="margin-left: 1rem;">
                                                <p style="font-family: 'Ubuntu';font-style: italic;font-weight: 400;font-size: 16px;line-height: 24px;color: #FFFFFF;">Принять идею</p>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white" id="closeModalbtnAccept<?= $line['id'] ?>" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row" style="margin: 3rem 1rem 1rem 1rem;">

                                                <input class="inputIdeaTitle" id="newIdeaInputTitle<?= $line['id'] ?>" type="text" placeholder="Введите название идеи..." value="<?= $line['title'] ?>" style="width: 70%; height:3rem; line-height: 1.5;background-color: #fff;background-clip: padding-box;border: 2px solid white; outline:none; color: #D9D9D9; font-size: 20px;" maxlength="23">


                                                <textarea class="form-control" id="newIdeaInputTitleTextarea<?= $line['id'] ?>" placeholder="Измените вашу идею" rows="3" maxlength="1000" oninput="resizeTextarea(this)" style="width: 100%;height: 100px; border: none; resize: none; margin-left: 0.5rem;"><?= $line['description'] ?></textarea>

                                            </div>
                                            <div id="preShowImage<?= $line['id'] ?>" class="row justify-content-center ">
                                                <div class="col-auto" id="deleteImgBtn<?= $line['id'] ?>" style="margin-left: auto; margin-right: 2rem;">
                                                    <button type="button" class="btn-delete-idea" onclick="deleteImageFromEditInputs(<?= $line['id'] ?>)" style="padding: .1rem .5rem .25rem .5rem;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z" />
                                                        </svg>
                                                    </button>
                                                </div>

                                                <div class="idea-image" style="padding: 1rem 5% 2rem 5%;">
                                                    <img class="card-img-top" id="newIdeaPreShowImage<?= $line['id'] ?>" style="width: 100%;height: 40vh;object-fit: cover;" src="<?= $line['image'] ?>" value="1" alt="Card image cap">
                                                    <input hidden id="newIdeaPreShowImageBool<?= $line['id'] ?>" value="1">
                                                </div>
                                            </div>
                                            <div class="row" id="hideEditBtn1" style="margin-bottom: 1rem;">
                                                <div class="col-auto">
                                                    <button type="button" class="btn-input" id="emojiBtn" onclick="smileTrigger(event)" style="margin-left: 3rem">
                                                        <svg xmlns=" http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-emoji-smile" viewBox="0 0 16 16" style="color: #BCBCBC;">
                                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"></path>
                                                            <path d="M4.285 9.567a.5.5 0 0 1 .683.183A3.498 3.498 0 0 0 8 11.5a3.498 3.498 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.498 4.498 0 0 1 8 12.5a4.498 4.498 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683zM7 6.5C7 7.328 6.552 8 6 8s-1-.672-1-1.5S5.448 5 6 5s1 .672 1 1.5zm4 0c0 .828-.448 1.5-1 1.5s-1-.672-1-1.5S9.448 5 10 5s1 .672 1 1.5z"></path>
                                                        </svg>
                                                    </button>

                                                </div>
                                                <div class="col-auto" style="margin-right: 2.25rem;" value="0">
                                                    <input class="imageControl " type="file" name="file" id="formFile<?= $line['id'] ?>" accept=".jpg, .jpeg, .png" onchange="changeImagePreShow(<?= $line['id'] ?>)" required="" hidden="">
                                                    <button type="button" class="btn-input" onclick="inputImageTrigger(<?= $line['id'] ?>)">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-card-image" viewBox="0 0 16 16" style="color: #BCBCBC;">
                                                            <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"></path>
                                                            <path d="M1.5 2A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13zm13 1a.5.5 0 0 1 .5.5v6l-3.775-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12v.54A.505.505 0 0 1 1 12.5v-9a.5.5 0 0 1 .5-.5h13z"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row" id="addedTags<?= $line['id'] ?>">
                                                <?php
                                                $tagInIdea = pg_query($db, "SELECT * FROM public.inc_idea_tag WHERE idea_id = " . $line['id'] . ";");
                                                $tagIdx = 0;
                                                while ($line_tag = pg_fetch_array($tagInIdea, null, PGSQL_ASSOC)) {
                                                ?>
                                                    <div class="col-auto" style="margin: 0 1rem 0 3rem;" id="idea<?= $line['id'] ?>Tag<?= $tagIdx ?>">
                                                        <div class="row d-flex" style="align-items: center;">
                                                            <div class="col-auto" style="padding: 0 0.25rem 0 0;">
                                                                <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 24px;color: #000000; margin: 0;" id="tagName<?= $tagIdx ?>"><?= $line_tag['tag'] ?></p>
                                                            </div>
                                                            <div class="col-auto" style="padding: 0;">
                                                                <button type="button" class="btn-delete-tag" name="deleteBtnTag<?= $line['id'] ?>" style="padding: 0;" onclick="deleteAddSuggestTag(<?= $tagIdx++ ?>, <?= $line['id'] ?>)">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="row d-none" id="errs<?= $line['id'] ?>">
                                                <div class="col-auto" style="margin-left: 3rem; color: red;font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 13px;">
                                                    Такой тег уже добавлен!
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="input-group" style="margin: 1rem 3rem 0 2rem; width: 90%; font-family: 'Ubuntu';font-style: normal;">
                                                    <input type="text" class="form-control" placeholder="Тег, например, C++" aria-label="Recipient's username" aria-describedby="button-addon2" id="tagsSearchInput<?= $line['id'] ?>" oninput="suggestTag(this, <?= $line['id'] ?>)">
                                                    <button class="btn btn-outline-secondary" type="button" id="button-addon2" style="border-color: #BCBCBC;" onclick="addSuggestTag(<?= $line['id'] ?>)">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16" style="color: #006D77;">
                                                            <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z" />
                                                        </svg>
                                                    </button>
                                                </div>

                                                <div style="border: 0.25px solid #BCBCBC;margin: 0 1rem 0 3rem;width: 81%; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;font-family: 'Ubuntu';font-style: normal;" id="suggestTagsDiv<?= $line['id'] ?>" class="d-none">
                                                    <ul class="list-group" id="suggestTags<?= $line['id'] ?>">

                                                    </ul>
                                                </div>
                                            </div>
                                            <hr>
                                            <?php if ($line['status'] != 1) {
                                            ?>
                                                <div class="row justify-content-center">

                                                    <p style=" font-family: 'Ubuntu' ;font-style: normal;font-weight: 400;font-size: 16px;line-height: 24px;color: #000000; margin-left: 4rem;">Выберите статус идеи</p>
                                                    <select class="form-select" id="statusSelect<?= $line['id'] ?>" aria-label="Default select example" style="width: 90%; margin: 1rem 3rem 2rem 2rem;" onchange="getStatusFromSelect(<?= $line['id'] ?>)">
                                                        <option selected="" value="0" selected>Выберите статус идеи</option>
                                                        <option id="option_select1" value="3">На голосовании</option>
                                                        <option id="option_select1" value="7">Выполнена</option>
                                                        <option id="option_select2" value="8">Не выполнена</option>
                                                    </select>

                                                </div>
                                            <?php } ?>
                                            <div class="row justify-content-center" id="voteTime<?= $line['id'] ?>">
                                                <div class="col-3">
                                                    <ul class="list-group">
                                                        <li class="list-group-item" style="border-color: white; margin: 0 auto 0 auto;">
                                                            <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 16px;color: #BCBCBC;">Начало голосования</p>
                                                        </li>
                                                        <li class="list-group-item" style="border-color: white; margin: 0 auto 0 auto;">
                                                            <input type="date" class="form-control" id="start_vote_field<?= $line['id'] ?>">
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col-3">
                                                    <ul class="list-group">
                                                        <li class="list-group-item" style="border-color: white; margin: 0 auto 0 auto;">
                                                            <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 16px;color: #BCBCBC;">Конец голосования</p>
                                                        </li>
                                                        <li class="list-group-item" style="border-color: white; margin: 0 auto 0 auto;">
                                                            <input type="date" class="form-control" id="end_vote_field<?= $line['id'] ?>">
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center d-none" id="acceptErr<?= $line['id'] ?>">
                                                <div class="col-auto">
                                                    <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 13px;line-height: 16px;color: red;">Неправильно поставлена дата</p>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center" style="margin: 3rem 0 3rem 0;">
                                                <div class="col-auto">
                                                    <?php if ($line['status'] == 1) {
                                                    ?>
                                                        <button type="button" class="btn-edit-idea" onclick="adminAddEditIdea(<?= $line['id'] ?>)" style="padding: .25rem 1rem .25rem 1rem;">Принять</button>
                                                    <?php
                                                    } else { ?>
                                                        <button type="button" class="btn-edit-idea" onclick="updateIdea(<?= $line['id'] ?>)" style="padding: .25rem 1rem .25rem 1rem;">Обновить</button>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-auto">
                                                    <button type="button" class="btn-login-out" onclick="triggerModalExit(<?= $line['id'] ?>)" style="padding: .25rem 1rem .25rem 1rem; font-size: 16px;">Отмена</button>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="w-100" id="w-100" style="margin-left: 2.25rem; margin-top: 1rem;  font-family: 'Ubuntu';font-style: normal;font-weight: 400;font-size: 14px;line-height: 18px;">
                                            <div id="titleErr<?= $line['id'] ?>" class="d-none" style="color: red;">
                                                Неправильно введен заголовок
                                            </div>
                                            <div id="descrErr<?= $line['id'] ?>" class="d-none" style="color: red;">
                                                Неправильно введено описание
                                            </div>
                                            <div id="fileErr<?= $line['id'] ?>" class="d-none" style="color: red;">

                                            </div>
                                            <div id="successErr<?= $line['id'] ?>" class="d-none" style="color: green;">
                                                Идея добавлена!
                                            </div>
                                            <div id="statussErr<?= $line['id'] ?>" class="d-none" style="color: red;">
                                                Выберите статус!
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <div class="col-3 offset-md-5">
                    <div class="card" style="border-radius: 20px;">
                        <div class="row justify-content-center">
                            <div class="col-auto" style="text-align: center;">
                                <p style="font-family: 'Ubuntu';font-style: normal;font-weight: 700;font-size: 30px;padding-top: 1rem;">Категории</p>
                            </div>

                        </div>
                        <div class="row justify-content-center">
                            <div class="col-auto" style="color:#ced4da;font-family: 'Ubuntu';font-style: normal;font-size: 20px;font-weight: 400; padding-left: 2rem;
                            padding-right: 2rem; padding-bottom: 1rem;">
                                <select class="form-select" aria-label="Default select example">
                                    <option onclick="chooseCategory(this)" selected>выбрать категорию</option>
                                    <?php
                                    $tagsQuery = pg_query($db, "SELECT DISTINCT tag FROM public.inc_idea_tag;");
                                    $curTagIdx = 0;

                                    while ($tag = pg_fetch_array($tagsQuery, null, PGSQL_ASSOC)) {
                                    ?>
                                        <option class="option-inc" style=";font-family: 'Ubuntu';font-style: normal;font-size: 20px;font-weight: 400;" onclick="chooseCategory(this)" value="<?= $curTagIdx++ ?>"><?= $tag['tag'] ?></option>

                                    <?php
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-6" style="text-align: center; margin-top: 2rem;">
                    <p style="color: #ced4da;">Упс, кажется вы просмотрели все идеи :(<br>
                        Не желаете добавить новую?
                    </p>
                    <button type="button" class="btn-to-start" id="btn-to-start" onclick="scrollpageUp()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-arrow-up-circle" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-7.5 3.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V11.5z" />
                        </svg>
                    </button>
                    <p style="color:#FCA311;">В начало</p>
                </div>

            </div>
            <div class="ctmNavIdeaMenuDis" id="navIdea" style="max-width: 1000px;>
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

    </main>

</body>