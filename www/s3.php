<?php session_start();
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
class myS3
{
    private $s3;
    private $bucketName;
    public function __construct()
    {
        require_once "vendor/autoload.php";
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        $IAM_KEY            =   getenv("S3_KEY");
        $IAM_SECRET         =   getenv("S3_SECRET_K");
        $this->bucketName   =   getenv("S3_BUCKET");
        $this->s3 = S3Client::factory(
            array(
                'credentials' => array(
                    'key' => $IAM_KEY,
                    'secret' => $IAM_SECRET
                ),
                'version' => 'latest',
                'region'  => 'us-east-2'
            )
        );
    }
    public function listObjects($prefix){
        return $this->s3->listObjectsV2([
            'Bucket'=>$this->bucketName,
            'Prefix'=>$prefix
        ]);
    }
    public function addObject($keyName, $sourcePath){
        $this->s3->putObject(
            array(
                'Bucket'=>$this->bucketName,
                'Key' =>  $keyName,
                'SourceFile' => $sourcePath,
                'StorageClass' => 'REDUCED_REDUNDANCY'
            )
        );
    }
    public function deleteObject($keyName){
        $this->s3->deleteObject([
            'Bucket'=>$this->bucketName,
            'Key'=>$keyName
        ]);
    }
    public function getObject($keyName, $destPath){
        $this->s3->getObject([
            'Bucket'=>$this->bucketName,
            'Key'=>$keyName,
            'SaveAs'=>$destPath
        ]);
    }

    //public function
}
?>