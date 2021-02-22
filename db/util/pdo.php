<?php
$servername = "localhost";
$username = "id14619952_zaida";
if($_SERVER['HTTP_HOST'] == "zaidazadkiel.com"){
  $password = "lNbA(\xght06HR|[";
} else {
  $password = "pass";
}
$database = "id14619952_zaidazadkiel";

global $mysql;
try{
  global $mysql;
  $mysql = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
  // set the PDO error mode to exception
  $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $mysql->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

  // error_log("mysql".json_encode($mysql!=false), 0);

} catch(PDOException $e){
    error_log($e->getMessage(), 0);
}
// TODO: handle errors in starting pdo
?>
