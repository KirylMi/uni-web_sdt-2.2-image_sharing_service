<?php
session_start();
require_once "Functions.php";
checkErrors();
clearCache("images");
#First - php, There are three possible scenarios:
#   1) /users/ -> show all users;
#   2) /users/username -> show user with the corresponding username
#   3) /users/username/something else -> error. Such
if(isUser($_SERVER['REQUEST_URI'])){
    //if (!isset($_SESSION['page'])) $_SESSION['page']=1;  //stores the current page of the current user
    $name=getName($_SERVER['REQUEST_URI']);
    if(DB_userExistence($name)===false){
        errorRerout("/users.php","Such user doesn\'t exists");
        echo "Location: /users.php";
    }
} 
?>
<html>
<head>
<style>
        <?php include 'styles/table.css'; ?>
        <?php include 'styles/navPanel.css'; ?>
        <?php include 'styles/imagesFlexShort.css'; ?>
        body{
            font-family: 'Sans-serif' !important;
        }
</style>
</head>
<body>
<div class="topnav">
    <?php
    if ($_SESSION['username']){
        echo "<a href=/signin.php>Log out</a>";
        echo "<a href=/profile.php>Account</a>";
        
    }
    else {
        echo "<a href=/signin.php>Sign in</a>";
    }
    echo "<a href=/index.php style=\"float:left!important;\">Main page</a>";
    ?>
</div>
<?php
        require_once "Functions.php";
        #if page is /users/ -> show all users
        if (!isset($name)){
            if (($users=DB_getAllUsers()) !=null)
                form_table($users,['id','email','name']);
        }

        #if user is requested, then show some info about him (images and stuff)
        else {
            $username = DB_getUserData($name)->username;
            $imagesNames = DB_getImagesNames($username);
            $info = DB_getUserData($name);  #PASS errors rerouting, like if username is not correct, then (NOT THROW) redirect with error msg to /users/
            if  ($info->username != $_SESSION['username'] and isset($_SESSION['username'])){
                #PASS
                if (!isSubscribed($_SESSION['username'],$username)){
                    form_subscribe($username,$name);
//                    echo "<form action='/UsersLogic.php' method='POST' name='Subscribe'>";
//                    echo "<input type='hidden' value='$username' name='username'>";
//                    echo "<input type='hidden' value='$name' name='name'>";
//                    echo "<button type='submit' name='Subscribe'>SUBSCRIBE</button>";
//                    echo "</form>";
                } else{
                    form_unsubscribe($username,$name);
//                    echo "<form action='/UsersLogic.php' method='POST' name='UnSubscribe'>";
//                    echo "<input type='hidden' value='$username' name='username'>";
//                    echo "<input type='hidden' value='$name' name='name'>";
//                    echo "<button type='submit' name='UnSubscribe'>UNSUBSCRIBE</button>";
//                    echo "</form>";
                }
            }
            echo "<h1>" . $name . " Page!</h1>";
            //echo "Photos are gonna be here soon... Probably...";
            echo "<br><br>Debug: (Checking if DB is working:)<br>";
            print_r($info);
            form_images($username);
        }

?>
</body>
</html>