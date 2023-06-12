<?php
  $params = [
    "listassets" => [
      "id"    => "optional numeric",
      "page"  => "optional numeric",
      "count" => "optional numeric",
      "type"  => [
        "img",
        "video",
        "misc"
      ]
    ],
    "create" => [
      "name"  => "optional text",
      "file"  => "file",
      "type"  => [
        "img",
        "video",
        "misc"
      ]
    ],
    "remove" => [
      "id" => "numeric"
    ]
  ];

  function create($data){
    // if(!isset($data["file"])) return ["error"=>"file not set"];
    // if(!isset($data["name"])) return ["error"=>"name not set"];
    // if(!isset($data["type"])) return ["error"=>"type not set"];

    global $mysql;

    $sql = <<<SQL
      INSERT INTO `rl_project_pictures` (
        `txt_type`,
        `fk_project_id`,
        `txt_name`,
        `txt_key`,
        `url_picture`
      ) VALUES (
        :txt_type,
        :fk_project_id,
        :txt_name,
        :txt_key,
        :txt_url
      );
    SQL;
    // echo $sql;

    // TODO: this is insecure, find a better way
    // random "unique" name for the file upload to contain false/broken uploads
    $UUID = vsprintf( '%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4) );
    // tell client the encoded upload file target name
    $url  = "/api/assets.php?action=create&file=$UUID";
    //hashdata needs to be a string
    $hashdata = is_array($data["file"])  ? json_encode($data["file"])       : $data["file"];
    //filedata needs to be an associative array
    $filedata = !is_array($data["file"]) ? json_decode($data["file"], true) : $data["file"];

    // create secure-er unique hidden data to save in DB
    $hash = password_hash($hashdata, PASSWORD_DEFAULT);
    // save the projcet ID (jan 2023: not implemented)
    $x    = 0;


    $ext = pathinfo($filedata["name"], PATHINFO_EXTENSION);
    $fileurl = "$UUID.$ext";
    if(touch("../_upl/$fileurl") == false){
      return [
        "error" => "Cannot create temporary file"
      ];
    }

    $query = $mysql->prepare($sql);

    $query->bindParam(":txt_type",      $data["type"] );
    $query->bindParam(":fk_project_id", $x );
    $query->bindParam(":txt_name",      $data["name"] );
    $query->bindParam(":txt_key",       $hash );
    $query->bindParam(":txt_url",       $fileurl );

    $query->execute();

    return [
      "query"       => $query->queryString,
      "upload_url"  => $url,
      "http_method" => "put",
      "key"         => $hash,
      "data"        => $data
      // "data"        => $mysql->lastInsertId()
    ];
  }

  function listassets($data, $userdata){
    global $mysql;
    $sql = <<<SQL
      SELECT 
        `id`,
        `txt_type`,
        `url_picture`,
        `fk_project_id`,
        `txt_name`,
        `txt_key`
      from `rl_project_pictures` ;
    SQL;
    $query = $mysql->prepare($sql);

    $query->execute();
    $assets = $query->fetchAll();

    return ["data" => $assets];
  }

  function remove($data, $userdata){
    global $mysql;
    $sql = <<<SQL
      DELETE FROM `rl_project_pictures` WHERE `id`=:id;
    SQL;
    $query = $mysql->prepare($sql);
    $query->bindParam(":id", $data['id'] );
    $query->execute();

    if($query->rowCount() === 0) return ["error" => "Deletion failed", "query" => $sql];
    return ["deleted" => $query->rowCount()];
  }

  /* updload handler*/
  if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // echo "file". $_GET["file"] . "<br/>";
    // echo "HTTP_APIKEY". $_SERVER["HTTP_APIKEY"] . "<br/>";

    include 'util/pdo.php';

    global $mysql;
    $sql = <<<SQL
      SELECT
        `id`,
        `txt_type`,
        `url_picture`,
        `fk_project_id`,
        `txt_name`,
        `txt_key`
      from `rl_project_pictures`
      WHERE
        `txt_key` = :apikey
      ;
    SQL;

    // echo $sql."<br/>";
    // echo $_SERVER["HTTP_APIKEY"]."<br/>";
    // echo $_GET['file']."<br/>";
    $query = $mysql->prepare($sql);
    $query->bindParam(":apikey",   $_SERVER["HTTP_APIKEY"]);
    $query->execute();

    $assets = $query->fetchAll();

    echo json_encode(["data"=>$assets]);
    // echo "write to ../_upl/{$assets[0]['url_picture']}";

    $putdata = fopen("php://input", "r");
    $fp = fopen("../_upl/{$assets[0]['url_picture']}", "w");

    $len = 0;
    while ($data = fread($putdata, 1024*100)){
      $len += fwrite($fp, $data);
    }

    fclose($fp);
    fclose($putdata);

    // echo "wrote $len bytes";

    $dir  = "../assets/files/{$assets[0]['url_picture']}";
    $path = "../_upl/{$assets[0]['url_picture']}";
    if(copy($path, $dir)){
       unlink($path);
       return true;
    } else {
      return false;
    }

    return;
  }



  include 'util/input.php';
?>
