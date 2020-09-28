<?php session_start(); 
require_once("Functions.php");
checkErrors();
clearCache("images");
?>
<html>

<head>
    <title>RofloImageService (Main page of the internet )) )</title>
    <style>
        <?php include 'styles/navPanel.css'; ?>
        <?php include 'styles/imagesFlex.css';?>
        body{
            font-family: 'Sans-serif' !important;
            /* color: #444444; */
            /* font-size: 9pt; */
            /* background-color: #FAFAFA; */
        }
    </style>
</head>

<body>
    <div class="topnav">
        <?php

        if ($_SESSION['username']) {
            if ($_SESSION['privelege'] == "admin") {
                #pass
            }
            echo "<a href=/signin.php>Log out</a>";
            echo "<a href=/profile.php>My Profile</a>";
        } else {
            echo "<a href=/signin.php>Sign in</a>";
        }
        echo "<a href=/users.php>Users list</a>";
        ?>
    </div>
    <?php

    //$_SESSION['ERROR'] = "test";
    //require_once "db.php";
    //$myDb = new DB();
    //$result = $myDb->DB_getAllUsers();
    //print_r(get_object_vars($result));
    //30L0TOII PANTEON
    //echo "GG".count(array_keys(get_object_vars($result)));
    //
    echo "DEBUG: <br/>";
    foreach (DB_getAllUsers() as $row){
        
        print_r ($row);
        echo "<br>";
    }
    if (isset($_SESSION['username']) && $_SESSION['active']==true){  //if a real user is here...
        form_subs_images($_SESSION['username']);
    }



    //echo time();


//    require 'vendor/autoload.php';
//    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
//    $dotenv->load();
//
//	$bucketName = getenv("S3_BUCKET");
//	$filePath = './something.png';
//	$keyName = basename($filePath);
//
//	$IAM_KEY = getenv("S3_KEY");
//    $IAM_SECRET = getenv("S3_SECRET_K");
//
//	use Aws\S3\S3Client;
//	use Aws\S3\Exception\S3Exception;
//
//	// Set Amazon S3 Credentials
//	$s3 = S3Client::factory(
//		array(
//			'credentials' => array(
//				'key' => $IAM_KEY,
//				'secret' => $IAM_SECRET
//			),
//			'version' => 'latest',
//			'region'  => 'us-east-2'
//		)
//	);

//	try {
//		if (!file_exists('/tmp/tmpfile')) {
//			mkdir('/tmp/tmpfile');
//		}
//
//		// Create temp file
//		$tempFilePath = '/tmp/tmpfile/' . basename($filePath);
//		$tempFile = fopen($tempFilePath, "w") or die("Error: Unable to open file.");
//		$fileContents = file_get_contents($filePath);
//		$tempFile = file_put_contents($tempFilePath, $fileContents);
//
//
//		// Put on S3
//		$s3->putObject(
//			array(
//				'Bucket'=>$bucketName,
//				'Key' =>  $keyName,
//				'SourceFile' => $tempFilePath,
//				'StorageClass' => 'REDUCED_REDUNDANCY'
//			)
//		);
//	} catch (S3Exception $e) {
//		echo $e->getMessage();
//	} catch (Exception $e) {
//		echo $e->getMessage();
//	}
 

    ?>
<!--    <img src="/slir/w500-h400-c1.1/imagecache/2.png"/>-->

</body>
</html>