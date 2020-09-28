<?php session_start();
require_once "Functions.php";
if (isset($_POST['Sign_up'])){  //if sign_in button was pressed #PASS STOP USING  DIRECT DATABASE AND MAIL CONNECTIONS!!!!!!!
    try{
        validate("Email",$_POST['email']);
        validate("Username",$_POST['username']);
        validate("Password",$_POST['password']);
        validate("Name",$_POST['name']);
        #if we are here, then validation is over
        require_once "db.php";
        $myDb = new DB();
        if(($myDb->addUser($_POST['username'],$_POST['password'],$_POST['email'],$_POST['name']))===false){
            throw new Exception("Something wrong with UsersLogic. Probably, such user already exists. If you are sure, that it doesn\'t, then there are some differences between how MySQL and PostgreSql works");
        }
        else{
            if($myDb->generateActivation($_POST['username'],$_POST['email']) === false) throw new Exception("Something wrong with the UserLogic. GENERATION Probably, i have fucked up something with the other table insertion (ActivationUser).");
            else{
                require_once "Mail.php";
                $activationCode=$myDb->getActivationCode($_POST['username']);
                //var_dump($activationCode);
                if ($activationCode===false) throw new Exception ("Something wrong with the UserLogic. GETTINGCODE Probably, i have fucked up something with the other table insertion (ActivationUser).");
                else{
                    sendActivationEmail($_POST['email'],$activationCode);
                }
            }
            
        }
    }
    catch (Exception $ex){
        errorRerout("/signup.php",$ex->getMessage());
    }
    $_SESSION['username']=$_POST['username'];
    $_SESSION['privilege']='user';
    $_SESSION['active']=false;
    $_SESSION['email']=$_POST['email'];
    $_SESSION['name']=$_POST['name'];
    $_SESSION['password']=$_POST['password']; # MAYBE DON't???
    header("Location: /profile.php");

}

if (isset($_POST['No_Registration'])){
    #PASS
}

if (isset($_POST['Sign_in'])){ #PASS STOP USING  DIRECT DATABASE AND MAIL CONNECTIONS!!!!!!!
    try{
        validate("Username",$_POST['username']);
        validate("Password",$_POST['password']);
        require_once "db.php";
        $myDb = new DB();
        $row=$myDb->trySignIn($_POST['username'],$_POST['password']);
        if($row===false){
            throw new Exception ("Such user doesn\'t exists.(can add checking for username or pass incorrection)");
        } else{
            $_SESSION['username']=$row->username;
            $_SESSION['privilege']=$row->privilege;
            $_SESSION['active']=$row->active;
            $_SESSION['email']=$row->email;
            $_SESSION['name']=$row->name;
            $_SESSION['password']=$row->password;  # MAYBE DON't???
        }
    }
    catch (Exception $ex){
        errorRerout("/signin.php",$ex->getMessage());
    }
    header("Location: /profile.php");
    
}

if (isset($_POST['Change_email'])){ #PASS STOP USING  DIRECT DATABASE AND MAIL CONNECTIONS!!!!!!!
    try{
        $newEmail=$_POST['email'];
        if ($newEmail == $_SESSION['email']) errorRerout("/profile.php", "Ha-ha, very funny");
        validateEmail($_POST['email']);
        DB_changeEmail($newEmail);
        DB_deactivateUser($_SESSION['username']);
        require_once "db.php";
        $myDb = new DB();
        $myDb->generateActivation($_SESSION['username'],$newEmail);
        require_once "Mail.php";
        sendActivationEmail($newEmail,$myDb->getActivationCode($_SESSION['username']));
        errorRerout("/signin.php","Changes accepted. Sign in again pls :3 And remember to activate your new email first");
    }
    catch (Exception $ex){
        try{
            restoreDBFromSession($_SESSION['username'],$_SESSION);
        }
        catch (Exception $ex){
            errorRerout("/index.php",$ex->getMessage());
        }
        errorRerout("/profile.php",$ex->getMessage());
    }
}

if (isset($_POST['Change_name'])) { #PASS STOP USING  DIRECT DATABASE AND MAIL CONNECTIONS!!!!!!!
    try{
        $newName=$_POST['name'];
        if ($newName==$_SESSION['name']) errorRerout("/profile.php", "Ha-ha, very funny");
        validateName($newName);
        DB_changeName($newName);
        errorRerout("/signin.php","Changes accepted. Sign in again pls :3");
    }
    catch (Exception $ex){
        try{
            restoreDBFromSession($_SESSION['username'],$_SESSION);
        }
        catch (Exception $ex){
            errorRerout("/index.php",$ex->getMessage());
        }
        errorRerout("/profile.php",$ex->getMessage());
    }
}

if (isset($_POST['Change_password'])){
    try{
        $newPassword=$_POST['password'];
        if ($newPassword==$_SESSION['password']) errorRerout("/profile.php", "Ha-ha, very funny");
        validatePassword($newPassword);
        DB_changePassword($newPassword);
        errorRerout("/signin.php","Changes accepted. Sign in again pls :3");
    }
    catch (Exception $ex){
        try{
            restoreDBFromSession($_SESSION['username'],$_SESSION);
        }
        catch (Exception $ex){
            errorRerout("/index.php",$ex->getMessage());
        }
        errorRerout("/profile.php",$ex->getMessage());
    }
}

