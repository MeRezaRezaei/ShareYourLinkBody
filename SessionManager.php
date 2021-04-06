<?php
 
 
 class SessionManager
 {
  // back up sessions path
  protected $Bot_Backup_Session_Path = null;
  protected $Client_Backup_Session_Path = null;
  // sessions for use path
  protected $Bot_Session_Path = null;
  protected $Client_Session_Path = null;
  
  protected $SessionCopiedBefore = false;
  // singleton design pattern instance
  private static $Instance = null;
  
  private function __construct()
  {$this->LoadSettings();}
  protected function LoadSettings(){
   $this->Bot_Backup_Session_Path = 'D:\wamp64\www\ShareYourLinkBody\Sessions\BotBackup\BotBackup.madeline';
   $this->Client_Backup_Session_Path = 'D:\wamp64\www\ShareYourLinkBody\Sessions\ClientBackup\ClientBackup.madeline';
   // session for use
   $this->Bot_Session_Path = 'D:\wamp64\www\ShareYourLinkBody\Sessions\Bot\Bot.madeline';
   $this->Client_Session_Path = 'D:\wamp64\www\ShareYourLinkBody\Sessions\Client\Client.madeline';
  }
  public static function GetInstance(): \SessionManager
  {return self::$Instance ?? self::$Instance = new SessionManager();}
  
  public function Get_Sessions_Ready(){
   if ($this->SessionCopiedBefore === false){
    
    if (!$this->Does_Bot_Session_Exist()){
     throw new Exception('BOT_BACKUP_SESSION_DOES_NOT_EXIST');
    }
    
    if (!$this->Does_Client_Session_Exist()){
     throw new Exception('CLIENT_BACKUP_SESSION_DOES_NOT_EXIST');
    }
    $this->Clear_Bot_Session_If_Exist();
    $this->Clear_Client_Session_If_Exist();
 
    if (!$this->Copy_Bot_Session()){
     throw new Exception('UNABLE_TO_COPY_BOT_SESSION');
    }
    if (!$this->Copy_Client_Session()){
     throw new Exception('UNABLE_TO_COPY_CLIENT_SESSION');
    }
    return false;
   }
   return $this->SessionCopiedBefore;
  }
  
  protected function Clear_Bot_Session_If_Exist(): bool {
   if ($this->Bot_Session_Path === null){$this->LoadSettings();}
   if (file_exists($this->Bot_Session_Path)){
    if (unlink($this->Bot_Session_Path)){
     return true;
    }
    throw new Exception('UNABLE_TO_DELETE_BOT_SESSION');
   }
   return true;
  }
 
  protected function Clear_Client_Session_If_Exist(): bool {
   if ($this->Client_Session_Path === null){$this->LoadSettings();}
   if (file_exists($this->Client_Session_Path)){
    if (unlink($this->Client_Session_Path)){
     return true;
    }
    throw new Exception('UNABLE_TO_DELETE_CLIENT_SESSION');
   }
   return true;
  }
  
  protected function Copy_Bot_Session(): bool {
   return copy($this->Bot_Backup_Session_Path,$this->Bot_Session_Path);
  }
  
  protected function Copy_Client_Session(): bool {
   return copy($this->Client_Backup_Session_Path,$this->Client_Session_Path);
  }
  
  
  protected function Does_Bot_Session_Exist(): bool {
   if ($this->Bot_Backup_Session_Path === null){$this->LoadSettings();}
  return file_exists($this->Bot_Backup_Session_Path);
  }
  
  protected function Does_Client_Session_Exist(): bool {
   if ($this->Client_Backup_Session_Path === null){$this->LoadSettings();}
   return file_exists($this->Client_Backup_Session_Path);
  }
  
 }
 
 $Session = SessionManager::GetInstance();
 $Session->Get_Sessions_Ready();