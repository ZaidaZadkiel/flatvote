<?php

global $mysql;

include 'util/pdo.php';

$sql   = "SHOW TABLES;";
$query = $mysql->prepare($sql);

if($query->execute()){
  $tables = $query->fetchAll();
} else {
  $tables = "none";
}

$dir        = '.';
$files1     = scandir($dir);
$arrayfiles = [];

$permission = [
  'public',
  'user',
  'admin'
];

foreach($files1 as $file){
  $info = pathinfo($file);
  if(isset($info['extension']) && $info["extension"] == "php"){ 
    echo $file;
    
    $f = fopen($file, 'r');
    
    for($n=0;$n!=15;$n++){ //TODO: currently reads only the first 15 lines, should read until 'function' text is found
      $line = trim(fgets($f));

      if(strncmp($line, '$', 1)===0) { // variable declaration
        preg_match(
          '/^[\w]+/',      //only words
          substr($line,1), //discard starting '$' character
          $name            //store
        );

        if(in_array($name[0], $permission)){
          echo '<br/>'.$name[0].' '.$line;
        }
      }
    }

    fclose($f);
    echo '<br/>';
    echo '<br/>';
    array_push($arrayfiles, $file); 
  }
}

$host = sprintf(
  "%s://%s:%s%s",
  isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
  $_SERVER['SERVER_NAME'],
  $_SERVER['SERVER_PORT'],
  $_SERVER['REQUEST_URI']
);

$date = date("F jS, Y", strtotime("now"));

$htmltables = count($tables) == 0 ? "No tables found" : count($tables) . " tables";

// return;

echo <<<DOC
<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>$date</title>
    <script type="text/javascript" src="index/index.js?q=2"></script>
    <link rel="stylesheet" href="index/index.css?q=1">
</head>

<body onload="get_host()">
  <div class="w3-col l11 w3-padding-large">
    <h2 id="host">$host</h2>
    $htmltables<br/> 
    <div style="margin-bottom: 1em">
      token: <span id="token"></span>
      <br/>
      <button class="w3-button w3-blue w3-round-large" onclick="update_token('')">clear token</button>
    </div>
DOC;

$dir     = basename(__FILE__, ",php");
$ownname = basename(__FILE__, ",php");

foreach ($arrayfiles as $file){
  if($file == $ownname) continue; //prevent showing index.php since it doesnt have api functions

  //TODO: add ctrl+return to send data automatically
  echo <<<DOC
    <div class="w3-section" >
      <div>url: <a href="$file">$file</a></div>
      <button class="w3-button w3-blue w3-round-large" onClick="show_file('$file')">get info</button>

      <span id="$file-data" class="w3-show-inline-block"></span>
      <table id="$file" style="display:none" class="w3-table w3-border-bottom w3-responsive w3-code"></table>
    </div>
  DOC;
}

echo "
  </div>


</body>
</html>";

// echo json_encode(array("resources" => $arrayfiles, "error" => "resource not found"));
?>
