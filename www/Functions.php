<?php session_start();

$GLOBALS['imagesMinWidth']=500;
$GLOBALS['imagesMinHeight']=400;
defined("TAB1") or define("TAB1", "\t");
#pass make it more "Settingable" globals means more. Slir usees globals etc.
function checkErrors()
{
    if ($_SESSION['ERROR']) {
        echo "<script type=text/javascript>";
        echo "window.alert('".$_SESSION['ERROR']."')";  //ERROR NAME
        echo "</script>";
        unset($_SESSION['ERROR']); 
    }
}

function validate($what,$value){
    if (strlen($value)==0) throw new Exception ("One or more (i can show which one is not filled, but it will be a lot (not really) of ctrl+C ctrl+V stuff (Not really a lot, like 4-8 strok))");
    $funcToCall="validate".$what;
    if(function_exists($funcToCall)){
        $funcToCall($value);  //roflo-vizov, dl9 testa dobavil, prikolnauja fishka
    } else{
        throw new Exception ("Something bad happened. Please, ask the developer about this mistake and send logs )))) (Function.php, validate)");
    }
}

function validateUsername($value){

    $usernameMinLength=5;
    $usernameMaxLength=15;
    if (strlen($value)<=$usernameMinLength || strlen($value)>=$usernameMaxLength){
        throw new Exception ("Wrong username length. It must be >".$usernameMinLength." and <".$usernameMaxLength." in size");
        //return;
    } else{
        if (substr_count($value,'/')!=0) throw new Exception ("Trying to make some url injections??))) Please, don\'t use \"\\\" in the username");
        //throw new Exception ("Wrong username length. It must be >".$usernameMinLength." and <".$usernameMaxLength." in size");
    }
    //return;
}

function validatePassword($value){
    #PASS NOT SAVE PASSWORD
    if (is_int($value)) throw new Exception ("Password must consist of letters and, if needed, numbers");  #PASS (Check the string to int if all are numbers)
    $passwordMinLength=7;
    $passwordMaxLength=18;
    if (strlen($value)<=$passwordMinLength || strlen($value)>=$passwordMaxLength){
        throw new Exception ("Wrong password length. It must be >".$passwordMinLength." and <".$passwordMaxLength." in size");
        //return;
    } else{
        //throw new Exception ("Wrong password length. It must be >".$passwordMinLength." and <".$passwordMaxLength." in size");
    }
    //return;
}

function validateEmail($value){
    //echo strpos($value,'@');
    if (strpos($value,'@')==false ||
        substr_count($value,'@')>1 ||
        strpos ($value,'.'==false) ||
        strpos(substr($value,strpos($value,'@')),'.')==false){ //bcs html 'email' input doesn't check if the value has '.' after the '@'
            throw new Exception ("Something went wrong. Your email is not correct. Correct variant is youremail@example.reg");
    }
    else{
        return;
    }
}

function validateName($value){
    $nameMinLength=2;
    $nameMaxLength=25;
    if (strlen($value)<=$nameMinLength || strlen($value)>=$nameMaxLength){
        throw new Exception ("Wrong name length (no offense). Can\'t belive that your name is not in range of (".$nameMinLength.",".$nameMaxLength.")");
        return;
    } else{
        //throw new Exception ("Wrong name length (no offense). Can't belive that your name is not in range of (".$nameMinLength.",".$nameMaxLength.")");
    }
    //return;
}

function validatePhotoName($value){
    $photoNameMinLength=3;
    if (strpos($value,'\'') !== false) throw new Exception("Yberi \' pls");
    $photoNameMaxLength=14;
    if (strlen($value)<=$photoNameMinLength || strlen($value)>=$photoNameMaxLength){
        throw new Exception("Wrong photo description length");
    }
}

function fixSQLInjection($value){
    //echo "inside the fixer: ";
    $newstring=$value;
    //echo "value=".$value."<br/>";
    $latestPos=0;
    $counter=0;
    if (strpos($value,',')!==false) throw new Exception ("PASS. Comma is unavaiable as a character. PGSQL doesn\'t like it :( ");
    //echo "position of ': ";var_dump(strpos($value,'\'')); echo "<br/>";
    while(($positionOfXrenb=strpos($value,'\'',$latestPos))!==false){
        //echo "GG";
        $newstring = substr($newstring,0,$positionOfXrenb+$counter);
        $newstring.='\'';
        $newstring.=substr($value,$positionOfXrenb,strlen($value)-$positionOfXrenb);
        //var_dump ($newstring);
        //echo "<br/>";
        $latestPos=$positionOfXrenb;
        $value[$positionOfXrenb]='X';
        $counter++;
        
    }
    //var_dump ($newstring);
    return $newstring;
}

