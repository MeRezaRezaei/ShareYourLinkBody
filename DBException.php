<?php
 
 
 class DBException extends Exception
 {
  protected $Mysql_Error = null;
  public function GetMysqlError(){
   return $this->Mysql_Error;
  }
  public function SetMysqlError($Mysql_Error){
  
  }
  public function __construct($Mysql_Error,$message = "", $code = 0, Throwable $previous = null)
  {
   $this->Mysql_Error = $Mysql_Error;
   parent::__construct($message, $code, $previous);
  }
 }