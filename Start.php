<?php
require_once 'madeline.php';
$Settings = [];
$Settings['app_info']['api_id'] = 2421776;
$Settings['app_info']['api_hash'] = '45570937e7dcee90a0234c3983112e87';
// $Settings['app_info']['api_id']= 2496;
// $Settings['app_info']['api_hash']="8da85b0d5bfe62527e5b244c209159c3";
 try {
  $MadelineProto = new \danog\MadelineProto\API('Sessions/BotBackup/BotBackup.madeline',$Settings);
  $MadelineProto->botLogin('1730644510:AAEivmyuK2rbt-ccZ_zA6jXUKXKWsufgBZc');
  $MadelineProto->async(false);
  $Self = $MadelineProto->getSelf();
  echo json_encode($Self);
 }
 catch (Exception $e){
  echo $e;
 }







