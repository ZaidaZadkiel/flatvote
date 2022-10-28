<?php
  $params = [
    "signup" => [
      "username" => "text",
      "email"    => "email",
      "password" => "password",
    ],
    "login" => [
      "username" => "text",
      "password" => "password",
      "extra"    => "optional numeric"
    ],
    "profile" => [
      "status" => "text",
      "avatar" => "optional image"
    ],
    "find" => [
      "object" => ["projects","documents", "votes"],
      "owner"  => "optional numeric",
      "id"     => "optional numeric",
      "tags"   => "optional text",

    ]
  ];

  function signup($data){
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
          "profile" => [
            "username"  => $user[0]["txt_name"],
            "lastLogin" => $user[0]["ts_last_login"],
            "id"        => $user[0]["id"]
          ],
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

  // "find" => [
  //   "object" => ["projects","documents", "votes"],
  //   "user"   => "optional numeric",
  //   "id"     => "optional text",
  //   "tags"   => "optional text",
  //
  // ]
  function find($data, $userdata){
    $object = "all";
    switch($data["object"]){
      case "projects":   return ["data" => object_get_projects($data, $userdata) ];
      case "documents":  return ["data" => "doc"];
      case "votes":      return ["data" => "vot"];
      default:           return ["error" => "object is not one of ".  $params["find"]["object"] ]; //this never happens
    }
  }

  function object_get_projects($data, $userdata){
    global $mysql;
    $project = (empty($data["id"])    ? false           : $data["id"] );
    $owner   = (empty($data["owner"]) ? $userdata["id"] : $data["owner"] );
    $sql = "SELECT * FROM `flatvote_projects` where `id_owner`=:owner" . ($project==false ? "" : " and `id`=:project" );

    $query = $mysql->prepare($sql);
    $query->bindParam(":owner",    $owner);
    if($project != false) $query->bindParam(":project", $project);
    $query->execute();
    $user = $query->fetchAll();

    return $user;
  }

  include 'util/input.php';
?>
