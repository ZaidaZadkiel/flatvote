<?php
  $params = [
    "create" => [
      "txt_username" => "text",
      "txt_email"    => "email",
      "txt_password" => "password",
    ],
    "login" => [
      "txt_username" => "text",
      "txt_password" => "password",
      "extra" => "optional numeric"
    ],
    "profile" => [
      "status" => "text",
      "avatar" => "optional image"
    ],
    "project" => [
      "id_project" => "numeric",
      "action" => ["subscribe","leave"]
    ]
  ];

  function create($data){

    $sql = "INSERT INTO `flatvote_user` (`id`, `ts_creation`, `txt_name`, `pwd_password`) VALUES (NULL, current_timestamp(), :username, :password)";
    $query = $conn->prepare($sql);
    $query->bindParam(":username", $data["username"]);
    $query->bindParam(":password", $data["password"]);
    $query->execute();

    return $query;
  }

  function login($data){
    return array("hello" => "what");
  }

  function profile($data){
    return array("hello" => "what");
  }

  function project($data){
    return $data; // array("hello" => "what");
  }

  include 'util/input.php';
?>
