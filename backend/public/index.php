<?php
$dir    = '.';
$files1 = scandir($dir);
$arrayfiles = [];

foreach($files1 as $file){
  $info = pathinfo($file);
  if(isset($info['extension']) && $info["extension"] == "php"){ array_push($arrayfiles, $file); }
}

$host = sprintf(
    "%s://%s:%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    $_SERVER['SERVER_PORT'],
    $_SERVER['REQUEST_URI']
  );


echo <<<DOC
<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Today's Date</title>
    <script type="text/javascript" src="index/index.js?q=2"></script>
    <link rel="stylesheet" href="index/index.css?q=1">
</head>

<body>
  <div class="w3-col l11 w3-padding-large">
  <h2>$host</h2>
  <div style="margin-bottom: 1em">
    token: <span id="token"></span>
    <br/>
    <button class="w3-button w3-blue w3-round-large" onclick="update_token('')">clear token</button>
  </div>
DOC;

$ownname = basename(__FILE__, ",php");
foreach ($arrayfiles as $file){
  if($file == $ownname) continue;
  echo <<<DOC
  <div class="w3-section" >
    <div>url: <a href="$host$file">$host$file</a></div>
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
