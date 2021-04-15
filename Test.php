<?php
 if (!\file_exists('madeline.php')) {
  \copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
 }
 define('MADELINE_BRANCH', '5.1.34');
 include 'madeline.php';
$settings['app_info']['api_id'] =2421776;


$settings['app_info']['api_hash']='45570937e7dcee90a0234c3983112e87' ;
 $MLP = new \danog\MadelineProto\API('/root/ShareYourLinkBody/Sessions/Bot2Backup/Bot2.madeline');
 //$MLP->botLogin('1730644510:AAEivmyuK2rbt-ccZ_zA6jXUKXKWsufgBZc');
// $S = $MLP->getSelf();
// echo json_encode($S);