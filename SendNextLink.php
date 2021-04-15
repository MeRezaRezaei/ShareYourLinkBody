<?php
 require_once 'DB.php';
 require_once 'Client.php';
 require_once 'Bot.php';
 
 class SendNextLink
 {
  private static $Instance = null;
  
  protected $Error_Count = 0;
  
  protected $DB = null;
  
  protected $Seconds_To_Add_To_Deactivate_Time = 9 * 60;
  
  protected $Bot = null;
  protected $Client = null;
  
  private function __construct()
  {
   
   $this->Client = Client::GetInstance();
   $this->Bot = Bot::GetInstance();
   $this->DB = DB::GetInstance();
   
  }
 
  public static function GetInstance(){
   return self::$Instance ?? self::$Instance = new SendNextLink();
  }
  
  protected function GetNextLink(){
   $R = $this->DB->GetNextLinkForPost();
   if ($R === false){
   exit();
   }
  return $R;
  }
  
  public function SendNextLink(){
  if ($this->DoesItTimeToSendNextLink()){
   list($Link,$Id) = $this->GetNextLink();
   echo $Id .' '.$Link;
   if ($Link === false){
    echo 'No Link To Send!';
   }
   $Error_Flag  = false;
   
    $Client = Client::GetInstance();
    try {
     $FullInfo = $Client->GetFullInfo($Link);
     $TypeInWords = '';
     $TypeFlag = '';
     if (
     array_key_exists('type',$FullInfo)
     ){
      
      if ($FullInfo['type'] === 'supergroup'){
       $TypeInWords = 'گروه';
       $TypeFlag = 'supergroup';
      }
      elseif ($FullInfo['type'] === 'channel'){
       $TypeInWords = 'کانال';
       $TypeFlag = 'channel';
      }
      else{
       $Error_Flag  = true;
      }
      
     }
     else{
      $Error_Flag  = true;
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
     if(strpos('/joinchat/',$Link) === false){
      $Link_Public_Private = 'private';
     }
     else{
      $Link_Public_Private = 'public';
     }
     if ($Public_Private_Flag === '' || $Link_Public_Private === ''){
      $Error_Flag  = true;
      return;
     }
     $GroupLinkInWords = '';
     // it means the group is public but the requester have sent us private link
     if ($Public_Private_Flag === 'public'){
      if ($Link_Public_Private === 'private'){
       // writing both public and private link
       $GroupLinkInWords = ''
        .'https://t.me/'.$FullInfo['Chat']['username'].PHP_EOL
        .'@'.$FullInfo['Chat']['username'].PHP_EOL
        .$Link.PHP_EOL
       ;
      }
      elseif ($Link_Public_Private === 'public'){
       // both the group and link were public
       $GroupLinkInWords = ''
        .'https://t.me/'.$FullInfo['Chat']['username'].PHP_EOL
        .'@'.$FullInfo['Chat']['username'].PHP_EOL
       ;
      }
     }
     elseif ($Public_Private_Flag === 'private'){
      if ($Link_Public_Private === 'private'){
       // we have only the private manner
       $GroupLinkInWords = ''
        .$Link.PHP_EOL
        .$Link.PHP_EOL
       ;
      }
      elseif ($Link_Public_Private === 'public'){
       // some thing went wrong from the detector
       $GroupLinkInWords = ''
        .$Link.PHP_EOL
        .$Link.PHP_EOL
       ;
      }
     }
     $Default_Banned_Rights_InWords = '';
     $DoesHaveBannedRights = null;
     if ($TypeFlag === 'channel'){$DoesHaveBannedRights = false;}
     if ($TypeFlag === 'supergroup'){
      $DoesHaveBannedRights = true;
      $EveryThingIsFree = true;
      if ($FullInfo['Chat']['default_banned_rights']['view_messages']){
       $Default_Banned_Rights_InWords .= ' '.'دیدن پیام ها'.' ';
       $EveryThingIsFree = false;
      }
      if ($FullInfo['Chat']['default_banned_rights']['send_messages']){
       $Default_Banned_Rights_InWords .= ' '.'ارسال پیام'.' ';
       $EveryThingIsFree = false;
      }
      if ($FullInfo['Chat']['default_banned_rights']['send_media']){
       $Default_Banned_Rights_InWords .= ' '.'ارسال فایل'.' ';
       $EveryThingIsFree = false;
      }
      if ($FullInfo['Chat']['default_banned_rights']['send_stickers']){
       $Default_Banned_Rights_InWords .= ' '.'ارسال استیکر'.' ';
       $EveryThingIsFree = false;
      }
      if ($FullInfo['Chat']['default_banned_rights']['send_gifs']){
       $Default_Banned_Rights_InWords .= ' '.'ارسال گیف'.' ';
       $EveryThingIsFree = false;
      }
      if ($FullInfo['Chat']['default_banned_rights']['send_games']){
       $Default_Banned_Rights_InWords .= ' '.'ارسال بازی'.' ';
       $EveryThingIsFree = false;
      }
      if ($FullInfo['Chat']['default_banned_rights']['send_inline']){
       $Default_Banned_Rights_InWords .= ' '.'ارسال دکمه شیشه ای'.' ';
       $EveryThingIsFree = false;
      }
      if ($FullInfo['Chat']['default_banned_rights']['embed_links']){
       $Default_Banned_Rights_InWords .= ' '.'ارسال لینک'.' ';
       $EveryThingIsFree = false;
      }
      if ($FullInfo['Chat']['default_banned_rights']['send_polls']){
       $Default_Banned_Rights_InWords .= ' '.'ارسال رای گیری'.' ';
       $EveryThingIsFree = false;
      }
      if ($FullInfo['Chat']['default_banned_rights']['change_info']){
       $Default_Banned_Rights_InWords .= ' '.'تغییر اطلاعات'.' ';
       $EveryThingIsFree = false;
      }
      if ($FullInfo['Chat']['default_banned_rights']['invite_users']){
       $Default_Banned_Rights_InWords .= ' '.'دعوت افراد'.' ';
       $EveryThingIsFree = false;
      }
      if ($FullInfo['Chat']['default_banned_rights']['pin_messages']){
       $Default_Banned_Rights_InWords .= ' '.'پین کردن پیام'.' ';
       $EveryThingIsFree = false;
      }
      if ($EveryThingIsFree){
       $Default_Banned_Rights_InWords .= 'همه چیز ازاد است';
      }
   
     }
     $Description = (array_key_exists('about',$FullInfo['full']) && $FullInfo['full']['about'] != '') ? $FullInfo['full']['about'] : 'ندارد';
     $BannedRights = $DoesHaveBannedRights ? 'ممنوعیت ها'.':'.PHP_EOL.$Default_Banned_Rights_InWords : '';
     $LinkInfoInWords = ''
      .'عنوان'.':'.PHP_EOL.$FullInfo['Chat']['title'].PHP_EOL
      .'نوع'.':'.PHP_EOL.$TypeInWords.PHP_EOL
      .'وضعیت'.':'.PHP_EOL.$Public_Private_InWords.PHP_EOL
      .'تعداد کاربران واقعی'.':'.PHP_EOL.$FullInfo['full']['participants_count'].PHP_EOL
      .'لینک'.':'.PHP_EOL.$GroupLinkInWords
      .$BannedRights.PHP_EOL
      .'توضیحات'.':'.PHP_EOL.$Description.PHP_EOL
     ;
     
     $Bot = Bot::GetInstance();
     try {
      $ChannelUpdate =  $Bot->MLP->messages->sendMessage(['peer' => -1001326282114, 'message' => $LinkInfoInWords, 'reply_to_msg_id' =>  null, 'parse_mode' => 'HTML','no_webpage'=>true]);
      $Bot->MLP->messages->forwardMessages(['silent' => false, 'background' => false, 'with_my_score' => null, 'from_peer' => $ChannelUpdate, 'id' => [$ChannelUpdate['updates'][0]['id']], 'to_peer' => 'chat#1380225275', 'schedule_date' => null, ]);
     }catch (Exception $e){
      echo $e;
      if ($e->getMessage() === 'var_export does not handle circular references'){
       echo 'This problem have a long story!';
      }
     }
     //yield $this->messages->sendMessage(['peer' => $Update, 'message' => $LinkInfoInWords, 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML','no_webpage'=>true]);
     //yield $this->messages->forwardMessages(['silent' => false, 'background' => false, 'with_my_score' => null, 'from_peer' => $ChannelUpdate, 'id' => [$ChannelUpdate['updates'][0]['id']], 'to_peer' => $update['message']['from_id']['user_id'], 'schedule_date' => null, ]);
    }
    
    catch (RuntimeException $RuntimeExceptionWhileGettingLinkFullInfo){
     switch ($RuntimeExceptionWhileGettingLinkFullInfo->getMessage()){
      case 'IS_NOT_LINK':{
       $Error_Flag = true;
       //yield $this->messages->sendMessage(['peer' => $update, 'message' => 'متنی که فرستادید لینک نیست', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
       break;
      }
      case 'USERNAME_INVALID':{
       $Error_Flag = true;
       //yield $this->messages->sendMessage(['peer' => $update, 'message' => 'متنی که فرستادید نام کابری معتبر نیست', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
       break;
      }
      case 'IS_USER':{
       $Error_Flag = true;
       //yield $this->messages->sendMessage(['peer' => $update, 'message' => 'نام کاربری فرستاده شده متعلق به کاربر است لطفا لینک کانال یا گروه مورد نظر خود را وارد کنید', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
       break;
      }
      case 'UNABLE_TO_FIND_INFO':
       {
        $Error_Flag = true;
        //yield $this->messages->sendMessage(['peer' => $update, 'message' => 'قادر به دریافت اطلاعات نیستیم', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
        break;
       }
      case 'CHANNELS_TOO_MUCH':{
       $Error_Flag = true;
       //yield $this->messages->sendMessage(['peer' => $update, 'message' => 'تعداد نهایت کانال های کلاینت به پایان رسیده', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
       break;
      }
      case 'UNHANDLED_EXCEPTION_DETECTED':
       {
        $Error_Flag = true;
        //yield $this->messages->sendMessage(['peer' => $update, 'message' => 'مشکلی در ارائه خدمات وجود دارد لطفا مجددا تلاش کنید', 'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
        break;
       }
     }
     
    }
   $this->Delete_Link($Id);
   // in which conditions we need to delete link
   if ($Error_Flag){
    $this->Error_Count++;
    if ($this->Error_Count === 2){
    $this->Stop();
    }
    $this->SendNextLink();
    echo 'Error During Finding Information';
   }
   $this->Stop();
  }
  else{
   echo 'Its Not Time!';
  }
  }
  
  protected function Stop(){
  $this->Increase_Deactivate_Time();
  exit();
  }
  
  
  protected function Increase_Deactivate_Time(){
   
   $this->DB->Plus_N_Deactivate_Time($this->Seconds_To_Add_To_Deactivate_Time + time());
   
  }
   protected function Delete_Link($Id){
   return $this->DB->Delete_Link($Id);
   }
  
  public function DoesItTimeToSendNextLink(): bool{
   $Link_SafeMode_Deactivate = $this->DB->Link_SafeMode_Deactivate();
   echo '$Link_SafeMode_Deactivate:'.$Link_SafeMode_Deactivate;
  if ($Link_SafeMode_Deactivate === false){
   return false;
  }
  $time = time();
   return $Link_SafeMode_Deactivate < $time;
  }
  
  
 }
 
 try {
  $SendNextLink = SendNextLink::GetInstance();
  $SendNextLink->SendNextLink();
 }catch (DBException $DBException){
  echo $DBException->GetMysqlError();
 }
 catch (Exception $e){
  echo $e;
 }
 
 
 
 