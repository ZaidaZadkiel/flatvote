<?php
	$public = [
		"popular"
	];
	
  $params = [
    "create" => [
      "txt_title"     => "text",
      "id_owner"     => "text",
    ],

    "show" => [
      "id"      => "optional numeric",
      "filter"   => ["single", "all"]
    ],

    "update" => [
      "id"           => "numeric",
    ],

    "popular" => [
    ]

  ];

  function popular($data){
  	return 'hi';
  }

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

  include 'util/input.php';

?>