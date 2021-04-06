<?php
require_once 'madeline.php';
$Settings = [];
$Settings['app_info']['api_id'] = 2421776;
$Settings['app_info']['api_hash'] = '45570937e7dcee90a0234c3983112e87';
// $Settings['app_info']['api_id']= 2496;
// $Settings['app_info']['api_hash']="8da85b0d5bfe62527e5b244c209159c3";
 try {
  $MadelineProto = new \danog\MadelineProto\API('Sessions/ClientBackup/ClientBackup.madeline',$Settings);
  $MadelineProto->async(false);
  $MadelineProto->phoneLogin($MadelineProto->readline('Enter your phone number: '));
  $authorization = $MadelineProto->completePhoneLogin($MadelineProto->readline('Enter the phone code: '));
  if ($authorization['_'] === 'account.password') {
   $authorization = $MadelineProto->complete2falogin($MadelineProto->readline('Please enter your password (hint '.$authorization['hint'].'): '));
  }
  if ($authorization['_'] === 'account.needSignup') {
   $authorization = $MadelineProto->completeSignup($MadelineProto->readline('Please enter your first name: '), readline('Please enter your last name (can be empty): '));
  }
 
  $Self = $MadelineProto->getSelf();
  echo json_encode($Self);
 }
 catch (Exception $e){
  echo $e;
 }







