<?php
 require_once 'madeline.php';
 require_once 'SessionManager.php';
 class Client
 {
  protected $Client_Session_Path = null;
  
  Private static $Instance = null;
  
  
  protected $MLP = null;
  private function __construct()
  {
   $Session = SessionManager::GetInstance();
   $Session->Get_Sessions_Ready();
   $this->LoadSettings();
   $this->LoadClient();
  }
  
  public static function GetInstance(): \Client
  {
   return self::$Instance ?? self::$Instance = new Client();
  }
  
  protected function LoadClient(){
   $this->MLP = new \danog\MadelineProto\API($this->Client_Session_Path);
   if ($this->MLP->getSelf() === false){
    throw new Exception('CLIENT_SELF_IS_FALSE');
   }
   
  }
  
  protected function LoadSettings(){
  $this->Client_Session_Path = 'D:\wamp64\www\ShareYourLinkBody\Sessions\Client\Client.madeline';
  }
  public function GetFullInfo($Link){
   try {
   $FullInfo = $this->CheckLinkValidity($Link);
   }
   catch (RuntimeException $ExceptionWhileGettingGroupFullInfo){
   switch ($ExceptionWhileGettingGroupFullInfo->getMessage()){
    case 'IS_PRIVATE':{
     try {
      $this->JoinGroup($Link);
      try {
       $FullInfo = $this->CheckLinkValidity($Link);
       $this->MLP->channels->leaveChannel(['channel' => $FullInfo['bot_api_id'], ]);
      }
      catch (RuntimeException $ExceptionWhileGettingGroupFullInfoAfterJoining){
       switch ($ExceptionWhileGettingGroupFullInfoAfterJoining->getMessage()){
        case 'IS_NOT_LINK':{
         throw new RuntimeException('IS_NOT_LINK');
         break;
        }
        case 'USERNAME_INVALID':{
         throw new RuntimeException('USERNAME_INVALID');
         break;
        }
        case 'IS_USER':{
         throw new RuntimeException('IS_USER');
         break;
        }
        case 'UNHANDLED_EXCEPTION_DETECTED':
        case 'UNEXPECTED_FULLINFO_TYPE':
        case 'WRONG_FULLINFO_OBJECT':
         {
          throw new RuntimeException('UNABLE_TO_FIND_INFO');
          break;
         }
        default:{
         throw new RuntimeException('UNHANDLED_EXCEPTION_DETECTED');
         break;
        }
       }
      }
     }
     catch (RuntimeException $ExceptionWhileJoiningPrivateLink){
      switch ($ExceptionWhileJoiningPrivateLink){
       case 'CHANNELS_TOO_MUCH':{
        throw new RuntimeException('CHANNELS_TOO_MUCH');
       }
       case 'UNHANDLED_EXCEPTION_DETECTED':{
        throw new RuntimeException('UNHANDLED_EXCEPTION_DETECTED');
       }
       default:{
        echo $ExceptionWhileJoiningPrivateLink;
        throw new RuntimeException('UNHANDLED_EXCEPTION_DETECTED');
       }
      }
      
     }
     break;
    }
    case 'IS_NOT_LINK':{
     throw new RuntimeException('IS_NOT_LINK');
     break;
    }
    
    case 'USERNAME_INVALID':{
     throw new RuntimeException('USERNAME_INVALID');
     break;
    }
    case 'IS_USER':{
     throw new RuntimeException('IS_USER');
     break;
    }
    case 'UNHANDLED_EXCEPTION_DETECTED':
    case 'UNEXPECTED_FULLINFO_TYPE':
    case 'WRONG_FULLINFO_OBJECT':
    {
     throw new RuntimeException('UNABLE_TO_FIND_INFO');
     break;
    }
    default:{
     break;
    }
    
   }
   }
   return $FullInfo;
  }
 
 
 
  public function JoinGroup($Link){
   try {
    return $this->MLP->messages->importChatInvite(['hash' => ''.$Link, ]);
   }catch (Exception $ExceptionWhileJoiningGroupMotherClient){
    switch ($ExceptionWhileJoiningGroupMotherClient->getMessage()){
     case 'CHANNELS_TOO_MUCH':{
      throw new RuntimeException('CHANNELS_TOO_MUCH');
      break;
     }
     default:{
      echo '#Exception #UnExpected While Joining Group Mother Client'.PHP_EOL.$ExceptionWhileJoiningGroupMotherClient;
      throw new RuntimeException('UNHANDLED_EXCEPTION_DETECTED');
      break;
     }
    }
   }
  
  }
  
  public function CheckLinkValidity($Link){
   try {
    $FullInfo = $this->MLP->getFullInfo($Link);
   }
   catch (Exception $ExceptionWhileGettingFullInfoInMotherClient){
    switch ($ExceptionWhileGettingFullInfoInMotherClient->getMessage()){
     case 'This peer is not present in the internal peer database':{
      throw new RuntimeException('IS_NOT_LINK');
      break;
     }
     case 'You have not joined this chat':{
      throw new RuntimeException('IS_PRIVATE');
      break;
     }
     case 'USERNAME_INVALID':{
      throw new RuntimeException('USERNAME_INVALID');
      break;
     }
     default:{
      echo '#Exception #UnExpected While Getting FullInfo In Mother Client '.PHP_EOL.$ExceptionWhileGettingFullInfoInMotherClient;
      throw new RuntimeException('UNHANDLED_EXCEPTION_DETECTED');
      break;
     }
    }
   }
   if (array_key_exists('type',$FullInfo)){
    switch ($FullInfo['type']){
     case 'user':{throw new \RuntimeException('IS_USER');break;}
     case 'supergroup':
     case 'channel':
      {
      return $FullInfo;
      break;
     }
     default:{
      throw new \RuntimeException('UNEXPECTED_FULLINFO_TYPE');
      break;
     }
    }
   }else{
    throw new RuntimeException('WRONG_FULLINFO_OBJECT');
   }
  }
 
  
  public function End(){
   exit('Exit Have Been Called From Client');
  }
  public function __destruct()
  {
   $this->MLP->stop();
   unset($this->MLP);
  }
 }
 
 
// $Client = Client::GetInstance();
// $Info = $Client->GetFullInfo('https://t.me/joinchat/5gxWSALEeZ5mNTI0');
// echo json_encode($Info);
 
 
 