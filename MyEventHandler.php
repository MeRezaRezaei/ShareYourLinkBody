<?php
 
 use danog\MadelineProto\EventHandler;
 use danog\MadelineProto\Tools;
 use danog\MadelineProto\API;
 use danog\MadelineProto\Logger;
 use danog\MadelineProto\RPCErrorException;
 
 /**
  * Event handler class.
  */
 class MyEventHandler extends EventHandler
 {
  /**
   * @var int|string Username or ID of bot admin
   */
  const ADMIN = "ShareYourLinkManager"; // Change this
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
   if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
    return;
   }
   $WelcomeMessage = ''
    .'سلام به ربات اشتراک گذاری لینک خوش امدید'.PHP_EOL.PHP_EOL
    .'من میتونم اطلاعات گروه ها و کانال های تلگرام رو استخراج کنم'.PHP_EOL.PHP_EOL
    .'و در کانال متصل به ربات به اشتراک بگذارم فقط کافیه'.PHP_EOL.PHP_EOL
    .'لینک یا یوزرنیم یک کانال یا گروه رو برای من بفرستید'.PHP_EOL.PHP_EOL
    .'شاد پیروز و سلامت باشید'
   ;
   if($update['message']['message'] === '/start'){
    
    yield $this->messages->sendMessage(['peer' => $update, 'message' => $WelcomeMessage, 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
    return;
   }
   
   $res = \json_encode($update, JSON_PRETTY_PRINT);
   
   try {
    yield $this->messages->sendMessage(['peer' => $update, 'message' => "<code>$res</code>", 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
    if (isset($update['message']['media']) && $update['message']['media']['_'] !== 'messageMediaGame') {
     yield $this->messages->sendMedia(['peer' => $update, 'message' => $update['message']['message'], 'media' => $update]);
    }
   } catch (RPCErrorException $e) {
    $this->report("Surfaced: $e");
   } catch (Exception $e) {
    if (\stripos($e->getMessage(), 'invalid constructor given') === false) {
     $this->report("Surfaced: $e");
    }
   }
  }
 }