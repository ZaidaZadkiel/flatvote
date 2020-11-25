<?php
  include 'pdo.php';
  /*TODO: authentication bs to make sure we dont get spurious votes */
  $data = json_decode($post, true);

  if(isset($data['id_question']) && is_numeric($data['id_question']) &&
     isset($data['id_user'])     && is_numeric($data['id_user']) &&
     isset($data['id_ballot'])   && is_numeric($data['id_ballot'])
  ){
    $comment = isset($data['txt_comment']) ? $data['txt_comment'] : "NULL";
    $sql = "INSERT INTO `flatvote_votes` (`id`, `timestamp`, `id_user`, `id_question`, `id_ballot`, `txt_comment`)
            VALUES (NULL, current_timestamp(), :id_user, :id_question, :id_ballot, :txt_comment)";
            // VALUES (NULL, current_timestamp(), {$data['id_user']}, {$data['id_question']}, {$data['id_ballot']}, {$comment});";
    try{
      $query = $conn->prepare($sql);
      $query->bindParam(":id_user",     $data["id_user"]);
      $query->bindParam(":id_question",     $data["id_question"]);
      $query->bindParam(":id_ballot",   $data["id_ballot"]);
      $query->bindParam(":txt_comment", $data["txt_comment"]);
      $query->execute();


      $sql = "SELECT count(*) FROM `flatvote_votes` where `id_question` = {$data["id_question"]}";
      $res = $conn->query($sql)->fetchColumn();
      echo'{"count":'.$res.'}';
    } catch(PDOException $e) {
      echo '{"error":"' . $e->getMessage() .'"}';
    }
  } else {
    echo '{"error": "request not numeric data or missing id_question, id_user or id_ballot"}';
  }

?>
