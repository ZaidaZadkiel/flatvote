<?php
  $params = [
    "create" => [
      "name"        => "text",
      "description" => "optional text",
      "imageurl"    => "optional text",
      "restriction" => ["public", "private"]
    ],
    "subscription" => [
      "project"  => "numeric",
      "key"      => "optional text",
      "option"   => ["join", "leave"]
    ],
    "show" => [
      "user" => "optional text",
      "tags"  => "optional text"
    ]
  ];

  function create($data, $userdata){
    global $mysql;

    $sql =  "INSERT INTO `flatvote_projects` (`txt_name`, `txt_description`, `img_cover`, `id_owner`, `visible`, `ts_creation`, `ts_last_update`)".
            " VALUES ".
            "(:name, :description, :imageurl, :owner, :visible, current_timestamp(), current_timestamp())";
    $query = $mysql->prepare($sql);
    $visible = ($data["restriction"] == "private" ? 0 : 1);
    $query->bindParam(":name",        $data["name"]);
    $query->bindParam(":description", $data["description"]);
    $query->bindParam(":imageurl",    $data["imageurl"]);
    $query->bindParam(":owner",       $userdata["id"]);
    $query->bindParam(":visible",     $visible);
    $query->execute();

    return [
      "query" => $query->queryString,
      "id"    => $mysql->lastInsertId()
    ];
  }


  function show($data, $userdata){
    global $mysql;
    $sql = "SELECT `id`, `txt_name`, `txt_description`, `img_cover`, `id_owner`, `visible`, `ts_creation`, `ts_last_update` FROM `flatvote_projects` WHERE `id_owner`=:id_owner";
    $owner = (!empty($data["user"]) ? $data["user"] : $userdata["id"]);

    $query = $mysql->prepare($sql);
    $query->bindParam(":id_owner", $owner);
    $query->execute();
    $projects = $query->fetchAll();

    if(!isset($projects[0])){
      return [
        "query"   => $query->queryString,
        "error"   => "project for user '" . $owner . "' not found"
      ];
    }

    return [
      "query" => $query->queryString,
      "data"  => $projects
    ];
  }

  // "subscription" => [
  //   "project"  => "numeric",
  //   "key"      => "optional text",
  //   "option"   => ["join", "leave"]
  // ],
  function subscription($data, $userdata){
    global $mysql;
    //TODO: add key validation
    if($data["option"]  == "join") {
      $sql = "INSERT INTO `flatvote_project_users` (`id_project`, `id_user`, `ts_subscription`, `txt_user_name`) VALUES (:project, :user, current_timestamp(), :username)";
    }
    if($data["option"]  == "leave") {
      $sql = "DELETE FROM `flatvote_project_users` WHERE `id_project`=:project and `id_user`=:user and `txt_user_name`=:username";
    }

    error_log("username ".$datadata["user"], 0);
    $query = $mysql->prepare($sql);
    $query->bindParam(":project",  $data["project"]);
    $query->bindParam(":user",     $userdata["id"]);
    $query->bindParam(":username", $userdata["user"]);
    $res = $query->execute();

    // $sql = "SELECT `txt_name`, `txt_status` FROM `flatvote_user` WHERE `id`=:id and `txt_name`=:username";
    // $query = $mysql->prepare($sql);
    // $query->bindParam(":username", $userdata["user"]);
    // $query->bindParam(":id",       $userdata["id"]);
    // $query->execute();
    // $user = $query->fetchAll();

    return array("success" => $res);
  }

  include 'util/input.php';
?>
