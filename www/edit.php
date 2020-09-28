<?php session_start(); 
require_once("Functions.php");
checkErrors();
require_once "Functions.php";
if (!isset($_SESSION['username']) || $_SESSION['active']!=true){
    errorRerout("/index.php","You are not allowed to enter this forbidden place!");
}
?>
<html>

<head>
    <title>RofloImageService (Main page of the internet )) )</title>
    <style>
        <?php include './styles/navPanel.css'; ?>
        <?php include './styles/stealthWindows.css'; ?>
        <?php include './styles/edit.css'; ?>
        body{
            font-family: 'Sans-serif' !important;
        }
    </style>
</head>

<body>

<div class="topnav">
    <?php
        echo "<a href=/signin.php>Log out</a>";
        echo "<a href=/profile.php>Account</a>";
        echo "<a href=/users.php>Users List</a>";
        echo "<a href=/index.php style=\"float:left!important;\">Main page</a>";
    ?>
</div>

<fieldset style="width:30%;margin:auto;">
<legend style="font-size: 20px">Your current data:</legend>
    <table>
        <?php foreach ($_SESSION as $key=>$userVal){
            echo "<tr>";
            echo "<td>$key</td><td class='Value'>";
            if ($key!='password') echo $userVal; else echo str_repeat('*',strlen($userVal));
            echo"</td>";
            echo "<td class='Value' style='width:350px;text-align:center'>";
            if ($key!='active' && $key != 'username') echo "<a href=\"#$key\">Change $key </a>";
            else echo "Unchangeable";
            echo "</td>";
            echo "</tr>";
        }?>
    </table>
</fieldset>


<div id="email">
    <div id="okno">
        Warning!: You will have to reactivate your new email <br>
        <form method="POST" action="/UsersLogic.php" name="Change_email">
            <label for="email">new email:</label> <input type="email" name="email">
            <button type="submit" name="Change_email">Commit</button>
        </form>
        <a href="#" class="close">Close me</a>
    </div>
</div>

<div id="name">
    <div id="okno">
        Warning! This action will change your user URL! <br>
        <form method="POST" action="/UsersLogic.php" name="Change_name">
            <label for="name">new name:</label> <input type="text" name="name">
            <button type="submit" name="Change_name">Commit</button>
        </form>
        <a href="#" class="close">Close me</a>
    </div>
</div>

<div id="password">
    <div id="okno">
        Warning! This action will change your password. Your old password will be lost<br>
        <form method="POST" action="/UsersLogic.php" name="Change_password">
            <label for="password">new password:</label> <input type="password" name="password">
            <button type="submit" name="Change_password">Commit</button>
        </form>
        <a href="#" class="close">Close me</a>
    </div>
</div>


<div id="privilege">
    <div id="okno">
        Warning! If you have no reason to ask administrator for such change, please, don't. <br>
        <form method="POST" action="/UsersLogic.php" name="Change_privilege">
            <label for="privilege">Are you sure about that?</label> <input type="checkbox" value="admin" name="privilege">
            <br/>
            <button type="submit" name="Change_privilege">Commit</button>
        </form>
        <a href="#" class="close">Close me</a>
    </div>
</div>

<?php
?>


</body>