function isActivation($url){
    $url=substr($url,1);
    $pos = strpos($url,'/'); if ($pos === false ) return false;
    if (strlen(substr($url,$pos+1))!=32) return false;
    return true;
}

function getKey($url){
    $url=substr($url,1);
    $pos = strpos($url,'/');
    return (substr($url,$pos+1));
}

function isUser($url){
    $url=substr($url,1);
    $pos = strpos($url,'/');
    if (strlen(substr($url,$pos+1))>0){
        if (substr_count($url,'/')>1){
            return false;
        }
        return true;
    }
    else return false;
}

function getName($url){
    $url=substr($url,1);
    $pos = strpos($url,'/');
    //echo $url;
    return substr($url,$pos+1);
}

function activateUser($username,$key){
    //echo $key;
    require_once "db.php";
    $myDb = new DB();
    if(($result=$myDb->activateUser($username,$key))!==true){
        $_SESSION['ERROR']=$result;
        header("Location: /index.php");
    }
    $_SESSION['active']=true;
    return true;
}

function DB_userExistence($name){
    require_once "db.php";
    $myDb = new DB();
    return $myDb->DB_userExistance($name);
}

function form_table($info, $valuesToOutput=null){ #PASS Refract
    #PASS validation
    $fields=array_keys(get_object_vars($info[0]));
    echo "<table border = 1 align=center class=MainTable>";
    echo "<tr>";//Oglavlenie
    foreach ($fields as $field){  //main fields
        if ($valuesToOutput!=null){
            foreach ($valuesToOutput as $valueToOutput){
                if ($field==$valueToOutput){
                    echo "<td>".$field."</td>";
                }
            }
        }
        else{
            echo "<td>".$field."</td>";
        }
    }
    echo "</tr>";
    foreach($info as $row){
        echo "<tr>";
        if ($valueToOutput!=null){
            foreach ($row as $key=>$value){
                foreach ($valuesToOutput as $valueToOutput){
                    if ($valueToOutput==$key){
                        echo "<td>".$value."</td>";
                    }
                }
            }
            
        }
        else{
            foreach ($row as $value){
                echo "<td>".$value."</td>";
            }
        }
        echo "</tr>";
    }
    echo "</table>";
}

function errorRerout($address, $errorMsg){
    $_SESSION['ERROR']=$errorMsg;
    if ($address[0]!='/') $address='/'.$address; //fix vnimatelnosti
    header ("Location: ".$address."");
    exit();
}

function DB_getUserData($name){
    require_once "db.php";
    $myDb = new DB();
    $result=$myDb->DB_getUserData($name);
    if ($result==false) errorRerout("","Something very bad happened. DB_getUserData doesn\'t work");
    return $result;
}

function DB_getName($username){
    require_once "db.php";
    $myDb = new DB();
    $result = $myDb->DB_getName($username);
    if ($result==false) errorRerout("","Something very bad happened. DB_getName doesn\'t work");
    return $result;
}


function DB_getAllUsers(){
    require_once "db.php";
    $myDb = new DB();
    $result=$myDb->DB_getAllUsers();
    if ($result==false) echo "No users yet";
    ###################if ($result==false) errorRerout("","Something very bad happened. DB_getAllUsers doesn\'t work");
    return $result;
}

function DB_changeEmail($newEmail){
    $username = $_SESSION['username'];
    require_once "db.php";
    $myDb = new DB();
    $result = $myDb->changeEmail($username, $newEmail);
    if ($result==false){
        throw new Exception("Something very bad happened. DB_changeEmail doesn\'t work (Probably, such email already exists");
    }
}
function DB_changeName($newName){
    $username=$_SESSION['username'];
    require_once "db.php";
    $myDb = new DB();
    $result = $myDb->set($username,'name',$newName);
}
function DB_changePassword($newPassword){
    $username=$_SESSION['username'];
    require_once "db.php";
    $myDb = new DB();
    $result = $myDb ->set($username,'password',$newPassword);
}

