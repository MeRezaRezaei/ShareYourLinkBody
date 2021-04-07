<?php
 require_once 'Client.php';
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
   else{
    yield $this->messages->sendMessage(['peer' => $update, 'message' => 'درحال اماده سازی برای استخراج اطلاعات', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
   $Client = Client::GetInstance();
    try {
     $FullInfo = $Client->GetFullInfo($update['message']['message']);
     yield $this->messages->sendMessage(['peer' => $update, 'message' => 'درحال استخراج اطلاعات', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
     $TypeInWords = '';
     if (
      array_key_exists('type',$FullInfo)
     ){
      if ($FullInfo['type'] === 'supergroup'){
      $TypeInWords = 'گروه';
      }
      elseif ($FullInfo['type'] === 'channel'){
      $TypeInWords = 'کانال';
      }
      else{
       yield $this->messages->sendMessage(['peer' => $update, 'message' => 'نوع لینک قابل شناسایی نیست مجددا تلاش کنید', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
       return;
      }
     }
     else{
      yield $this->messages->sendMessage(['peer' => $update, 'message' => 'نوع لینک از طرف تلگرام اعلام نشده لطفا مجددا تلاش کنید', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
      return;
     }
     $Public_Private_Flag = '';
     $Public_Private_InWords = '';
     if (array_key_exists('username',$FullInfo['Chat'])){
      $Public_Private_Flag = 'public';
      $Public_Private_InWords = 'عمومی';
     }else{
      $Public_Private_Flag = 'private';
      $Public_Private_InWords = 'خصوصی';
     }
     // finding out the difference between group public private with link join chat pattern
     $Link_Public_Private = '';
     if(strpos('/joinchat/',$update['message']['message']) === false){
      $Link_Public_Private = 'private';
     }
     else{
      $Link_Public_Private = 'public';
     }
     if ($Public_Private_Flag === '' || $Link_Public_Private === ''){
      yield $this->messages->sendMessage(['peer' => $update, 'message' => 'قادر به تشخیص عمومی یا خصوصی بودن کانال نیستیم لطفا مجددا امتحان کنید', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
      return;
     }
     $GroupLinkInWords = '';
     // it means the group is public but the requester have sent us private link
     if ($Public_Private_Flag === 'public'){
      if ($Link_Public_Private === 'private'){
       // writing both public and private link
       $GroupLinkInWords = ''
        .$update['message']['message'].PHP_EOL
        .'t.me/'.$FullInfo['Chat']['username'].PHP_EOL
        .'@'.$FullInfo['Chat']['username'].PHP_EOL
       ;
      }
      elseif ($Link_Public_Private === 'public'){
       // both the group and link were public
       $GroupLinkInWords = ''
        .'t.me/'.$FullInfo['Chat']['username'].PHP_EOL
        .'@'.$FullInfo['Chat']['username'].PHP_EOL
       ;
      }
     }
     elseif ($Public_Private_Flag === 'private'){
      if ($Link_Public_Private === 'private'){
       // we have only the private manner
       $GroupLinkInWords = ''
        .$update['message']['message'].PHP_EOL
        .$update['message']['message'].PHP_EOL
       ;
      }
      elseif ($Link_Public_Private === 'public'){
      // some thing went wrong from the detector
       $GroupLinkInWords = ''
        .$update['message']['message'].PHP_EOL
        .$update['message']['message'].PHP_EOL
       ;
      }
     }
     $Description = array_key_exists('description',$FullInfo['full']) ? $FullInfo['full']['description'] : 'ندارد';
     $LinkInfoInWords = ''
      .'عنوان'.PHP_EOL.$FullInfo['Chat']['title'].PHP_EOL
      .'نوع'.PHP_EOL.$TypeInWords.PHP_EOL
      .'وضعیت'.PHP_EOL.$Public_Private_InWords.PHP_EOL
      .'تعداد کاربران واقعی'.PHP_EOL.$FullInfo['full']['participants_count'].PHP_EOL
      .'لینک'.PHP_EOL.$GroupLinkInWords.PHP_EOL
      .'توضیحات'.PHP_EOL.$Description.PHP_EOL
     ;
     yield $this->messages->sendMessage(['peer' => $update, 'message' => $LinkInfoInWords, 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
     return ;
    }
    catch (RuntimeException $RuntimeExceptionWhileGettingLinkFullInfo){
    switch ($RuntimeExceptionWhileGettingLinkFullInfo->getMessage()){
     case 'IS_NOT_LINK':{
      yield $this->messages->sendMessage(['peer' => $update, 'message' => 'متنی که فرستادید لینک نیست', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
       break;
      }
     case 'USERNAME_INVALID':{
      yield $this->messages->sendMessage(['peer' => $update, 'message' => 'متنی که فرستادید نام کابری معتبر نیست', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
      break;
     }
     case 'IS_USER':{
      yield $this->messages->sendMessage(['peer' => $update, 'message' => 'نام کاربری فرستاده شده متعلق به کاربر است لطفا لینک کانال یا گروه مورد نظر خود را وارد کنید', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
      break;
     }
     case 'UNABLE_TO_FIND_INFO':
     case 'CHANNELS_TOO_MUCH':
     case 'UNHANDLED_EXCEPTION_DETECTED':
      {
     yield $this->messages->sendMessage(['peer' => $update, 'message' => 'مشکلی در ارائه خدمات وجود دارد لطفا مجددا تلاش کنید', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
      break;
     }
    }
    return ;
    }
    catch (Exception $ExceptionWhileGettingLinkFullInfo){
     yield $this->messages->sendMessage(['peer' => $update, 'message' => 'مشکلی در ارائه خدمات وجود دارد لطفا مجددا تلاش کنید', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
     echo $ExceptionWhileGettingLinkFullInfo;
     return ;
    }
   
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