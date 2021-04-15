<?php
 require_once 'DBException.php';
 
 class DB
 {
  const DB_ADDRESS = 'localhost';
  const DB_USER = 'root';
  const DB_PASSWORD = '19499825';
  const DB_DATABASE = 'ShareYourLinkBody';
  
  protected static $Connection = null;
  
  protected static $Instance = null;
  
  private function __construct()
  {
   $this->CreateMySQL();
   $this->init_Connection();
  }
  public static function GetInstance(): \DB{
   return self::$Instance ?? self::$Instance = new  DB();
  }
  protected function CreateMySQL(){
   self::$Connection = new mysqli();
  }
  protected function init_Connection(){
   if (self::$Connection === null){
    $this->CreateMySQL();
   }
   self::$Connection->connect(
    self::DB_ADDRESS
    ,self::DB_USER
    ,self::DB_PASSWORD
    ,self::DB_DATABASE
   );
    if (self::$Connection->connect_error || !self::$Connection->ping()){
     throw new DBException(self::$Connection->connect_error,'MYSQL_CONNECTION_ERROR');
    }
    return true;
  }
  
  public function Q($Q){
   
    $R = self::$Connection->query($Q);
    if ($R !== false){
     return $R;
    }
   
   throw new DBException('Error: '.self::$Connection->error.PHP_EOL.'Query: '.$Q,'MYSQL_QUERY_ERROR');
  }
  
  public function GetNextLinkForPost(){
   $SEM = sem_get(101112,1);
   if (sem_acquire($SEM)){
    $Q  = 'SELECT `Id`,`Link` FROM `Link_For_Post` WHERE `Pick_Time` IS NULL ORDER BY `Id` LIMIT 1 ;';
    $R = $this->Select($Q);
    if (mysqli_num_rows($R) === 1){
     $R = mysqli_fetch_assoc($R);
     $Link = $R['Link'];
     $Id = $R['Id'];
     $time = time();
     $this->Update('UPDATE `Link_For_Post` SET `Pick_Time` = \''.$time.'\' WHERE `Link_For_Post`.`Id` = '.$Id.' LIMIT 1  ;');
     sem_release($SEM);
     return [$Link,$Id];
    }
    return false;
   }
   throw new Exception('SEM_ERROR');
  }
  protected function Update($Q){
   $R = $this->Q($Q);
   $this->PrintResult($Q,$R);
   return $R;
  }
  public function Select($Q){
   $R = $this->Q($Q);
   if ($R !== false){
    $this->PrintResult($Q,true);
   }
   else{
    $this->PrintResult($Q,false);
   }
   return $R;
  }
  public function OmitLinkFromQueue($Id){
  $Q = 'DELETE FROM `Link_For_Post` WHERE `Id` = '.$Id.' LIMIT 1 ';
   return $this->Update($Q);
  }
  protected function PrintResult($Q,$R){
  $RInWords = $R ? 'true':'false';
  echo PHP_EOL.$Q.PHP_EOL.$RInWords.PHP_EOL;
  }
  
  
  public function QueueNewLink($Link){
   $Q = 'INSERT INTO `Link_For_Post` (`Id`, `Link`, `Pick_Time`) VALUES (NULL, \''.$Link.'\', NULL)';
   return $this->Update($Q);
  }
  
  public function Link_SafeMode_Deactivate(){
  $Q = 'SELECT `Link_SafeMode_Deactivate` FROM `Send_Link_Timing` WHERE `Id` = 1 AND Is_In_Use = 0 ; ';
  $R = $this->Select($Q);
  if (mysqli_num_rows($R) === 1){
   $R = mysqli_fetch_assoc($R);
   return $R['Link_SafeMode_Deactivate'];
  }
  return false;
  }
  
  public function Plus_N_Deactivate_Time($S){
   $Q =  'UPDATE `Send_Link_Timing` SET `Link_SafeMode_Deactivate` = \''.$S.'\' WHERE `Send_Link_Timing`.`Id` = 1';
   return $this->Update($Q);
  }
  
  
  public function Delete_Link($Id){
   $Q = 'DELETE FROM `Link_For_Post` WHERE `Link_For_Post`.`Id` = '.$Id.'  ';
   return $this->Update($Q);
  }
  
  
  public function Set_Link($Link){
   $Q = 'INSERT INTO `Link_For_Post` (`Id`, `Link`, `Pick_Time`) VALUES (NULL, \''.$Link.'\', NULL)';
   //$Q = addslashes($Q);
   return $this->Update($Q);
  }
  
  
  
  
  
  
 }
 
 
 
 
 try {
  $DB = DB::GetInstance();
  $R = $DB->Select('select  5 * 5 ;');
//  echo var_dump(mysqli_fetch_assoc($R));
  //$DB->Plus_N_Deactivate_Time(999);
 }catch (DBException $DBException){
  echo $DBException;
  echo $DBException->GetMysqlError();
 }
 