function DB_deactivateUser($username){
    require_once "db.php";
    $myDb = new DB();
    if(!$myDb->deactivateUser($username)) throw new Exception("Something is wrong with the deactivation. Contact support");
}

function restoreDBFromSession($sessionData){
    $username = $sessionData->username;
    require_once "db.php";
    $myDb = new DB();
    if(!$myDb->updateEntry($username,$sessionData)) throw new Exception("Something is wrong with the restoration. Contact support");
}

function clearCache($type=null){
    if ($type!=null){
        switch($type){
            case "images":
                foreach (scandir(getcwd().'/imagecache') as $photo){
                    if (!is_dir($photo)) unlink(getcwd().'/imagecache/'.$photo);
                }
                break;
        }
    }
}


function DB_addPhotoName($username, $photoName){
    require_once "db.php";
    $myDb = new DB();
    try{
        $myDb->addPhotoName($username,$photoName);
    }
    catch (Exception $ex){
        errorRerout("/profile.php",$ex->getMessage());
    }
}

function DB_getImagesNames($username){
    require_once "db.php";
    $myDb = new DB();
    $jsonRes = $myDb->getImagesNames($username);
    return json_decode($jsonRes);
}

function DB_getPhotoNumber($username, $photoName){
    require_once "db.php";
    $myDb = new DB();

    $photos = json_decode($myDb->getImagesNames($username));
    if (($key = array_search($photoName,$photos))!==false){
        return $key;
    } else throw new Exception("No such photo");
}

function DB_addComment($targetUser, $commentator, $targetPhoto, $comment){
    require_once "db.php";
    $myDb = new DB();
    $myDb->addComment($targetUser, $commentator, $targetPhoto, $comment);
}

function DB_getComments($targetUser, $photoName){
    require_once "db.php";
    $myDb = new DB();
    //echo $targetUser;
    $commentsData = $myDb->getAllComments($targetUser);
    if ($commentsData == null) return null;
    $commentsData->comment = json_decode($commentsData->comment);
    $commentsData->commentator = json_decode($commentsData->commentator);
    $commentsData->imageName = json_decode($commentsData->imageName);
    //var_dump($commentsData);
    //echo $commentsData->comment[0];
    $resultComments = array();
    foreach($commentsData->imageName as $number=>$imageName){
        if ($imageName == $photoName){
            $resultComments['imageName'][]=$imageName;
            $resultComments['commentator'][]=$commentsData->commentator[$number];
            //echo $number;
            $resultComments['comment'][]=$commentsData->comment[$number];
            //echo $commentsData->comment[$nubmer];
            //echo $commentsData->comment[$nubmer];
        }
    }
    return $resultComments;
    //echo "comments: ";
    //var_dump(json_decode($comments->imageName));
    //var_dump(json_decode($comments->commentator));
    //var_dump($comments);
    #PASS
}

function to_pg_array($set) {
    settype($set, 'array'); // can be called with a scalar or array
    $result = array();
    foreach ($set as $t) {
        if (is_array($t)) {
            $result[] = to_pg_array($t);
        } else {
            $t = str_replace('"', '\\"', $t); // escape double quote
            if (! is_numeric($t)) // quote only non-numeric values
                $t = '"' . $t . '"';
            $result[] = $t;
        }
    }
    return '{' . implode(",", $result) . '}'; // format
}

