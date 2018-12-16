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
    header("Location: index.php?page=login.php");
    exit;
}

$watch = $_GET['v'];

switch ($watch) {
    case "video2":
        header("Location: index.php?page=video2.php");
        break;
    case "video3":
        header("Location: index.php?page=video3.php");
        break;
    case "video4":
        header("Location: index.php?page=video4.php");
        break;
    case "video5":
        header("Location: index.php?page=video5.php");
        break;
    default:
        continue;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>MeePwnTube</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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
</head>

<body>
    <header class="row" style="margin: 10px 0; box-shadow: 0 4px 2px -2px #f2f2f2;">
        <div class="container">
            
            <div class="row">
                <div class="col-md-2" style="margin: 10px 0;">
                    <a href = "index.php"><img src="images/meepwntube.png" width="250px" height="150px" /></a>
                </div>
                <div class="col-md-10">
                    <div class="row">
                    <div class="col-md-5 col-md-offset-6 icon-action">
                    <div class="row" style="text-align: right; margin: 15px 0;">
                        <p>
                            <a href ="#"><i class="fa fa-video-camera fa-lg" aria-hidden="true"></i></a>
                        </p>
                        <p>
                            <a href ="#"><i class="fa fa-calendar fa-lg" aria-hidden="true"></i></a>
                        </p>
                        <p>
                            <a href ="#"><i class="fa fa-comment-o  fa-lg" aria-hidden="true"></i></a>
                        </p>
                        <p>
                            <a href ="#"><i class="fa fa-bell fa-lg" aria-hidden="true"></i></a>
                        </p>
                        <p>
                        </p>
                    </div>

                </div>
                
                <div class="col-md-1 icon-action">
                        <div id="profile">
                            <div class="icon" id="profile-icon" style="cursor: pointer">
                                <?php echo $_parse_current_avatar;?>
                            </div>

                            <div class="list-group" style="position: absolute; width: 300%; left: -250%; margin-top: 10px; z-index: 10; display: none; box-shadow: 0 1px 10px rgba(0,0,0,.1); border-width: 0;"
                                id="profile-detail">
                                <div class="list-group-item" style="background-color: #e9e9e9;">
                                    <div class="row" >
                                        <div class="col-md-2" style="text-align: center;">
                                            <?php echo $_thumbnail;?>
                                        </div>
                                        <div class="col-md-9">
                                            <div style="padding: 5px 0">
                                                <center>
                                                    <b><p style='font-size:15px; color:#0099cc'>Hello <?php echo miniwaf($username) ;?></p></b>
                                                    <b><p style='font-size:12px; color:#0099cc'>Having a nice day</p></b>
                                                </center>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                                <a href="account.php" class="list-group-item" style="display: block;">
                                        <div class="row" >
                                                <div class="col-md-2" style="text-align: center;">
                                                        <i class="fa fa-address-book fa-lg" aria-hidden="true"></i>
                                                </div>
                                                <div class="col-md-9">
                                                           My account
                                                </div>
                                            </div>
                                </a>
                                <a href="#under_construction" class="list-group-item" style="display: block;">
                                    <div class="row" >
                                        <div class="col-md-2" style="text-align: center;">
                                                <i class="fa fa-cog fa-lg" aria-hidden="true"></i>
                                        </div>
                                        <div class="col-md-9">
                                                    Creator Studio
                                        </div>
                                    </div>
                                </a>
                                <a href="logout.php" class="list-group-item" style="display: block;">
                                    <div class="row" >
                                        <div class="col-md-2" style="text-align: center;">
                                            <i class="fa fa-sign-out fa-lg" aria-hidden="true"></i>
                                        </div>
                                        <div class="col-md-9">
                                            Logout
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                </div>
                </div>

                <div align="center" class="row">
                    <b><p style='font-size:50px; color:#ff5050'>Music for Everyone</p></b>
                </div>
            </div>

            <script type="text/javascript">
                $(function () {
                    $("#profile-icon").click(function () {
                        $("#profile-detail").toggle();
                    });

                    $(document).mouseup(function(e) 
                    {
                        var container = $("#profile-detail");
                        if (!container.is(e.target) && container.has(e.target).length === 0){
                            container.hide();
                        }
                    });
                });
            </script>

            
            </div>
            <div class="row">
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
        </div>

    </header>
    <div class="container">

        <br />
        <article class="row">
            <section class="col-md-8">
                <video width="100%" height="auto" controls>
                    <source src="videos/video8.mp4" type="video/mp4">
                </video>
                <h3>
                    <b>Ta Cứ Đi Cùng Nhau - Đen ft Linh Cáo (Beat Instrumental)</b>
                </h3>
                <div class="row">
                    <div class="col-md-5">
                        <p style="font-size:20px">37,456 B view</p>
                    </div>
                    <div class="col-md-7" style="text-align: right">
                        <a href = "#"><img src = "images/like.png" height="25" width="25"><b style="font-size:20px">1,4 M</b>    
                        <a href = "#"><img src = "images/dislike.jpg" height="25" width="25"><b style="font-size:20px">400</b>
                        <a href = "#under_construction"><img src = "images/report.png" height="25" width="25"><b style="font-size:20px">Report video</b></a>
                    </div>
                </div>
                <form method="post" action="comment.php">  
                <br>
                    <b><p style="font-size:20px; color:green">Comment:</p></b> <textarea name="comment" rows="1" cols="40" style="width: 742px; height: 66px;"></textarea>
                <br>
                <input type="submit" name="submit" value="Submit">  
                </form>
                <?php
                    $parse = mysqli_query($conn,"SELECT * FROM comments ORDER BY id DESC limit 10");
                    while($row=mysqli_fetch_array($parse)){
                        echo "<div>";
                        echo "<b><p style='font-size:30px; color:#c44dff'>".$row['username'] . ": " . $row['comment']."</p></b>";
                        echo "</div>";
                    }
                    mysqli_close($conn);
                ?>
            </section>

            <section class="col-md-4">

                <div class="row" >
                    <a href="#">
                        <div class="col-md-7">
                            <video width="100%" height="auto">
                                <source src="videos/video1.mp4" type="video/mp4">
                            </video>
                        </div>
                        <div class="col-md-5">
                            <h5 class="hidden-overflow">
                                <a href = "index.php?page=video1.php"><b>Hương Trấu (Rice Husk) - Tofu ft. 2Can
                                </b>
                            </h5>
                            <p style="font-size:20px">Tay Nguyen Sound</p>
                            <p style="font-size:20px">2,4 bilion view</p>
                        </div>
                    </a>
                </div>

                <div class="row" >
                    <a href="#">
                        <div class="col-md-7">
                            <video width="100%" height="auto">
                                <source src="videos/video2.mp4" type="video/mp4">
                            </video>
                        </div>
                        <div class="col-md-5">
                            <h5 class="hidden-overflow">
                                <a href = "index.php?page=video2.php"><b>Đen - Ngày Khác Lạ ft. Giang Pham, Triple D
                                </b>
                            </h5>
                            <p style="font-size:20px">Đen</p>
                            <p style="font-size:20px">2,4 bilion view</p>
                        </div>
                    </a>
                </div>
                <hr />

                <div class="row" style="margin-bottom: 10px">
                    <a href="#">
                        <div class="col-md-7">
                            <video width="100%" height="auto">
                                <source src="videos/video3.mp4" type="video/mp4">
                            </video>
                        </div>
                        <div class="col-md-5">
                            <h5 class="hidden-overflow">
                                <a href = "index.php?page=video3.php"><b>Những Ô Cửa Màu - ToFu ft. VoVanDuc
                                </b>
                            </h5>
                            <p style="font-size:20px">Tay Nguyen Sound</p>
                            <p style="font-size:20px">1,4 bilion view</p>
                        </div>
                    </a>
                </div>
                <div class="row"  style="margin-bottom: 10px">
                    <a href="#">
                        <div class="col-md-7">
                            <video width="100%" height="auto">
                                <source src="videos/video4.mp4" type="video/mp4">
                            </video>
                        </div>
                        <div class="col-md-5">
                            <h5 class="hidden-overflow">
                                <a href = "index.php?page=video4.php"><b>Bản Giao Hưởng Tây Nguyên - TayNguyenSound |Vid Kara| |TROY LYRICS| |TNS|
                                </b>
                            </h5>
                            <p style="font-size:20px">Tay Nguyen Sound</p>
                            <p style="font-size:20px">900 milion view</p>
                        </div>
                    </a>
                </div>

                <div class="row"  style="margin-bottom: 10px">
                    <a href="#">
                        <div class="col-md-7">
                            <video width="100%" height="auto">
                                <source src="videos/video5.mp4" type="video/mp4">
                            </video>
                        </div>
                        <div class="col-md-5">
                            <h5 class="hidden-overflow">
                                <a href = "index.php?page=video5.php"><b>Cho Những Gì Ta Yêu - TeA x Tuyết x VoVanDuc
                                </b>
                            </h5>
                            <p style="font-size:20px">Tay Nguyen Sound</p>
                            <p style="font-size:20px">800 milion view</p>
                        </div>
                    </a>
                </div>

                <div class="row"  style="margin-bottom: 10px">
                    <a href="#">
                        <div class="col-md-7">
                            <video width="100%" height="auto">
                                <source src="videos/video6.mp4" type="video/mp4">
                            </video>
                        </div>
                        <div class="col-md-5">
                            <h5 class="hidden-overflow">
                                <a href = "index.php?page=video6.php"><b>Người Khác - Phan Mạnh Quỳnh ft. Drum7
                                </b>
                            </h5>
                            <p style="font-size:20px">Phan Mạnh Quỳnh</p>
                            <p style="font-size:20px">900 milion view</p>
                        </div>
                    </a>
                </div>

                <div class="row"  style="margin-bottom: 10px">
                    <a href="#">
                        <div class="col-md-7">
                            <video width="100%" height="auto">
                                <source src="videos/video7.mp4" type="video/mp4">
                            </video>
                        </div>
                        <div class="col-md-5">
                            <h5 class="hidden-overflow">
                                <a href = "index.php?page=video7.php"><b>CHẠY NGAY ĐI | RUN NOW | SƠN TÙNG M-TP
                                </b>
                            </h5>
                            <p style="font-size:20px">SƠN TÙNG M-TP</p>
                            <p style="font-size:20px">1337 bilion view</p>
                        </div>
                    </a>
                </div>

                

            </section>
        </article>
        <footer class="row">
        </footer>
    </div>

</body>

</html>