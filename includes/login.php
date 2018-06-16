
<script src="jscolor.js"></script>

<?php
    session_start();
    if(isset($_SESSION['user_id'])) {

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
        //echo $_SESSION['user_id'];
    }
?>