function deletePhotoFromDB($username, $number){
    require_once  "db.php";
    $myDb = new DB();
    $photoNames = json_decode($myDb->getImagesNames($username));
    $photoName=$photoNames[$number];
    array_splice($photoNames,$number,1);
    $photoNames = to_pg_array($photoNames);


    $myDb->setImagesNames($username,$photoNames);
    #latest
    $commentsData = $myDb->getAllComments($username);
    if ($commentsData == null) return;
    $commentsData->comment = json_decode($commentsData->comment);
    $commentsData->commentator = json_decode($commentsData->commentator);
    $commentsData->imageName = json_decode($commentsData->imageName);
    $comments = $commentsData->comment;
    $commentators = $commentsData->commentator;
    $imageNames=$commentsData->imageName;
    //echo count($commentsData->imageName);
    //var_dump($commentsData);
    $amountOfComments = count($commentsData->imageName);
    for ($i=0;$i<$amountOfComments;$i++){
        //echo "WE ARE OUTSIDE";
        //echo "CommentsData = ".$commentsData->imageName[$i]. "PhotoName = ".$photoName."<br/>";
        if ($commentsData->imageName[$i] == $photoName){
            //echo "WE ARE HERE";
            unset($comments[$i]);
            unset($commentators[$i]);
            unset($imageNames[$i]);
            //array_splice($comments,$i,1);
            //var_dump($comments);
            //array_splice($commentators,$i,1);
            //array_splice($imageNames,$i,1);
        }
            // array_splice($commentsData->comment,$i,1);
            // array_splice($commentsData->commentator,$i,1);
            // array_splice($commentsData->imageName,$i,1);
        //}
    }
    array_values($comments);
    array_values($commentators);
    array_values($imageNames);
    // var_dump($comments);
    // var_dump($commentators);
    // var_dump($imageNames);
    // var_dump($commentsData);
    //echo $photoName;
    //exit(0);
    $comments=to_pg_array($comments);
    $commentators = to_pg_array($commentators);
    $imageNames = to_pg_array($imageNames);
    //var_dump($comments);
    //var_dump($commentators);
    //var_dump($imageNames);
    //echo "<br/>";
    //var_dump($commentsData);
    $myDb->setComments($username,$commentators,$imageNames,$comments);
}

require_once 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

function S3_addPhoto($name,$path){
    require_once ("s3.php");
    $s3 = new myS3();
    $s3->addObject($name,$path);
}



function S3_deletePhoto($S3PhotoName){
    require_once "s3.php";
    $s3 = new myS3();
    $s3->deleteObject($S3PhotoName);
}

function S3_getPhotoNames($username){
    require_once "s3.php";
    $s3 =  new myS3();
    return $s3->listObjects($username);
}

function S3_downloadPhoto($key,$savePath){
    $tempFilePath = realpath(dirname(getcwd()));
    $tempFilePath.='/html/imagecache';
    $savePath=$tempFilePath.'/'.$savePath;
    require_once "s3.php";
    $s3 =  new myS3();
    $s3->getObject($key,$savePath);
}




function form_comments($targetUsername, $imageName){
    $commentsObj = DB_getComments($targetUsername, $imageName);
    if ($commentsObj!=null){
        foreach ($commentsObj['commentator'] as $number=>$commentSection){
            // print(' ');
            #PASS TABS DOESN'T WORK (NOT EVEN \T WITH DOUBLE QUOTES)
            echo "-------------------------<br/>";
            echo "<span style=margin-left:5px><strong>$commentSection</strong> :".TAB1.$commentsObj['comment'][$number]."</span>";
            echo "<br/>";
        }
    }
    echo "-------------------------<br/>";
    #pass
    echo "<form action=/UsersLogic.php method=POST name=Comment>";
        echo "<label for=comment> Add a comment:<br/></label> <input type=text name=comment><br/>";
        echo "<input type=hidden name=targetUser value='$targetUsername'>";
        echo "<input type=hidden name=imageName value='$imageName'>";
        echo "<button type=submit name=Comment>comment</button>";
    echo "</form>";
}


function form_images($username){
    $imagesDisplayedNames=DB_getImagesNames($username);
    //foreach ($imagesDisplayedNames as $image) echo $image;
    //var_dump($imagesDisplayedNames);
    //var_dump($imagesDisplayedNames);
    $images = S3_getPhotoNames($username);
    if(!isset($images['Contents'])) exit(0);
    echo "<div class=\"flex-container\">";
    $images['Contents']=array_reverse($images['Contents']);
    $imagesDisplayedNames=array_reverse($imagesDisplayedNames);
    //var_dump($images['Contents'][0]['Key']);
    foreach(array_combine($imagesDisplayedNames,$images['Contents']) as $imageName=>$image){
        echo "<div>";
            S3_downloadPhoto($image['Key'],basename($image['Key']));
            echo "<img src=\"/slir/w500-h400-c5x4/imagecache/".basename($image['Key'])."\"/>";
            #latest
            echo "<article class=centerised>".$imageName."</article>";
            
            // echo "<br/><span style=\"font-size:15px; text-align: left !important; line-height:15px\"> Comments:</span><br/>";
            // //comments
            // echo "<span style=\"font-size:15px; line-height:15px\">";
            echo "<span class=comments><strong>Comments:</strong><br/>";
            form_comments($username,$imageName);
            echo "</span>";
            //echo "</span>";
            //comments
        echo "</div>";
    }
    echo "</div>";
}

