<?php
  $params = [
    "cast" => [
      "ballot"   => "numeric",
      "comment"  => "optional text",
      "choice"   => [
        "accept",
        "reject",
        "dispute"
      ]
    ],
    "list" => [
      "project"  => "numeric",
      "document" => "numeric",
      "settled"  => [
        "confirmed",
        "rejected",
        "ongoing",
        "all"
      ]
    ]
  ];

  function create($data){
    global $mysql;
    $hash = password_hash($data["password"], PASSWORD_DEFAULT);
    $sql = "INSERT INTO `flatvote_user` (`ts_creation`, `txt_name`, `pwd_password`) VALUES (current_timestamp(), :username, :password)";
    $query = $mysql->prepare($sql);
    $query->bindParam(":username", $data["username"]);
    $query->bindParam(":password", $hash);
    $query->execute();

    return [
      "query" => $query->queryString,
      "id"    => $mysql->lastInsertId()
    ];
  }


  //TODO: add something for extra parameters or remove if not necessary
  function login($data){
    global $mysql;
    $sql = "SELECT `id`, `ts_creation`, `txt_name`, `pwd_password`, `ts_last_login` FROM `flatvote_user` WHERE `txt_name`=:username";
    $query = $mysql->prepare($sql);
    $query->bindParam(":username", $data["username"]);
    $query->execute();
    $user = $query->fetchAll();

    if(isset($user[0])){
      $match = password_verify($data["password"], $user[0]["pwd_password"]);
      if($match == true){
        $mysql->query("UPDATE `flatvote_user` set `ts_last_login` = now() WHERE `id`=".$user[0]['id']);
        $time = $mysql->query("SELECT `ts_last_login` FROM `flatvote_user` WHERE `txt_name`='".$user[0]["txt_name"]."'");
        $time->execute();
        $gg = $time->fetchAll();

        return [
          "query"   => $query->queryString,
          "profile" => $user[0],
          "token"   => base64_encode(json_encode([
                "id"   => $user[0]["id"],
                "user" => $user[0]["txt_name"],
                "key"  => $gg[0]["ts_last_login"]
          ]))
        ];
      }

      return [
        "query" => $query->queryString,
        "error" => "wrong user or pass"
      ];
    }

    return [
      "query" => $query->queryString,
      "error" => "wrong user or pass"
    ];
  }


  function profile($data, $userdata){
    global $mysql;
    $sql = "UPDATE `flatvote_user` set `txt_status` = :status WHERE `id`=:id and `txt_name`=:username";
    $query = $mysql->prepare($sql);
    $query->bindParam(":username", $userdata["user"]);
    $query->bindParam(":id",       $userdata["id"]);
    $query->bindParam(":status",   $data["status"]);
    $query->execute();

    $sql = "SELECT `txt_name`, `txt_status` FROM `flatvote_user` WHERE `id`=:id and `txt_name`=:username";
    $query = $mysql->prepare($sql);
    $query->bindParam(":username", $userdata["user"]);
    $query->bindParam(":id",       $userdata["id"]);
    $query->execute();
    $user = $query->fetchAll();

    return array("data" => $user[0]);
  }

  include 'util/input.php';
?>
