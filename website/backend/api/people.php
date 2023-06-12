<?php
  $params = [
    "create" => [
      "txt_name"     => "text",
      "txt_blurb"    => "text",
      "url_photo"    => "text",
      "arr_badges"   => "object",
      "arr_socials"  => "object",
      "arr_projects" => "optional object",
    ],

    "show" => [
      "id"      => "optional numeric",
      // "name"    => "optional text",
      // "role"    => "optional text",
      // "project" => "optional numeric",
      "filter"   => ["single", "all"]
    ],

    "update" => [
      "id"           => "numeric",
      "txt_name"     => "optional text",
      "txt_blurb"    => "optional text",
      "url_photo"    => "optional text",
      "arr_badges"   => "optional object",
      "arr_projects" => "optional object",
      "arr_socials"  => "optional object",
    
    ]

  ];

  function create($data, $profiledata){
    global $mysql;

    $sql = <<<SQL
      INSERT INTO 
        rainbowlobster_cms_dev.rl_people (
          txt_name,
          txt_blurb,
          url_photo,
          arr_badges,
          arr_socials,
          fk_projects
        ) VALUES (
          :txt_name,
          :txt_blurb,
          :url_photo,
          :arr_badges,
          :arr_socials,
          "1"
        );
    SQL;

    $query = $mysql->prepare($sql);
    $query->bindParam(":txt_name",    $data["txt_name"]);
    $query->bindParam(":txt_blurb",    $data["txt_blurb"]);
    $query->bindParam(":url_photo",   $data["url_photo"]);
    $query->bindValue(":arr_badges",  json_encode($data["arr_badges"]) );
    $query->bindValue(":arr_socials", json_encode($data["arr_socials"]) );
    // $query->bindParam(":fk_projects", $data["fk_projects"]);
    $query->execute();

    $createresult = [
      "query" => $sql,
      "id"    => $mysql->lastInsertId()
    ];

    return $createresult;
  }



  function show($data, $userdata){
    global $mysql;

    if($data["filter"] == "single" && !$data["id"]) return ["error" => "filtering for single but id not set"];
    $where = (
      $data["filter"] == "single" 
        ? " WHERE rp.id=:id"
        : ""
    );

    $sql = <<<SQL
      SELECT 
        rp.id,
        rp.txt_name,
        rp.txt_blurb,
        rp.arr_badges,
        rp.fk_projects,
        rp.arr_socials,
        rpp.id as photo_id,
        rpp.url_picture as url_photo
      FROM 
        rainbowlobster_cms_dev.rl_people rp
      LEFT JOIN 
        rainbowlobster_cms_dev.rl_project_pictures rpp
      ON
        rpp.id = rp.url_photo
      {$where}
    SQL;


    $query = $mysql->prepare($sql);
    $query->bindParam(":id", $data["id"]);
    $query->execute();
    $res = $query->fetchAll();

    foreach($res as $key=>$value){
      if( $value["arr_badges"][0] != '[' ){
        $res[$key]["arr_badges"]  = [$value["arr_badges"]]; // single value in array
      } else {
        $res[$key]["arr_badges"]  = json_decode($value["arr_badges"]); // string to array
      }

      if( $value["arr_socials"][0] != '[' ){
        $res[$key]["arr_socials"] = [$value["arr_socials"]];
      } else {
        $res[$key]["arr_socials"] = json_decode($value["arr_socials"]);
      }
    }


    return [
      "data"=>$res
    ];
  }



  function update($data, $profiledata){
    global $mysql;

    $sql = <<<SQL

      UPDATE 
        rainbowlobster_cms_dev.rl_people
      SET 
        url_photo=:url_photo,
        arr_badges=:arr_badges,
        txt_name=:txt_name,
        txt_blurb=:txt_blurb,
        fk_projects=:fk_projects,
        arr_socials=:arr_socials
      WHERE 
        id=:id;
    SQL;

    $query = $mysql->prepare($sql);

    $query->bindParam(":id",          $data["id"]);
    $query->bindParam(":url_photo",   $data["url_photo"]);
    $query->bindParam(":txt_name",    $data["txt_name"]);
    $query->bindParam(":txt_blurb",   $data["txt_blurb"]);
    $query->bindValue(":fk_projects", "1");// $data["fk_projects"]);
    $query->bindValue(":arr_socials", json_encode($data["arr_socials"]) );
    $query->bindValue(":arr_badges",  json_encode($data["arr_badges"]) );

    $query->execute();

    $updatecount = $query->rowCount();

    $res = show($data, $userdata);
    $res["updated"] = $updatecount;
    return $res;
  }


  include 'util/input.php';
?>

