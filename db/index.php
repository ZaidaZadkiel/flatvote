<?php
$dir    = '.';
$files1 = scandir($dir);
$arrayfiles = [];

foreach($files1 as $file){
  if(pathinfo($file)["extension"] == "php"){ array_push($arrayfiles, $file); }
}

echo json_encode(array("resources" => $arrayfiles, "error" => "resource not found"));
?>
