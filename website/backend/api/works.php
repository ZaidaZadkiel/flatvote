<?php

  $params = [
    "create" => [
      "id_owner"              => "optional numeric",
      "id_picture_header"     => "optional numeric",
      "id_video_thumb"        => "optional numeric",
      "txt_title"             => "optional text",
      "txt_subtitle"          => "optional text",
      "txt_description"       => "optional text",
      "txt_blurb"             => "optional text",
      "txt_slug"              => "optional text",
      "txt_video_title"       => "optional text",
      "txt_video_description" => "optional text",
      "url_video"             => "optional text",
      "url_action"            => "optional text",
      "arr_credits"           => "optional object",
      "arr_gallery"           => "optional object",
      "arr_collabs"           => "optional object",
      "bool_published"        => ["published", "draft"],
    ],

    "show" => [
      "id_owner" => "optional numeric",
      "id_work"  => "optional numeric",
      "filter"   => ["main", "single", "published", "all"]
    ],

    "updatesort" => [
      "id_work"  => "numeric",
      "position" => "numeric"
    ],

    "update" => [
      "id_work"               => "numeric",
      "id_owner"              => "optional numeric",
      "id_picture_header"     => "optional numeric",
      "id_video_thumb"        => "optional numeric",
      "txt_title"             => "optional text",
      "txt_subtitle"          => "optional text",
      "txt_description"       => "optional text",
      "txt_blurb"             => "optional text",
      "txt_slug"              => "optional text",
      "txt_video_title"       => "optional text",
      "txt_video_description" => "optional text",
      "url_video"             => "optional text",
      "url_action"            => "optional text",
      "arr_credits"           => "optional object",
      "arr_gallery"           => "optional object",
      "arr_collabs"           => "optional object",
      "bool_published"        => ["published", "draft"],

    ]
  ];


  function updatesort($data, $userdata){
    global $mysql;

    $sql = <<<SQL
      SELECT
        x.`position`,
        rl_projects.`id` as fk_work ,
        x.`fk_changed_by`
      FROM `rl_mainpage` x
      right join `rl_projects` on (x.`fk_work` = `rl_projects`.`id`)
      ORDER BY x.`position`  ASC
    SQL;

    $res = $mysql->query($sql, PDO::FETCH_ASSOC)->fetchAll();
    $querycount = count($res);

    if($data["position"] < 0)           $data["position"]=0;           //prevent negative numbers ?
    if($data["position"] > $querycount) $data["position"]=$querycount; //prevent counting above last item in sequence ?

    $pos = 0;
    // find local index for fk_work
    for($pos; $pos!=$querycount; ++$pos){
      if($res[$pos]["fk_work"] == $data["id_work"]) break;
    }

    // echo "pos $pos {$data["id_work"]}";
    // echo json_encode($res[$pos]);
    // cut the index we have
    $t = array_splice($res, $pos, 1);
    array_splice($res, $data["position"]-1, 0, $t);

    $im = [];
    $k = 1;
    foreach($res as $index=>$object){
      $object["position"] = $k++;
      $im[ $index ]       = $object;
    }

    $data = [];
    foreach($im as $index=>$object){
      $data []= "( {$object["position"]}, {$object["fk_work"]}, {$userdata["id"]} )";
    }

    $strdata = implode(", ", $data);

    $sql = <<<SQL
      DELETE FROM rl_mainpage;
      INSERT INTO rl_mainpage (
        `position`,
        fk_work,
        fk_changed_by
      ) VALUES $strdata;
    SQL;
    $res = $mysql->exec($sql);

    // return ["query" => $sql, "data" => $data, "im" => $im ];

    return show(["filter"=>"main"], $userdata);
  } // function updatesort($data, $userdata)



  function update($data, $userdata){
    global $mysql;

    if( isset($data['arr_collabs']) ){
      $query = $mysql->prepare("delete from `rl_project_people` where `id_work`=:id_work");
      $query->bindParam('id_work', $data['id_work']);
      $query->execute();

      $query_parts = array();
      $d = $data['arr_collabs'];
      for($x=0; $x<count($d); $x++){
          $query_parts[] = "('{$data['id_work']}', '{$d[$x]}')";
      }

      $mysql->query("INSERT INTO rainbowlobster_cms_dev.rl_project_people (id_work,id_person) VALUES ".implode(',', $query_parts) );
    }

    if( isset($data['arr_gallery']) ){
      $query = $mysql->prepare("delete from `rl_project_gallery` where `id_work`=:id_work");
      $query->bindParam('id_work', $data['id_work']);
      $query->execute();

      $query_parts = array();
      $d = $data['arr_gallery'];
      for($x=0; $x<count($d); $x++){
          $query_parts[] = "('{$data['id_work']}', '{$d[$x]}')";
      }

      $mysql->query("INSERT INTO rainbowlobster_cms_dev.rl_project_gallery (id_work,id_picture) VALUES ".implode(',', $query_parts) );
    }

    $vars = [
      'id_owner'              => 'id_owner',
      'txt_title'             => 'txt_title',
      'txt_subtitle'          => 'txt_subtitle',
      'txt_description'       => 'txt_description',
      'txt_blurb'             => 'txt_blurb',
      'txt_slug'              => 'txt_slug',
      'txt_video_title'       => 'txt_video_title',
      'txt_video_description' => 'txt_video_description',
      'url_video'             => 'url_video',
      'url_action'            => 'url_action',
      'id_picture_header'     => 'fk_picture_header',
      'fk_project_credits'    => 'fk_project_credits',
      'id_video_thumb'        => 'fk_video_thumb',
    ];

    $v = [];
    foreach($data as $key=>$val){
      if($key == 'id_work') continue; //required key, we dont need to variably add it
      if($vars[$key]) $v[]= "`{$vars[$key]}` = :{$key}";
    }

    if(isset($data["bool_published"])){
      $val = strtolower($data["bool_published"])=="published" ? '1' : '0';
      $v []= "`bool_published` = {$val}";
    }

    $str = implode(",\n  ", $v); //ugly


    $sql = <<<SQL
      UPDATE `rl_projects`
      SET
      {$str},
      `ts_last_update` = current_timestamp()
      WHERE
        `id` = :id_work
      ;
    SQL;

    // echo $sql;

    $query = $mysql->prepare($sql);
    $query->bindParam(":id_work", $data["id_work"] );

    $r = "";
    foreach($data as $key=>$val){
      if($key === "id_work") continue;
      if($vars[$key]){
        $r .= "(:$key, $val) ";
        $query->bindParam(":$key", $data[$key]);
      }
    }

    $query->execute();
    $count = $query->rowCount();

    $data = show(
      [
        "id_work" => $data["id_work"],
        "filter"  => "single"
      ],
      $userdata
    )['data'][0];

    return [
      "query" => $sql,
      "count" => $count,
      "data"  => $data,
      // "r"     => $r
    ];
  }


  function create($data, $userdata){
    global $mysql;

    include("slugify.php");
    $txt_slug = slugify($data["txt_title"]);

    $sql = <<<SQL
      INSERT INTO `rl_projects`
        (
          `txt_title`,
          `txt_subtitle`,
          `txt_description`,
          `txt_blurb`,
          `fk_pictures`,
          `url_video`,
          `fk_picture_header`,
          `fk_project_credits`,
          `id_owner`,
          `bool_published`,
          `url_action`,
          `txt_slug`,
          `ts_creation`,
          `ts_last_update`
        )
      VALUES (
        :txt_title,
        :txt_subtitle,
        :txt_description,
        :txt_blurb,
        :fk_pictures,
        :url_video,
        :fk_picture_header,
        :fk_project_credits,
        :id_owner,
        :bool_published,
        :url_action,
        :txt_slug,
        current_timestamp(),
        current_timestamp()
      );
    SQL;

    $visible = ($data["restriction"] == "draft" ? 0 : 1);

    $query = $mysql->prepare($sql);

    $query->bindParam(":txt_title",            $data["txt_title"]);
    $query->bindParam(":txt_subtitle",         $data["txt_subtitle"]);
    $query->bindParam(":txt_description",      $data["txt_description"]);
    $query->bindParam(":txt_blurb",            $data["txt_blurb"]);
    $query->bindParam(":fk_pictures",          $data["id_pictures"]);
    $query->bindParam(":fk_picture_header",    $data["id_heroimage"]);
    $query->bindParam(":fk_project_credits",   $data["id_project_credits"]);
    $query->bindParam(":url_video",            $data["url_video"]);
    $query->bindParam(":url_action",           $data["url_action"]);
    $query->bindParam(":id_owner",             $userdata["id"]);
    $query->bindParam(":bool_published",       $visible);
    $query->bindParam(":txt_slug",             $txt_slug);

    $query->execute();

    $createresult = [
      "query" => $query->queryString,
      "id"    => $mysql->lastInsertId()
    ];

    updatesort(
      [
        "id_work" =>$createresult["id"],
        "position" => 999
      ],
      $userdata
    );

    return $createresult;
  }


  function show($data, $userdata){
    global $mysql;

    $where = [];
    $pr    = [];

    if(isset($data["filter"])){
      switch($data["filter"]){

        case "main":
          $where []= "rp.`bool_published`=TRUE";
          break;

        case "single":
          // BUG: ommiting work_id returns all works, should raise error
          if(isset($data["id_owner"])) {
            $where []= "rp.`id_owner`=:id_owner";
            $pr[':id_owner'] = $data["id_owner"];
          }

          if(isset($data["id_work"])) {
            $where []= "rp.`id`=:id_work";
            $pr[':id_work'] = $data["id_work"];
          }
          break;

        case "published":
          $where []= "rp.`bool_published`=TRUE";
          // $pr[':x']='y';
          break;

        case "all":
          $where   = [];
          break; //just fetch everytyhing
      }
    }

    $condition = "";
    if(count($where)) $condition = ' WHERE (' . implode(' AND ', $where) . ')';

    $sql = <<<SQL
    SELECT
      rp.`id`,
      rp.`txt_title`             as `header`,
      rp.`txt_subtitle`          as `subtitle`,
      rp.`txt_description`       as `content`,
      rp.`txt_blurb`             as `blurb`,
      rp.`fk_picture_header`     as `heroimage_id`,
      rp.`url_video`             as `url_video`,
      rp.`txt_video_description` as `txt_video_description`,
      rp.`txt_video_title`       as `txt_video_title`,
      rp.`fk_video_thumb`        as `id_video_thumb`,
      rp.`fk_project_credits`    as `credits`,
      rp.`url_action`            as `actionurl`,
      rp.`txt_slug`              as `txt_slug`,
      rpp.`id`                   as `id_heroimage`,
      rpp.`url_picture`          as `url_heroimage`,
      rpp.`txt_name`             as `txt_heroimage_name`,
      rpp.`txt_type`,
      if(rp.`bool_published`, "published", "draft") as `published`
    FROM
      `rl_projects` as rp
    left join `rl_project_pictures` as rpp
    on ( rp.fk_picture_header = rpp.id )
    $condition
    SQL;

    if($data["filter"] === "main"){
      /* there ought to be a better way */
      $sql = <<<SQL
      SELECT
        rp.`id`,
        rp.`txt_title`          as `header`,
        rp.`txt_subtitle`       as `subtitle`,
        rp.`txt_description`    as `content`,
        rp.`txt_blurb`          as `short`,
        rp.`fk_picture_header`  as `images`,
        rp.`url_video`          as `video`,
        rp.`fk_project_credits` as `credits`,
        rp.`url_action`         as `actionurl`,
        if(rp.`bool_published`, "published", "draft") as `published`,
        rpp.`id`                as `id_heroimage`,
        rpp.`url_picture`       as `url_heroimage`,
        rpp.`txt_name`          as `txt_heroimage_name`,
        rpp.`txt_type`,
        rm.`position`,
        rm.`fk_work`
      FROM
        `rl_projects` as rp
      left join `rl_project_pictures` as rpp 
        on ( rp.fk_picture_header = rpp.id )
      left join `rl_mainpage` as rm 
        on (rm.fk_work = rp.id)
      $condition
      order by rm.`position` asc
      SQL;
    }


    // echo $sql;
    $query = $mysql->prepare($sql);

    if(count($pr)){
      if( !empty($data["id_owner"]) ){
        $query->bindParam(":id_owner", $data["id_owner"]);
      }

      // Looping for all values into array...
      foreach ($pr as $key => &$val) {
          $query->bindParam($key, $val);
      }
    }

    $query->execute();
    $projects = $query->fetchAll();

    if($data['filter'] == 'single'){

      /* there is work, get collab people */
      $query = $mysql->prepare(<<<SQL
        SELECT
          rpp.id_person as id,
          rp.*
        FROM 
          rainbowlobster_cms_dev.rl_project_people rpp  
        LEFT JOIN 
          rainbowlobster_cms_dev.rl_people rp
        ON rp.id = rpp.id_person
        WHERE 
          rpp.id_work=:id_work;
      SQL);
      $query->bindParam(":id_work", $data['id_work']);
      $query->execute();
      $collabs = $query->fetchAll();
      foreach ($collabs as $index => $obj) {
        $collabs[$index]['arr_badges']  = json_decode($obj['arr_badges']);
        $collabs[$index]['arr_socials'] = json_decode($obj['arr_socials']);
      }

      $projects[0]['collabs'] = $collabs;
      
      // /* there is work, get gallery photos */
      $query = $mysql->prepare(<<<SQL
        SELECT 
          id_picture 
        FROM 
          rainbowlobster_cms_dev.rl_project_gallery
        WHERE id_work=:id_work;
      SQL);
      $query->bindParam(":id_work", $data['id_work']);
      $query->execute();
      $gallery = $query->fetchAll(PDO::FETCH_COLUMN);
      $projects[0]['gallery'] = $gallery;

    }

    $unpub = $mysql->query("select count(*) as count from `rl_projects` where bool_published = 0;")->fetchAll(PDO::FETCH_ASSOC)[0]["count"];

    return [
      "query"       => $query->queryString,
      "data"        => $projects,
      "unpublished" => $unpub
    ];
  }


  include 'util/input.php';
?>
