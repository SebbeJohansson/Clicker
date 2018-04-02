<?php
    session_start();

    include_once("classes/clickerclass.php");
    $_SESSION['initialtime'] = time();

    if(isset($_SESSION['user_id'])){
        $conn = new mysqli("mysql14.citysites.se", "132872-qo93560", "!Qwerty1", "132872-miniprojects");
        // Check connections
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "
                INSERT INTO clickerusers (userid, joined, totalclicks, ip)
                    VALUES ('".$_SESSION['user_id']."', '".time()."', '0', '".$_SERVER['REMOTE_ADDR']."')
                ";

        $conn->query($sql);
    }

?>

<html>

    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
        <script src="script.js"></script>
        <script src="jscolor.js"></script>
        <link href="style.css" type="text/css" rel="stylesheet">
    </head>

    <body>
        <section id="target">

        </section>
        <section id="stats" style="display: none">

            <?php
                //action="doSomething.php"

                echo '<section id="login">';
                    include_once('includes/login.php');
                    /*if(isset($_SESSION['user_id'])) {
                        echo "<h3 id='logout' onclick='logout()'>Logout</h3>";

                        $color = 0;
                        if(isset($_SESSION['user_color'])){
                            $color = $_SESSION['user_color'];
                        }else{
                            $color = "969696";
                        }

                        if(isset($_SESSION['user_id'])) {
                            echo 'Color: <input id="jscolor" class="jscolor" value="'.$color.'" onchange="colorUpdate(this.jscolor)">';
                        }

                    }else{
                        echo "<form method='post'  id='loginform' enctype='multipart/form-data'><input type='text' name='username' id='username' placeholder='Username'><input type='password' name='password' id='password' placeholder='Password'><button type='submit' name='login'>login</button><input type='hidden' name='action' value='login'></form>";

                    }*/
                echo '</section>';
            ?>


            <section id="statstext">
                <p>STATS</p></br><p>A lot of clicks</p>
            </section>

            <?php if(!isset($_SESSION['user_id'])) include('createaccount.html'); ?>
        </section>


        <section id="clicks">

        </section>

    </body>

</html>
