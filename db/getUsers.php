<?php
include 'pdo.php';

  $sql = "SELECT * FROM `flatvote_user` WHERE 1";
  $res = $conn->query($sql)->fetchAll();
  echo json_encode($res);
?>