//if (isset($_POST['Change_privilege'])){

//}
#PASS possible to fix?
require_once "vendor/autoload.php";
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

if (isset($_POST['Upload_photo'])){
    try{
        #Checks if file exists
        if (!is_uploaded_file($_FILES['photo']['tmp_name'])) throw new Exception("File hasn\'t been chosen");
        validate("PhotoName",$_POST['Name']);

        #Checks the type of the file
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
                $finfo->file($_FILES['photo']['tmp_name']),
                array(
                    'jpg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                ),
                true
            )) throw new Exception("Wrong file format");

        #Checks the size of the file
        if ($_FILES['photo']['size'] > 50000000) {
            throw new RuntimeException('Exceeded filesize limit.');
        }

        #PASS (DOCKER TROUBLES)
        if (!file_exists('./imagecache/')) {        #NOT WORKING WITH DOCKER PASS
            echo "GG";                                      #NOT WORKING WITH DOCKER PASS
                mkdir('/imagecache/',0777);  #NOT WORKING WITH DOCKER PASS
        }
        #NOT WORKING WITH DOCKER PASS
        #folder for the images is username (Ydobno kstati)
        $folderName=$_SESSION['username'];

        #Filename - time of upload (easy, elegant, useful)
        $keyName=time();

        #temp dir, for getting the file from $_FILES (necessary in php)
        $tempFilePath = realpath(dirname(getcwd()));
        $tempFilePath.='/html/imagecache';
        move_uploaded_file($_FILES['photo']['tmp_name'],"$tempFilePath/".$_FILES['photo']['name']);

        #checks width and height
        list($width, $height) = getimagesize("$tempFilePath/".$_FILES['photo']['name']);
        if ($width<$GLOBALS['imagesMinWidth']) throw new Exception("Image is way too small (width)");
        if ($height<$GLOBALS['imagesMinHeight']) throw new Exception("Image is way too small (height)");

        #S3
        S3_addPhoto($folderName.'/'.$keyName,$tempFilePath.'/'.$_FILES['photo']['name']);
        #DB
        DB_addPhotoName($_SESSION['username'],$_POST['Name']);
    }
    catch (\Aws\S3\Exception\S3Exception $ex){
        errorRerout("/index.php",$ex->getMessage().' S3');
    }
    catch (Exception $ex){
        errorRerout("/profile.php","USUAL ".$ex->getMessage());
    }
    errorRerout("/profile.php","Your image has been uploaded ^_^");
}

if (isset($_POST['Delete_photo'])){
    try {
        $deleteName = $_POST['Name'];
        #PASS are you sure about that?
        $number = DB_getPhotoNumber($_SESSION['username'],$deleteName);
        deletePhotoFromDB($_SESSION['username'],$number);
        $S3PhotoNames = S3_getPhotoNames($_SESSION['username']);
        $S3PhotoName = $S3PhotoNames['Contents'][$number]['Key'];
        S3_deletePhoto($S3PhotoName);
    }
    catch (Exception $ex){
        errorRerout("/profile.php",$ex->getMessage());
    }
    catch (\Aws\S3\Exception\S3Exception $ex){
        errorRerout("/profile.php",$ex->getMessage().' S3');
    }
    errorRerout("/profile.php","Your image has been deleted.");
}

if (isset($_POST['Subscribe'])){
    //echo $_POST['username'];
    try {
        $currentUser = $_SESSION['username'];
        $targetUser  = $_POST['username'];
        DB_SUBS_subscribe($currentUser,$targetUser);
    }
    catch (Exception $ex){
        errorRerout("/profile.php",$ex->getMessage());
    }
    errorRerout("/users/".$_POST['name'],"Done!");

}
if (isset($_POST['UnSubscribe'])){
    try{
        $currentUser = $_SESSION['username'];
        $targetUser  = $_POST['username'];
        DB_SUBS_unsubscribe($currentUser,$targetUser);
    }
    catch (Exception $ex){
        errorRerout("/profile.php",$ex->getMessage());
    }
    errorRerout("/users/".$_POST['name'],"Done!");
}
if (isset($_POST['Comment'])){
    //echo $_SERVER['HTTP_REFERER'];
    //echo "WE ARE HERE";
    $prev = $_SERVER['HTTP_REFERER'];
    $targetBackURL;
    if (strpos( $prev,   "users"))      $targetBackURL="/users/".basename($prev);
    if (strpos( $prev,   "index"))      $targetBackURL="/index.php";
    if (strpos( $prev,   "profile"))    $targetBackURL="/profile.php";
    try{
        $comment = fixSQLInjection($_POST['comment']);
        if(strlen($comment)<1 or strlen($comment)>55) throw new Exception("Comments must be 1-55 characters in length");
        DB_addComment($_POST['targetUser'],$_SESSION['name'], $_POST['imageName'], $comment);
    }
    catch(Exception $ex){
        errorRerout($targetBackURL,$ex->getMessage());
    }
    errorRerout($targetBackURL,"Done!");
}

echo "Not ready yet.";
?>