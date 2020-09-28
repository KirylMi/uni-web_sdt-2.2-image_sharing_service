<?php session_start();
require_once "Functions.php";
checkErrors();
clearCache("images");

#The part below checks if the profile is available
# (you can't access this part without logging in, and without activation from the e-mail)

#Checks if user is logged in (Profile page shouldn't be available to the guests)
if (!isset($_SESSION['username'])){
    errorRerout("/signin.php","You are not logged in yet. Your profile page will be available after logging into it");
}

#Checks if current user is activated already
if (($_SESSION['active'])!=true){
    require_once "Functions.php";
    #if user is trying to activate himself
    if (isActivation($_SERVER['REQUEST_URI'])){
        #if successful -> ok. Continue.
        if(activateUser($_SESSION['username'],getKey($_SERVER['REQUEST_URI']))==true){
            #if activation failed -> error. Re rout.
            errorRerout("/profile.php","Successfully activated! Enjoy!");
        }
    }
    #if user is not yet activate
    else{
        echo "Hi there! Your registration is almost completed.
         An email with the confirmation code was sent to your address".$_SESSION['email'];
        exit(0);
    }
}
?>

<html>
<head>
    <title>Profile page</title>
    <style>
        <?php include 'styles/navPanel.css';    ?>
        <?php include 'styles/imagesFlexShort.css';  ?>
        body{
            font-family: 'Sans-serif' !important;
        }
    </style>
</head>
<body>
<!--Navigation-->
<div class="topnav">
    <?php
        echo "<a href=/signin.php>Log out</a>";
        echo "<a href=edit.php>Account Settings</a>";
        echo "<a href=/users.php>Users List</a>";
        echo "<a href=/index.php style=\"float:left!important;\">Main page</a>";
        echo "<a style=\"margin-left: 40%;font-family:Meera;font-size: 21px;float:left!important;\">".$_SESSION['name']."</a>";
    ?>
</div>
<!--Addition and deletion forms-->
<table style="margin: auto">
    <tr>
        <td>
            <fieldset style="margin: auto;width: 150px;">
                <form method="POST" action="/UsersLogic.php" enctype="multipart/form-data" name="Upload_photo">
                    <input type="hidden" name="MAX_FILE_SIZE" value="50000000"/>  <!--  50mb yclovno-->
                    <label for="photo">Add new photo:</label> <input type="file" name="photo" accept="image/png,image/jpg,image/jpeg">
                    <label for="name">Name: </label> <input type="text" name="Name">
                    <button type="submit" name="Upload_photo">Commit</button>
                </form>
            </fieldset>
        </td>
        <td>
            <fieldset style="margin: auto;width: 150px;">
                <form method="POST" action="/UsersLogic.php" enctype="multipart/form-data" name="Delete_photo">
                    <label for="photo">Photo name to delete:</label> <input type="text" name="Name">
                    <label for="sure">Are you sure(#PASS)</label> <input type="checkbox" name="Sure">
                    <button type="submit" name="Delete_photo">Delete</button>
                </form>
            </fieldset>
        </td>
    </tr>
</table>

<!--Images themselves-->
<?php
form_images($_SESSION['username']);
?>

</body>
</html