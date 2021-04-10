<?php
 if(!defined ('MADELINE_BRANCH'))
  define('MADELINE_BRANCH', '5.1.34');
 require_once 'madeline.php';
 require_once 'SessionManager.php';
 
 class Bot
 {
 
  private static $Instance = null;
  
  protected $Bot_Session_Path = null;
 
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
   $Session->Get_Sessions_Ready();
   $this->LoadSettings();
   $this->LoadBot();
  }
  
  public static function GetInstance(){
   return self::$Instance ?? self::$Instance = new Bot();
  }
  protected function LoadSettings(){
  $this->Bot_Session_Path = '/root/ShareYourLinkBody/Sessions/Bot/Bot.madeline';
  }
  protected function LoadBot(){
   try {
    $this->MLP = new \danog\MadelineProto\API($this->Bot_Session_Path,$this->settings);
    if ($this->MLP->getSelf() === false){
     throw new Exception('CLIENT_SELF_IS_FALSE');
    }
   }
   catch (Exception $ExceptionWhileLoadingBotSession){
   echo $ExceptionWhileLoadingBotSession;
   }
  }
  
  public function __destruct()
  {
   // TODO: Implement __destruct() method.
   $this->MLP->stop();
   unset($this->MLP);
  }
 }
 
 
 
// $Bot = Bot::GetInstance();
// echo json_encode($Bot->MLP->getSelf());
 
 
 