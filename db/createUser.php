<?php
  include 'pdo.php';
  /*TODO: authentication bs to make sure we dont get spurious votes */
  $data = json_decode($post, true);

  if(
     isset($data['username']) && // is_numeric($data['id_question']) &&
     isset($data['password']) //&& is_numeric($data['id_ballot'])
  ){
    $sql = "INSERT INTO `flatvote_user` (`id`, `ts_creation`, `txt_name`, `pwd_password`) VALUES (NULL, current_timestamp(), :username, :password)";
    try{
      $query = $conn->prepare($sql);
      $query->bindParam(":username", $data["username"]);
      $query->bindParam(":password", $data["password"]);
      $query->execute();
      echo json_encode(array("insert"=>"okidoki"));
    } catch(PDOException $e) {
      echo '{"error":"' . $e->getMessage() .'"}';
    }
  } else {
    echo '{"error": "empty values for username or password"}';
  }

?>
