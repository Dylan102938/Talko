<?php
    session_start();
    require ("conn.php");
    mb_internal_encoding('UTF-8');
    mb_http_output('UTF-8');
    mb_http_input('UTF-8');
    mysqli_set_charset($conn, "utf8");

    $active = "upload";

    if (isset($_SESSION['uploaded'])) {
        if ($_SESSION['uploaded']) {
            $active = "vocabulary";
            $_SESSION['uploaded'] = false;
        }
    }

    session_destroy();

    $path_filename_ext = "";
    $languages = file_get_contents("languages.json");
    $language = '';
    $ex = '';
    $tr = '';

    if($_POST) {
        $target_dir = "upload/";
        $file = $_FILES['image']['name'];
        $path = pathinfo($file);
        $filename = $path['filename'];
        $ext = $path['extension'];
        $temp_name = $_FILES['image']['tmp_name'];
        $path_filename_ext = $target_dir.$filename.".".$ext;
        move_uploaded_file($temp_name, $path_filename_ext);

        $language = $_POST['language'];
    }

    $query = "SELECT * FROM `vocab`";
    $vocab_pretty ="";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $example = explode("</p>", $row["ex"]);
            $translated = explode("</p>", $row["tr"]);
            $text2 = "";

            for ($i = 0; $i < count($example); $i++) {
                $text2 .= "<div class = 'examples' style = 'margin-bottom: 20px;'>".$example[$i].$translated[$i]."</div>";
            }

            $vocab_pretty .=
                '<div class = "vocab">
                    <div class = "display-image">
                        <img src = "' . $row["image"] . '" class = "image">
                    </div>
        
                    <div class = "text">
                        <p><span style = "color: #339AF0"> English: </span>' . $row["text"] . '</p>
                        <br>
                        <p><span style = "color: #339AF0">' . $row["language"] . ": </span>".$row["translation"]. '</p>
                    </div>
        
                    <div class = "text2">'.$text2.'</div>
                </div>';
        }
    }

    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        $link = "https";
    else
        $link = "http";

    $link .= "://";
    $link .= $_SERVER['HTTP_HOST'];
?>

<html>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <head>

        <title>Talko</title>

        <!--importing stylesheets-->
        <link rel = "stylesheet" href = "main.css">
        <link rel = "stylesheet" href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.css">
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Dosis:100,400,800&display=swap" rel="stylesheet">
        <script
            src="https://code.jquery.com/jquery-3.4.1.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
            crossorigin="anonymous"></script>

    </head>

    <body>

        <!--nav-->
        <div id = "header">
                <div class = "nav first">
                    <p class = "nav-text" name = "upload">
                        <i class="nav-icon far fa-images"></i>
                        UPLOAD
                    </p>
                </div>

                <div class = "nav">
                    <p class = "nav-text" name = "vocabulary">
                        <i class="nav-icon far fa-comment-alt"></i>
                        VOCABULARY
                    </p>
                </div>

                <div class = "nav">
                    <p class = "nav-text" name = "profile">
                        <i class="nav-icon fas fa-user-alt" style = "font-size: 28px"></i>
                        MY PROFILE
                    </p>
                </div>

            <div style = "display: none"><p id = "status"><?php echo $path_filename_ext; ?></p></div>
            <div style = "display: none"><p id = "languages"><?php echo $languages; ?></p></div>
            <div style = "display: none"><p id = "translate"><?php echo $language; ?></p></div>
            <div style = "display: none"><p id = "show"><?php echo $active; ?></p></div>
            <div style = "display: none"><p id  = "url"><?php echo $link; ?></p></div>
        </div>
        <!--end of nav-->

        <!--bodies-->
        <div id = "upload" class = "changeable">
            <div id = "image-upload" class = "upload-div">
                <div class = "container">
                    <form method = "POST" action = "index.php" enctype = "multipart/form-data">
                        <div id = "upload-wrapper">
                            <button id = "custom-upload">UPLOAD IMAGE</button>
                            <input type = "file" id = "image" class = "input"  name = "image" onchange = "previewFile()" required/>
                        </div>

                        <p><input type = "text" id = "language" class = "input" name = "language" placeholder = "Enter language" autocomplete = "off" required></p>

                        <div id = "box">
                            <img src = "" id = "myImg" class = "input" />
                        </div>

                        <p><input type = "submit" id = "submit" class = "input" name = "submit"  value = "SUBMIT" required></p>
                    </form>
                </div>
            </div>

            <div id = "image-load" class = "upload-div">
                <div class = "container" id = "labels"></div>
            </div>
            <div id = "translateText" style = "margin-top: 50px; height: 25.6px; width: calc(50% - 65px); max-width: calc(50% - 65px); float: right;"></div>
        </div>

        <div id = "vocabulary" class = "changeable">
            <div class = "container" id = "vocab-container">
                <?php echo $vocab_pretty; ?>
            </div>
        </div>

        <div id = "profile" class = "changeable">

        </div>
        <!--end of bodies-->

        <script src = "main.js"></script>
    </body>

</html>