function DB_getSubscriptions($username){
    require_once "db.php";
    $myDb = new DB();
    return $myDb->listSubs($username);
}

function form_subs_images($username){
    $subscriptionUsernames = DB_getSubscriptions($username);
    $usersImagesNames = array();
    foreach($subscriptionUsernames as $subscriptionUsername){
        $usersImagesNames[$subscriptionUsername]['DB']=DB_getImagesNames($subscriptionUsername);
        if (empty($usersImagesNames[$subscriptionUsername]['DB'])){
            unset($usersImagesNames[$subscriptionUsername]);
            continue;
        }
        foreach(S3_getPhotoNames($subscriptionUsername)['Contents'] as $S3PhotoName){
            $usersImagesNames[$subscriptionUsername]['S3'][]=$S3PhotoName['Key'];
        }
    }
    foreach($usersImagesNames as $userImagesNames){
        foreach ($userImagesNames['S3'] as $userImagesNamesS3){
            S3_downloadPhoto($userImagesNamesS3,basename($userImagesNamesS3));
        }
    }
    echo "<div class=\"flex-container\">";
    foreach(array_diff(scandir('./imagecache', SCANDIR_SORT_DESCENDING),array('..','.')) as $S3PhotoName){
        foreach(array_column($usersImagesNames,'S3') as $user=>$userImagesNamesS3){
            foreach($userImagesNamesS3 as $number=>$userImageNameS3){
                if (strpos($userImageNameS3,$S3PhotoName)!==false){
                    echo "<div>";
                    #latest
                    echo "<article class=centerised>".DB_getName(array_keys($usersImagesNames)[$user])->name.'\'s'."</article>";
                    echo "<img src=\"/slir/w500-h400-c5x4/imagecache/".$S3PhotoName."\"/>";
                  
                    echo "<article class=centerised>".$usersImagesNames[array_keys($usersImagesNames)[$user]]['DB'][$number]."</article>";
                    echo "<span class=comments><strong>Comments:</strong><br/>";
                    #echo "<font style=font-size:15px;>hello</font>"; works well!
                    //comments
                    form_comments(array_keys($usersImagesNames)[$user],$usersImagesNames[array_keys($usersImagesNames)[$user]]['DB'][$number]);
                    echo "</span>";
                    //comments 
                    echo "</div>";
                }
            }
        }
    }
    echo "</div>";
    //var_dump(scandir('./imagecache'));
}

function DB_SUBS_subscribe($current, $target){
    require_once "db.php";
    $myDb = new DB();
    $myDb->subscribe($current,$target);
}

function DB_SUBS_unsubscribe($current, $target){
    require_once "db.php";
    $myDb = new DB();
    $myDb->unsubscribe($current,$target);
}

function isSubscribed($current,$target){
    require_once "db.php";
    $myDb = new DB();
    $subs = $myDb->listSubs($current);
    foreach ($subs as $sub) if ($target == $sub) return true;
    return false;
    //var_dump($result);
    //if($result->fetch()==false) return false;
//    foreach;
}

function form_subscribe($username, $name){
    echo "<form action='/UsersLogic.php' method='POST' name='Subscribe'>";
    echo "<input type='hidden' value='$username' name='username'>";
    echo "<input type='hidden' value='$name' name='name'>";
    echo "<button type='submit' name='Subscribe'>SUBSCRIBE</button>";
    echo "</form>";
}
function form_unsubscribe($username, $name){
    echo "<form action='/UsersLogic.php' method='POST' name='UnSubscribe'>";
    echo "<input type='hidden' value='$username' name='username'>";
    echo "<input type='hidden' value='$name' name='name'>";
    echo "<button type='submit' name='UnSubscribe'>UNSUBSCRIBE</button>";
    echo "</form>";
}

#PASS make one function, like isPatternUrl($url,number_for_checking_the_pattern,$pattern): isPatternUrl($ulr,2,'edit/');