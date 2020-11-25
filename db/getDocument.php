<?php
include 'pdo.php';

if(isset($_GET['id']) && is_numeric($_GET['id'])){
  $sql = "SELECT * FROM `flatvote_document` JOIN `flatvote_doc_entries` on flatvote_document.id=flatvote_doc_entries.id_document where flatvote_document.id={$_GET['id']}";
  $res = $conn->query($sql)->fetchAll();
  echo json_encode($res);
} else {
  echo'{"error":"id is not set or not numeric"}';
}
?>
