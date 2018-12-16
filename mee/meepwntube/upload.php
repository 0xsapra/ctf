<!DOCTYPE html>
<html lang="en">

<head>
    <title>MeePwnTube</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <style type="text/css">
        .icon-action p {
            display: inline;
            margin: 0 20px;
        }

        .hidden-overflow {
            white-space: nowrap;
            overflow: hidden !important;
            text-overflow: ellipsis;
        }
    </style>
    <link rel="shortcut icon" href="images/meepwntube.png">
    <html lang="en">

    <style>
    form {
        border: 3px solid #f1f1f1;
    }

    input[type=text], input[type=password] {
        width: 100%;
        padding: 12px 20px;
        margin: 8px 0;
        display: inline-block;
        border: 1px solid #ccc;
        box-sizing: border-box;
    }

    button {
        background-color: #4CAF50;
        color: white;
        padding: 14px 20px;
        margin: 8px 0;
        border: none;
        cursor: pointer;
        width: 100%;
    }

    .cancelbtn {
        width: auto;
        padding: 10px 18px;
        background-color: #f44336;
    }

    .imgcontainer {
        text-align: center;
        margin: 24px 0 12px 0;
    }

    img.avatar {
        width: 40%;
        border-radius: 50%;
    }

    .container {
        padding: 16px;
    }

    span.keypass {
        float: right;
        padding-top: 16px;
    }
    @media screen and (max-width: 300px) {
        span.keypass {
           display: block;
           float: none;
        }
        .cancelbtn {
           width: 100%;
        }
    }
    </style>

    <style>
        body {
        background: url("images/background.jpg") no-repeat center center fixed;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
        }
    </style>
    <meta http-equiv="refresh" content="5;url=http://meepwntube.0x1337.space/account.php" />
</head>

<?php
    require_once('dbconnect.php');
    require_once('auth.php');
    require_once('lib.php');
    require_once('_ducnt.php');
    ini_set("display_errors", 0);
    ini_set("session.cookie_httponly", 1);
    ob_start();
    session_start();
    
    if ($_auth === false) {
        header("Location: index.php");
        exit;
    }

    $secret_path = gen_secret_path($username);
    mkdir($secret_path,0777,true);
    $filename  = basename($_FILES["fileToUpload"]["name"]);
    $extension = pathinfo($filename, PATHINFO_EXTENSION);

    $avatar = trim(miniwaf($filename),".".$extension);
    $avatar = watermark($avatar).'.'.$extension;
    $target_file = $secret_path . $avatar;

    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    if(isset($_POST["submit"])) {
            if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
                $secret = '6LcS_10UAAAAAC0vehLXotI7pJCmFGuFfU2L933Z';
                $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
                $responseData = json_decode($verifyResponse);
                if($responseData->success){
                    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                    if($check !== false) {
                        $uploadOk = 1;
                    }
                    else{
                        echo "<center>File is not an image.</center>";
                        $uploadOk = 0;
                    }
                }
                else{
                    die("<center>Robot verification failed, please try again my dude. The page auto refresh after 5 second</center>");
                }
            }
            else{
                die("<center>Please click on the reCAPTCHA box my dude. The page auto refresh after 5 second</center>");
            }
    }

    if (file_exists($target_file)) {
        echo "<center><b><p style='font-size:15px; color:#cc0066'>Sorry, file already exists. The page auto refresh after 5 second</p></b></center>";
        $uploadOk = 0;
    }
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "<center><b><p style='font-size:15px; color:#cc0066'>Sorry, your file is too large. The page auto refresh after 5 second</p></b></center>";
        $uploadOk = 0;
    }
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "<center><b><p style='font-size:15px; color:#cc0066'>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</p></b></center>";
        $uploadOk = 0;
    }
    if ($uploadOk == 0) {
        echo "<center><b><p style='font-size:15px; color:#cc0066'>Sorry, your file was not uploaded. The page auto refresh after 5 second</p></b></center>";
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "<center><b><p style='font-size:20px; color:#ff5050'>Your file has been uploaded.</p></b></center>";
            $avatar = SERVER_ROOT.$secret_path.$avatar;
            $avatar = mysqli_real_escape_string($conn,$avatar);
            echo "<center><b><p style='font-size:20px; color:#ff5050'>Your avatar: ".$avatar."</p></b></center>";
            $store_avatar_url = mysqli_query($conn,"UPDATE users SET avatar_name = '$avatar' WHERE username='$username'");
            echo "<center><b><p style='font-size:15px; color:#cc0066'>The page auto refresh after 5 second</p></b></center>";

        } else {
            echo "<center><b><p style='font-size:15px; color:#cc0066'>Sorry, there was an error uploading your file. The page auto refresh after 5 second</p></b></center>";
        }
    }

?>

