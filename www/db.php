<?php session_start();
class DB
{
    private $myDb;
    public function __construct()
    {
        require_once "vendor/autoload.php";
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        $host = getenv("DB_HOST");
        $dbname = getenv("DB_NAME");
        $port = getenv("DB_PORT");
        $user = getenv("DB_USER");
        $pass = getenv("DB_PASSWORD");
        try {
            $this->myDb = new PDO("pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$pass");
            $this->myDb->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        } catch (PDOexception $ex) {
            echo $ex->getMessage();
            #pass
        }
    }
    public function generateActivation($username,$email){
        $activationCode=md5($email.time());
        $stmt=$this->myDb->prepare("INSERT INTO public.\"usersActivation\"(username, code) VALUES ('$username','$activationCode');");
        $stmt->execute();
        if ($stmt->fetch()!==false) return true; else return false;
    }
    public function getActivationCode($username){
        $stmt=$this->myDb->prepare("SELECT code FROM public.\"usersActivation\" WHERE username='$username'");
        $stmt->execute();
        return ($stmt->fetch()->code);
    }
    public function DB_getAllUsers()
    {
        return $this->myDb->query("SELECT * FROM users")->fetchAll();
    }
    public function query($sql)
    {
        return $this->myDb->query($sql);
    }
    public function addUser($username, $password, $email, $name)
    {
        $stmt = $this->myDb->prepare("INSERT INTO public.\"usersImages\"(username,images) VALUES('$username','{}')");
        $stmt->execute();
        $stmt = $this->myDb->prepare("INSERT INTO public.subscriptions(username,subscriptions) VALUES('$username','{}')");
        $stmt->execute();
        $stmt=$this->myDb->prepare("INSERT INTO public.users(email, username, password, privilege, name) VALUES ('$email','$username', '$password', 'user', '$name');");
        $stmt->execute();
        return $stmt->fetch();
    }
    public function deleteActivationCode($username){
        $stmt = $this->myDb->prepare("DELETE FROM public.\"usersActivation\" WHERE username='$username'");
        $stmt->execute();
    }
    public function activateUser($username,$activationCode){ #PASS change return to Throw (maybe)
        $stmt = $this->myDb->prepare("SELECT * FROM public.\"usersActivation\" WHERE username='$username' AND code='$activationCode'");
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result->username==$username && $result->code==$activationCode){
            $stmt=$this->myDb->prepare("UPDATE public.users SET active=True WHERE username='$username' RETURNING username,active");
            $stmt->execute();
            $result=$stmt->fetch();
            if ($result->username==$username && $result->active==true){
                #if we are here, it means, that the user was activated, so...
                $this->deleteActivationCode($username);
                return true;
            } else{
                return "Something is wrong with the users DB. Probably username doesn't exist";
            }
        } else{
            return "Something is wrong with the usersActivation DB. Username or activation codes are incorrect";
        }
    }
    public function trySignIn($username,$password){
        $stmt = $this->myDb->prepare("SELECT * FROM public.users WHERE username='$username' and password='$password'");
        $stmt->execute();
        $result=$stmt->fetch();
        if ($result->username==$username && $result->password==$password){
            return $result;
        } else{
            return false;
        }
    }
    public function DB_getUserData($name){
        $stmt = $this->myDb->prepare ("SELECT * FROM public.users WHERE name='$name'");
        $stmt->execute();
        $result=$stmt->fetch();
        return $result;
    }
    public function DB_userExistance($name){
        $stmt = $this->myDb->prepare ("SELECT * FROM public.users WHERE name='$name'");
        $stmt->execute();
        $result=$stmt->fetch();
        if ($result === false) return false;
        return true;
    }
    public function __destruct()
    {

    }
    public function changeEmail($username, $newEmail){
        $stmt = $this->myDb->prepare ("UPDATE public.users SET email='$newEmail' WHERE username='$username' RETURNING username,email");
        $stmt->execute();
        $result=$stmt->fetch();
        if ($result->username == $username && $result->email = $newEmail) return true;
        else return false;
    }
    public function changeEmail_force($username, $email){
        $this->myDb->query("UPDATE public.users SET email='$email' WHERE username='$username'");
    }
    public function deactivateUser($username){
        $stmt = $this->myDb->prepare("UPDATE public.users SET active=False WHERE username='$username' RETURNING username");
        $stmt->execute();
        $result=$stmt->fetch();
        if ($result->username == $username) return true;
        else return false;
    }
    public function updateEntry($username, $data){
        $email = $data->email;
        $password=$data->password;
        $name=$data->name;
        $active=$data->active;
        $stmt = $this->myDb->prepare("UPDATE public.users SET email='$email',password='$password', name='$name', active=$active WHERE username='$username' RETURNING username,email");
        $stmt->execute();
        $result=$stmt->fetch();
        if ($result->username == $username && $result->email==$email) return true;
        else return false;
    }
    public function get($username, $key='*'){  #__get is not an option, due to the 1 parameters restriction
        $stmt = $this->myDb->prepare("SELECT '$key' from public.users WHERE username='$username'");
        $stmt ->execute();
        $result=$stmt->fetch();
        if ($result!=false){
            return $result;
        } else{
            throw new Exception("Something is wrong with the getter");
        }
    }

    public function set($username, $key, $value){ #__Set is not an option, due to the 2 parameters restrictions
        $stmt = $this->myDb->prepare("UPDATE public.users SET $key ='$value' WHERE username='$username' RETURNING $key");
        $stmt->execute();
        $result = $stmt->fetch();
        if ($result->$key == $value){
            return true;
        } else{
            throw new Exception("Something is wrong with the setter");
        }
    }
    public function addPhotoName($username, $photoName){
        $stmt = $this->myDb->prepare("INSERT INTO public.\"usersImages\"(username) VALUES('$username') RETURNING username");
        $stmt->execute();
        //echo "UPDATE public.\"usersImages\" SET images = images || '{".'$photoName'."}' WHERE username='$username' RETURNING images";
        $photoStmt = $this->myDb->prepare("UPDATE public.\"usersImages\" SET images = images || '{".$photoName."}' WHERE username='$username' RETURNING images");
        $photoStmt->execute();
        #PASS if doesn't exist -> create. if exists -> update
        #PASS if image name wasn't loaded -> throw
        //$result = $stmt->fetch();
        //if ($result->username != $username) throw new Exception("Something went wrong with uploading username to Images DB");
        //$resultPhoto = $photoStmt->fetch();
        //foreach ($resultPhoto as $photo){
        //    if ($photo == $photoName) return true;
        //}
        //throw new Exception("If we are here.. It means that photo name wasn\'t uploaded");
    }

    public function getImagesNames($username){
        $stmt = $this->myDb->prepare("SELECT array_to_json(images) AS images FROM public.\"usersImages\" WHERE username = '$username'");
        $stmt->execute();
        //var_dump($stmt->fetch());
        return $stmt->fetch()->images;
        //return json_decode($stmt->fetch()->images);
    }

    public function setImagesNames($username,$photoNames){
        //json_decode($photoNames);
        //echo "UPDATE public.\"usersImages\" SET images = '$photoNames' WHERE username = '$username'";
        $stmt = $this->myDb->prepare("UPDATE public.\"usersImages\" SET images = '$photoNames' WHERE username = '$username'");
        $stmt->execute();
    }

    public function subscribe($subscriber, $target){
        $stmt = $this->myDb->prepare("INSERT INTO public.subscriptions(username) VALUES('$subscriber')");
        $stmt->execute();
        #yeah, double stmt, obvious error if first one exists... But it's postgres, i have not enough experience with it.
        $stmt=$this->myDb->prepare("UPDATE public.subscriptions SET subscriptions = subscriptions || '{".$target."}' WHERE username='$subscriber'");
        $stmt->execute();
    }

    public function unsubscribe($subscriber, $target){
        #PASS
        #PASS
        #DOES IT REMOVE ALL SUBSCRIPTIONS TO A TARGET???????????????????????????
        #PASS
        #PASS
        #PASS
        $stmt=$this->myDb->prepare("UPDATE public.subscriptions SET subscriptions = array_remove(subscriptions, '$target')");
        $stmt->execute();
    }

    public function listSubs($username){
        #koCTbI/\b
        $stmt = $this->myDb->prepare("INSERT INTO public.subscriptions(username,subscriptions) VALUES('$username','{}')");
        $stmt->execute();
        #konetc koCTbI/\9
        $stmt = $this->myDb->prepare("SELECT array_to_json(subscriptions) AS subscriptions FROM public.subscriptions WHERE username='$username'");
        $stmt->execute();
        //var_dump($stmt->fetch());
        return json_decode($stmt->fetch()->subscriptions);
    }

    public function DB_getName($username){
        $stmt = $this->myDb->prepare("SELECT name FROM public.users WHERE username='$username'");
        $stmt->execute();
        $result=$stmt->fetch();
        return $result;
    }

    public function getAllComments($username){
        $stmt = $this->myDb->prepare("SELECT array_to_json(commentator) AS commentator,array_to_json(\"imageName\") AS \"imageName\",array_to_json(comment) AS comment FROM public.\"usersComments\" WHERE \"targetUser\"='$username'");
        #pass is it safe?
        if ($stmt->execute()!==false){
            return $stmt->fetch();
        }
        else return null;
        #throw new Exception("Oh shit. No comments dude");
        #else return null;#throw new Exception("DB.php. comments error(or maybe not?)");
    }

    public function addComment($targetUser, $commentator, $imageName, $comment){
        $stmt = $this->myDb->prepare("SELECT * FROM public.\"usersComments\" WHERE \"targetUser\"='$targetUser'");
        //echo "SELECT * FROM public.\"usersComments\" WHERE \"targetUser\"='$targetUser'";
        //echo $targetUser;
        #LATEST added fetch
        $result = $stmt->execute();
        $result = $stmt->fetch();
        //var_dump($result);
        //var_dump($result);
        #mb replace vmesto ins + update?
        if ($result==null){
            //echo "DEBUG: addComment select targetName is false!";
            //echo "adding the dude";   
            $stmt = $this->myDb->prepare("INSERT INTO public.\"usersComments\"(\"targetUser\",commentator,\"imageName\",comment) VALUES ('$targetUser','{".$commentator."}','{".$imageName."}','{".$comment."}')");
            $result = $stmt->execute(); #pass
            return;
            #2 raza/
        }
        $stmt = $this->myDb->prepare("UPDATE public.\"usersComments\" SET commentator=commentator || '{".$commentator."}' , \"imageName\"=\"imageName\" || '{".$imageName."}' , comment=comment || '{".$comment."}'  WHERE \"targetUser\"='$targetUser'");
        $stmt->execute();
        
    }

    public function setComments($targetUser,$commentator,$imageName,$comment){
        $stmt = $this->myDb->prepare("UPDATE public.\"usersComments\" SET commentator = '$commentator' , \"imageName\" = '$imageName' , comment='$comment' WHERE \"targetUser\"='$targetUser'");
        $stmt->execute();
    }
}