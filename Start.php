<?php
 if(!defined ('MADELINE_BRANCH'))
  define('MADELINE_BRANCH', '5.1.34');
require_once 'madeline.php';
require_once 'SessionManager.php';
require_once 'Client.php';
require_once 'Bot.php';
require_once 'MyEventHandler.php';

 try {
  $Bot = Bot::GetInstance();
  $Bot->MLP->startAndLoop(MyEventHandler::class);
 }
 catch (Exception $e){
  echo $e;
 }







