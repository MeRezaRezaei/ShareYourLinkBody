<?php
 
 
 try {
  if(!defined ('MADELINE_BRANCH'))
   define('MADELINE_BRANCH', '5.1.34');
  require_once 'madeline.php';
  require_once 'SessionManager.php';
  
  class Bot2
  {
   
   private static $Instance = null;
   
   protected $Bot2_Session_Path = null;
   
   protected $settings = [
    'serialization' => [
     'cleanup_before_serialization' => true,
    ],
    'logger' => [
     'max_size' => 1*1024*1024,
    ],
    'peer' => [
     'full_fetch' => false,
     'cache_all_peers_on_startup' => false,
    ],
    'db'            => [
     'type'  => 'mysql',
     'mysql' => [
      'host'     => 'localhost',
      'port'     => '3306',
      'user'     => 'root',
      'password' => '19499825',
      'database' => 'bot',
     ]
    ]
   ];
   
   public $MLP = null;
   
   private function __construct()
   {
    $Session = SessionManager::GetInstance();
    $Session->Get_Bot2_Ready();
    $this->LoadSettings();
    $this->LoadBot2();
   }
   
   public static function GetInstance(){
    return self::$Instance ?? self::$Instance = new Bot2();
   }
   protected function LoadSettings(){
    $this->Bot2_Session_Path = '/root/ShareYourLinkBody/Sessions/Bot2/Bot2.madeline';
   }
   protected function LoadBot2(){
    try {
     $this->MLP = new \danog\MadelineProto\API($this->Bot2_Session_Path,$this->settings);
     if ($this->MLP->getSelf() === false){
      throw new Exception('CLIENT_SELF_IS_FALSE');
     }
    }
    catch (Exception $ExceptionWhileLoadingBot2Session){
     echo $ExceptionWhileLoadingBot2Session;
    }
   }
   
   public function __destruct()
   {
    // TODO: Implement __destruct() method.
    $this->MLP->stop();
    unset($this->MLP);
   }
  }
  
  
  
  $Bot2 = Bot2::GetInstance();
 }
 catch (Exception $exception){
  echo $exception;
 }
 catch (DBException $DBException){
  echo $DBException;
 }
 catch (RuntimeException $runtimeException){
  echo $runtimeException;
 }
 
  //echo json_encode($Bot2->MLP->getSelf());