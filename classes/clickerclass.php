<?php

    require_once("definitions.php");
    require_once("dbClass.php");

    //session_start();

    class clickerclass{

        private $db;


        public $userip;
        public $username;
        public $jointime;
        protected $lastmousepos;
        protected $lasttime;

        protected $dbservername;
        protected $dbusername;
        protected $dbpassword;
        protected $dbname;
        protected $dbtablename;
        protected $dbusertable;
        protected $dbuserstatstable;

        protected $conn;


        function __construct($ip){
            $this->dbservername = DB_HOST;
            $this->dbusername = DB_USER;
            $this->dbpassword = DB_PASS;
            $this->dbname = DB_DATABASE;
            $this->dbtablename = DB_TABLE;
            $this->dbusertable = DB_USERTABLE;
            $this->dbuserstatstable = "clickerusers";

            $this->db = new dbClass();

            $createQuery = "CREATE TABLE ".$this->dbtablename." (
                id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                posx int(11) NOT NULL,
                posy int(11) NOT NULL,
                clicker int(11),
                ip VARCHAR(16),
                time VARCHAR(24) NOT NULL,
                INDEX clicker (clicker),
                CONSTRAINT clicker FOREIGN KEY (clicker) REFERENCES users (id) ON UPDATE CASCADE
            )";

            // Checks if table exists. If it does not: create it.
            $this->db->query("SHOW TABLES LIKE '".$this->dbtablename."'");
            $this->db->execute();
            if($this->db->rowCount() == 1) {
                //echo "Table exists";
            }
            else {
                //echo "Table does not exist";
                $this->db->query($createQuery);
                $this->db->execute();
            }

            $createQuery = "CREATE TABLE ".$this->dbusertable."(
              	id INT(11) NOT NULL AUTO_INCREMENT,
                fullname VARCHAR(50) NOT NULL,
                username VARCHAR(100) NOT NULL,
                password VARCHAR(150) NOT NULL,
                email VARCHAR(100) NOT NULL,
                text TEXT NOT NULL,
                admin INT(3) NOT NULL,
                profilepic VARCHAR(50) NOT NULL,
                PRIMARY KEY (id)
            )";

            // Checks if table exists. If it does not: create it.
            $this->db->query("SHOW TABLES LIKE '".$this->dbusertable."'");
            $this->db->execute();
            if($this->db->rowCount() == 1) {
                //echo "Table exists";
            }
            else {
                //echo "Table does not exist";
                $this->db->query($createQuery);
                $this->db->execute();
            }


            $createQuery = "CREATE TABLE ".$this->dbuserstatstable." (
                userid int(11) NOT NULL,
                ip varchar(16),
                joined int(11),
                totalclicks int(11),
	            color VARCHAR(16) NULL DEFAULT NULL,
                PRIMARY KEY (userid),
                INDEX userid (userid),
                CONSTRAINT userid FOREIGN KEY (userid) REFERENCES users (id) ON UPDATE CASCADE
            )
            ENGINE=InnoDB
            ";

            // Checks if table exists. If it does not: create it.
            $this->db->query("SHOW TABLES LIKE '".$this->dbuserstatstable."'");
            $this->db->execute();
            if($this->db->rowCount() == 1) {
                //echo "Table exists";
            }
            else {
                //echo "Table does not exist";
                $this->db->query($createQuery);
                $this->db->execute();
            }


        }

        function __destruct(){
            if(isset($this->conn)){
                $this->conn->close();
            }

            unset($userip);
            unset($lastmousepos);
            unset($lastclick);

            unset($servername);
            unset($username);
            unset($password);
            unset($dbname);
            unset($tablename);

        }

        function login($username, $password){
            $sqlusers = "SELECT * FROM ".DB_USERTABLE." WHERE username = :username";

            $this->db->query($sqlusers);
            $this->db->execute(['username' => $username]);

            $user = $this->db->resultSingle();

            if($this->db->rowCount() != 0){
                // We found the user.
                $hash = $user['password'];
                if (password_verify($password, $hash)){
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_username'] = $user['username'];
                    $_SESSION['user_fullname'] = $user['fullname'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_text'] = $user['text'];
                    $_SESSION['user_admin'] = $user['admin'];


                    $sqldata = "SELECT * FROM ".$this->dbuserstatstable." WHERE userid = :userid";

                    $this->db->query($sqldata);
                    $this->db->execute(['userid' => $user['id']]);

                    $userdata = $this->db->resultSingle();

                    $_SESSION['user_color'] = $userdata['color'];

                    $this->username = $username;

                    echo  "ok.";

                }else{
                    echo "not ok";
                }
            }else {
                // no user was found.
                echo "not ok";
            }
        }

        function logout(){
            session_unset();

            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }

            session_destroy();

            echo "User has been logged out.";
            //window.location.assign("../index.php");
            //header("Location: ../index.php");

            //echo "User Logged out";
        }

        function createUser($username, $password){
            $hash = password_hash($password, PASSWORD_BCRYPT);

            $sqlusers = "SELECT * FROM ".DB_USERTABLE." WHERE username = :username";

            $this->db->query($sqlusers);
            $this->db->execute(['username' => $username]);

            if($this->db->rowCount() <= 0) {
                $sql = "INSERT INTO $this->dbusertable (username, password) VALUES (:username, :hash)";
                $this->db->query($sql);
                $this->db->execute(['username' => $username, 'hash' => $hash]);


                // Login does ok.
                $this->login($username, $password);
            }else{
                echo "not ok";
            }

        }

        function save($posx, $posy, $time){

            if(isset($_SESSION['user_id'])){


                $posx = trim($posx);
                $posy = trim($posy);
                $time = trim($time);
                $userid = $_SESSION['user_id'];
                $ip = $_SERVER['REMOTE_ADDR'];

                $this->lastmousepos = [$posx, $posy];
                $this->lasttime = $time;

                $sql = "INSERT INTO $this->dbtablename (posx, posy, clicker, ip, time) VALUES (:posx, :posy, :userid, :ip, :time)";
                $this->db->query($sql);
                $this->db->execute(['posx' => $posx, 'posy' => $posy, 'userid' => $userid, 'ip' => $ip, 'time' => $time]);

                $sql = "SELECT id FROM clicker WHERE clicker=:userid";
                $this->db->query($sql);
                $this->db->execute(['userid' => $userid]);
                $totalclicks = $this->db->rowCount();

                $sql = "
                INSERT INTO $this->dbuserstatstable (userid, joined, totalclicks)
                    VALUES (:userid, :time, :clicks)
                ON DUPLICATE KEY UPDATE 
                    totalclicks = :clicks
                ";
                $this->db->query($sql);
                $this->db->execute(['userid' => $userid, 'time' => $time, 'clicks' => $totalclicks]);

                echo "Saved.";
            }else{
                return "You are not logged in. Log in to save clicks.";
            }
        }

        function displayStats(){
            $sql = "SELECT * FROM $this->dbtablename";
            $this->db->query($sql);
            $this->db->execute();
            $clicksnum = $this->db->rowCount();
            $clicks = $this->db->resultAssoc();

            $times = [];
            $usertimes = [];
            $userdata = null;
            $userjoined = null;
            $firstclick = null;
            $lastclick = null;

            $query = "
            SELECT count(c.id), c.clicker, u.username
            FROM $this->dbtablename c
            JOIN $this->dbusertable u
                ON c.clicker = u.id
            GROUP BY c.clicker 
            ORDER BY count(c.id) DESC LIMIT 5
            ";
            // "SELECT count(id), clicker FROM $this->dbtablename c GROUP BY clicker ORDER BY count(id) DESC LIMIT 5"
            $this->db->query($query);
            $this->db->execute();
            $topclickers = $this->db->resultAssoc();

            $highestclick = [0,0];
            $mostsidewaysclick = [0,0];

            foreach($clicks as $click){
                array_push($times, $click['time']);

                if(isset($_SESSION['user_id'])){
                    if($click['clicker'] === $_SESSION['user_id']){
                        array_push($usertimes, $click['time']);
                    }
                }

                if($click['posy'] > $highestclick[1]){
                    $highestclick = [$click['posx'], $click['posy']];
                }

                if($click['posx'] > $mostsidewaysclick[0]){
                    $mostsidewaysclick = [$click['posx'], $click['posy']];
                }

            }
            if($clicksnum > 0){
                $lastclick = date("F j, Y, h:i:s", max($times));
                $firstclick =  date("F j, Y, h:i:s", min($times));
            }


            if(isset($_SESSION['user_id'])){
                //$result = $this->conn->query("SELECT * FROM $this->dbuserstatstable WHERE userid=".$_SESSION['user_id']);
                //$userdata = $result->fetch_assoc();

                $sql = "SELECT * FROM $this->dbuserstatstable WHERE userid=:userid";
                $this->db->query($sql);
                $this->db->execute(['userid' => $_SESSION['user_id']]);

                $userdata = $this->db->resultSingle();

                $userjoined = $userdata['joined'];
            }


            $stats = "<h3>GLOBAL STATS</h3>";

            if($clicksnum > 0) {
                $stats .= "<p><b>Number Of Clicks: </b>$clicksnum</p>";
                $stats .= "<p><b>First Click: </b>$firstclick</p>";
                $stats .= "<p><b>Last Click: </b>$lastclick</p>";
                $stats .= "<p><b>Most Vertical Click: </b>($highestclick[0], $highestclick[1])</p>";
                $stats .= "<p><b>Most Horizontal Click: </b>($mostsidewaysclick[0], $mostsidewaysclick[1])</p>";
                $stats .= "<p><b>Top Clicker: </b>" . $topclickers[0]['username'] . "</p>";
                //$stats .= phpversion();

            }else{
                $stats .= "<p><b>No one has clicked yet.</b></p>";
            }

            $stats .= "</br>";

            if(isset($_SESSION['user_id'])){
                $userjoined = date("F j, Y, h:i:s", $userjoined);
                $stats .= "<h3>PERSONAL STATS</h3>";
                $stats .= "<p>You joined on $userjoined</p>";

                if(count($usertimes) > 0){
                    $userinitclick = date("F j, Y, h:i:s", min($usertimes));//$userdata['joined']);
                    $userlastclick = date("F j, Y, h:i:s", max($usertimes));
                    $test = new DateTime($userinitclick);
                    $test2 = new DateTime($userjoined);
                    $datediff = date_diff($test, $test2);
                    $userwaited = "";
                    if($datediff->y !== 0){
                        $userwaited .= "$datediff->y years and ";
                    }
                    if($datediff->m !== 0){
                        $userwaited .= "$datediff->m months and ";
                    }
                    if($datediff->d !== 0){
                        $userwaited .= "$datediff->d days and ";
                    }
                    if($datediff->h !== 0){
                        $userwaited .= "$datediff->h hours and ";
                    }
                    if($datediff->i !== 0){
                        $userwaited .= "$datediff->i minutes and ";
                    }
                    if($datediff->s !== 0){
                        $userwaited .= "$datediff->s seconds";
                    }else{
                        $userwaited .= "0 seconds??!!??! GGWP";
                    }

                    //$stats .= var_dump($datediff);

                    $stats .= "<p>You have clicked a total of ".$userdata['totalclicks']." times.</p>";
                    $stats .= "<p>You clicked for the first time on $userinitclick.</p>";
                    $stats .= "<p>The last time you clicked was on $userlastclick.</p>";
                    $stats .= "<p>Before you clicked you waited for $userwaited.</p>";

                }else{
                    $stats .= "<p><b>You have not clicked yet!</p>";
                }


                $stats .= "</br>";
            }


            return $stats;

        }

        function displayClicks($height){

            $string = "";

            $this->db->query("SELECT * FROM $this->dbtablename");
            $this->db->execute();
            $clicksnum = $this->db->rowCount();
            $clicks = $this->db->resultAssoc();

            $this->db->query("SELECT userid, color FROM $this->dbuserstatstable");
            $this->db->execute();
            $users = $this->db->resultAssoc();

            foreach($clicks as $click){
                $clicker = null;

                foreach($users as $user){
                    if($click['clicker'] === $user['userid']){
                        $clicker = $user;
                        break;
                    }
                }



                $color = $clicker['color'];
                if($clicker['color']==NULL){
                    $color = "#969696";
                }
                $string .= "<div id='click' style='top:".($height - $click['posy'])."; left:".$click['posx']."; border-color:".$color."'></div>";
            }

            return $string;
        }

        function updateColor($color){

            if(isset($_SESSION['user_id'])) {
                $color = trim($color);
                $time = time();
                $ip = $_SERVER['REMOTE_ADDR'];
                $userid = $_SESSION['user_id'];
                $_SESSION['user_color'] = $color;


                $sql = "SELECT id FROM clicker WHERE clicker=:userid";
                $this->db->query($sql);
                $this->db->execute(['userid' => $userid]);

                $totalclicks = $this->db->rowCount();


                $sql = "
                INSERT INTO $this->dbuserstatstable (userid, joined, totalclicks, ip, color)
                    VALUES (:userid, :time, :totalclicks, :ip, :color)
                ON DUPLICATE KEY UPDATE 
                    color = :color
                ";

                $this->db->query($sql);
                $this->db->execute(['userid' => $userid, 'ip' => $ip, 'time' => $time, 'color' => $color, 'totalclicks' => $totalclicks]);

            }else{
            }

            return $color;
        }

    }