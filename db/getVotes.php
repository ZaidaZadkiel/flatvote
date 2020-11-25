<?php
include 'pdo.php';

if(isset($_GET[id]) && is_numeric($_GET[id])){
  $sql = "SELECT count(*) FROM `flatvote_votes` where `id_question` = {$_GET[id]}";
  $res = $conn->query($sql)->fetchColumn();
  echo'{"count":'.$res.'}';
} else {
  echo'{"error":"id is not set or not numeric"}';
}
?>
