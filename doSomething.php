<?php
    /**
     * Created by PhpStorm.
     * User: Sebbans
     * Date: 2018-01-02
     * Time: 20:13
     */
    include_once("classes/clickerclass.php");
    session_start();


    global $clicker;
    //$_SESSION['clicker'] = $clicker;
    $clicker = new clickerclass($_SERVER['REMOTE_ADDR']);
    //$clicker->jointime = $_SESSION['initialtime'];

    //$clicker = $_SESSION['clicker'];

    if(isset($_POST['action'])) {
        $action = $_POST['action'];

        switch($action){
            case 'clicked':
                echo $clicker->save($_POST['mousex'], $_POST['mousey'], time());

                //echo $_POST['mousex'].", ".$_POST['mousey']."\n\n".$clicker->jointime;
                break;
            case 'login':
                $clicker->login($_POST['username'], $_POST['password']);
                break;
            case 'logout':
                $clicker->logout();
                break;
            case 'createuser':
                $clicker->createUser($_POST['username'], $_POST['password']);
                break;
            case 'displaystats':
                echo $clicker->displayStats();
                break;
            case 'displayclicks':
                //echo "<div id='click'></div>";
                echo $clicker->displayClicks($_POST['height']);
                break;
            case 'updateColor':
                echo $clicker->updateColor($_POST['color']);
                break;
            default:

                break;
        }

    }else{
        echo "wtf";
    }