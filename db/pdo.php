<?php

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
    // you want to allow, and if so:
    // header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        // may also be using PUT, PATCH, HEAD etc
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}
header("Access-Control-Expose-Headers: Date, timestamp");
header("Content-type:application/json");
header("timestamp:".strftime('%Y-%m-%d %H:%M:%S',time()));

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
   return 0;
}

$post = file_get_contents('php://input');

$servername = "localhost";
$username = "id14619952_zaida";
$password = "lNbA(\xght06HR|[";
$database = "id14619952_zaidazadkiel";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  // echo "Connected successfully";
} catch(PDOException $e) {
  echo '{"error":"' . $e->getMessage() .'"}';
}
?>
