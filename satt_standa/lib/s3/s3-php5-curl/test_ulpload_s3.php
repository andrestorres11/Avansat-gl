<?php

ini_set('display_errors', true);
error_reporting(E_ALL & ~E_NOTICE);

try {

  include("S3.php");



  if (!class_exists('S3')) require_once 'S3.php';

  // AWS access info
  #if (!defined('awsAccessKey')) define('awsAccessKey', 'AKIAIJ34RW6BAKOKS4OQ');
  #if (!defined('awsSecretKey')) define('awsSecretKey', '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');



  if (!defined('awsAccessKey')) define('awsAccessKey', 'AKIAIGDORS4KTSNMEWSA');
  if (!defined('awsSecretKey')) define('awsSecretKey', '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');

 

  $uploadFile = dirname(__FILE__).'/9.jpg'; // File to upload, we'll use the S3 class since it exists
  $bucketName =  'files.spg.s3'; // Temporary bucket
 


  // Check for CURL
  if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll'))
    exit("\nERROR: CURL extension not loaded\n\n");

 
  // Instantiate the class
  $s3 = new S3(awsAccessKey, awsSecretKey, false);

  // List your buckets:
  echo "S3::listBuckets():<pre> ".print_r($s3->listBuckets(), 1)."</pre>\n";


 
  

  #____________________________________ SUBE ____________________________________

  // Get the contents of our bucket
    #$contents = $s3->getBucket($bucketName);
    #echo "S3::getBucket(): Files in bucket {$bucketName}:<pre> ".print_r($contents, 1)."</pre>";


  #____________________________________ SUBE ____________________________________
  $S3Path = "files/nelson12345";

  echo "<pre>"; print_r($uploadFile); echo "</pre>";
  echo "<pre>"; print_r($bucketName); echo "</pre>";
  echo "<pre>"; print_r(baseName($uploadFile)); echo "</pre>";
  echo "<pre>"; print_r(S3::ACL_PUBLIC_READ); echo "</pre>"; 
  echo "<pre>"; print_r($S3Path); echo "</pre>"; 

  

  // Put our file (also with public read access)
  $mUpload = $s3->putObjectFile($uploadFile, $bucketName, $S3Path, S3::ACL_AUTHENTICATED_READ);

  echo "<pre>Response Put<br>"; print_r( var_dump($mUpload) ); echo "</pre>";
  echo "<pre>Error Log Put Upload<br>"; print_r( var_dump($s3 -> getErrorBucket() ) ); echo "</pre>";

  if ($mUpload) {
    echo "S3::putObjectFile(): File copied to {$bucketName}/".baseName($uploadFile).PHP_EOL;
  }else {
    echo "S3::putObjectFile(): Failed to copy file\n";
  }




  
} catch (Exception $e) {
  echo "<pre>Catch<br>"; print_r($e); echo "</pre>";
}



?>