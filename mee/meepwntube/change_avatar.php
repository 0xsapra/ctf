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
        vertical-align: middle;
        width: 50px;
        height: 50px;
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
    <style>
        .avatar {
            vertical-align: middle;
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <header class="row" style="margin: 10px 0; box-shadow: 0 4px 2px -2px #f2f2f2;">
        <div class="container">
            
            <div class="col-md-2" style="margin: 10px 0;">
            <a href = "index.php"><img src="images/meepwntube.png" width="250px" height="150px" /></a>
            <div align = "center">
                <b><p><a href ="logout.php">Logout</a></p></b>
            </div>
            </div>
            <div align = "center"><b><p style='font-size:50px; color:#ff5050'><br> Music for Everyone</br></p></b></div>
            <div>
                <script>
                  (function() {
                    var cx = '014387967901114658405:bgdyo_bpwkc';
                    var gcse = document.createElement('script');
                    gcse.type = 'text/javascript';
                    gcse.async = true;
                    gcse.src = 'https://cse.google.com/cse.js?cx=' + cx;
                    var s = document.getElementsByTagName('script')[0];
                    s.parentNode.insertBefore(gcse, s);
                  })();
                </script>
                <gcse:search></gcse:search>
            </div>
        </div>
    <meta http-equiv="refresh" content="5;url=http://meepwntube.0x1337.space/account.php" />
    </header>


<?php
    require_once('dbconnect.php');
    require_once('auth.php');
    require_once('lib.php');
    require_once('_ducnt.php');
    require_once('curl.php');
    ini_set("display_errors", 0);
    ini_set("session.cookie_httponly", 1);
    ob_start();
    session_start();

    if ($_auth === false) {
    header("Location: index.php");
    exit;
    }

    function rule_change_avatar($data){        
        $_parse = parse_url($data);
        if(!preg_match("/^http$/", $_parse['scheme'])) {
            die("<center><b><p style='font-size:15px; color:#cc0066'>Error: 'HTTP only' dude!!</p></b></center>");
        }

        if(!preg_match("/meepwntube.0x1337.space$/", $_parse['host'])) {

            die("<center><b><p style='font-size:15px; color:#cc0066'>Error: Our 'space' only dude!!</p></b></center>");
        }

        //super miniwaf prevent scanning internal
        if(preg_match("/127|192|172|000|0.0|fff|0x0|0x7f|0177|2130706433|\]|\[/", $_parse['host'])) {
            die("<center><b><p style='font-size:15px; color:#cc0066'>Error: Oops script kiddie detected!!</p></b></center>");
        }
        $_domain = $_parse['scheme']."://".$_parse['host'].":".$_parse['port'].$_parse['path'];
        return $_domain;
    }


    $_name = miniwaf(rule_change_avatar($_POST['change_avatar']));

    $secret_path = gen_secret_path($username);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(empty($_name)){
        $_name = "";
    }
    else {
        $_name = mysqli_real_escape_string($conn,miniwaf($_name));
        $update_avatar = mysqli_query($conn,"UPDATE users SET avatar_name = '$_name' WHERE username='$username'");
        echo "<center><b><p style='font-size:20px; color:#ff5050'>Your avatar: ".$_name." have been updated successfully</p></b></center>";
        echo "<center><b><p style='font-size:15px; color:#cc0066'>The page auto refresh after 5 second</p></b></center>";
        exit;
    }
    echo "<center><b><p style='font-size:15px; color:#cc0066'>Ops there is an error</p></b></center>";
    }
?>
