<?php
 require_once 'madeline.php';
 require_once 'SessionManager.php';
 
 class Bot
 {
 
  private static $Instance = null;
  
  protected $Bot_Session_Path = null;
  
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
  $this->Bot_Session_Path = 'D:\wamp64\www\ShareYourLinkBody\Sessions\Bot\Bot.madeline';
  }
  protected function LoadBot(){
   try {
    $this->MLP = new \danog\MadelineProto\API($this->Bot_Session_Path);
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
 
 
 
 $Bot = Bot::GetInstance();
 echo json_encode($Bot->MLP->getSelf());
 
 
 