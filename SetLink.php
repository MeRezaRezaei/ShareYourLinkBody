<?php
 if(!defined ('MADELINE_BRANCH'))
  define('MADELINE_BRANCH', '5.1.34');
 require_once 'madeline.php';
 require_once 'DB.php';
 require_once 'SessionManager.php';
 require_once 'Bot2.php';
 use danog\MadelineProto\EventHandler;
 use danog\MadelineProto\Tools;
 use danog\MadelineProto\API;
 use danog\MadelineProto\Logger;
 use danog\MadelineProto\RPCErrorException;
 try {
  /**
   * Event handler class.
   */
  class BotEventHandler extends EventHandler
  {
   protected $DB = null;
   /**
    * @var int|string Username or ID of bot admin
    */
   const ADMIN = "@ShareYourLinkManager"; // Change this
   /**
    * Get peer(s) where to report errors
    *
    * @return int|string|array
    */
   public function getReportPeers()
   {
    return [self::ADMIN];
   }
   /**
    * Called on startup, can contain async calls for initialization of the bot
    *
    * @return void
    */
   public function onStart()
   {
   }
   /**
    * Handle updates from supergroups and channels
    *
    * @param array $update Update
    *
    * @return void
    */
   public function onUpdateNewChannelMessage(array $update): \Generator
   {
    return $this->onUpdateNewMessage($update);
   }
   /**
    * Handle updates from users.
    *
    * @param array $update Update
    *
    * @return \Generator
    */
   public function onUpdateNewMessage(array $update): \Generator
   {
    try {
     if (
      $update['message']['_'] === 'messageEmpty'
      || ($update['message']['out'] ?? false)
      || $update['message']['_'] !== 'message'
      || !isset($update['message']['message'])
      || $update['_'] === 'updateNewChannelMessage'
      || $update['message']['from_id'] == 1730644510
     ) {
      echo json_encode($update, JSON_PRETTY_PRINT);
      echo PHP_EOL.'Ignored'.PHP_EOL;
      return;
     }
     echo json_encode($update, JSON_PRETTY_PRINT);
     $WelcomeMessage = ''
      . 'سلام به ربات اشتراک گذاری لینک خوش امدید' . PHP_EOL . PHP_EOL
      . 'من میتونم اطلاعات گروه ها و کانال های تلگرام رو استخراج کنم' . PHP_EOL . PHP_EOL
      . 'و در کانال متصل به ربات به اشتراک بگذارم فقط کافیه' . PHP_EOL . PHP_EOL
      . 'لینک یا یوزرنیم یک کانال یا گروه رو برای من بفرستید' . PHP_EOL . PHP_EOL
      . 'شاد پیروز و سلامت باشید';
     if ($update['message']['message'] === '/start') {
  
      yield $this->messages->sendMessage(['peer' => $update, 'message' => $WelcomeMessage, 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
      return;
     }
     try {
      $this->messages->sendMessage(['peer' => $update, 'message' => 'درحال بررسی لینک برای ثبت در صف انتظار', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
      try {
       if ($this->DB->Set_Link($update['message']['message'])){
        $this->messages->sendMessage(['peer' => $update, 'message' => 'لینک ثبت شد', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
        return ;
       }
      }catch (DBException $DBException){
       echo $DBException->GetMysqlError();
      }
      
      
      $this->messages->sendMessage(['peer' => $update, 'message' => 'قادر به ثبت لینک نبودیم', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
 
     }catch (Exception $e){
      echo $e;
      if ($e->getMessage() === 'var_export does not handle circular references'){
       echo 'This problem have a long story!';
      }
     }
     
    } catch (DBException $DBException){
     echo $DBException;
    }catch (RuntimeException $runtimeException){
     echo $runtimeException;
    } catch (RPCErrorException $e) {
  $this->report("Surfaced: $e");
  } catch (Exception $e) {
   if (\stripos($e->getMessage(), 'invalid constructor given') === false) {
    $this->report("Surfaced: $e");
   }
  }
    
   }
   public function __construct($API)
   {
    try {
    $this->DB = DB::GetInstance();
   } catch (Exception $e){
  echo $e;
  }catch (DBException $DBException){
   echo $DBException;
  }
    parent::__construct($API);
    
   }
 
 
  }
  
  
 }
   catch (Exception $e){
  echo $e;
  }catch (DBException $DBException){
  echo $DBException;
 }catch (RuntimeException $runtimeException){
  echo $runtimeException;
 }
 
 try {
  $Bot = Bot2::GetInstance();
  $Bot->MLP->startAndLoop(BotEventHandler::class);
 }
 catch (Exception $e){
  echo $e;
 }catch (DBException $DBException){
  echo $DBException;
 }catch (RuntimeException $runtimeException){
  echo $runtimeException;
 } catch (RPCErrorException $e) {
 $this->report("Surfaced: $e");
} catch (Exception $e) {
 if (\stripos($e->getMessage(), 'invalid constructor given') === false) {
  $this->report("Surfaced: $e");
 }
}