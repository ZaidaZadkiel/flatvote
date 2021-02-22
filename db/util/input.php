<?php
include 'cors.php';
include 'pdo.php';

if($mysql == null) {
  echo "omfg";
  return;
}

if(empty($params)){
  echo json_encode([
    "error" => "internal server error: params is not found"
  ]);
  exit();
}


function check_authorization(){
  if($_GET['action'] == "login") return true; //we always allow login
  if(!isset($_SERVER['HTTP_AUTHORIZATION'])) return false; //everything else needs auth

  $token = $_SERVER['HTTP_AUTHORIZATION'];

  if (!empty($token)){
    $data = json_decode(base64_decode($token), true);
    global $mysql;


    $sql = "SELECT `ts_last_login` FROM `flatvote_user` WHERE `id`=:id and `txt_name`=:username";
    $query = $mysql->prepare($sql);
    $query->bindParam(":username", $data["user"]);
    $query->bindParam(":id", $data["id"]);
    $query->execute();
    $user = $query->fetchAll();

    if(isset($user[0])){
      if($data["key"] == $user[0]["ts_last_login"]) return $data;
    }
  }
  return false;
}

/* return bool: is value defined as str_type*/
function type_check($value, $str_type){
  if(empty($value)) return false;

  if(is_array($str_type)) return in_array($value, $str_type);

  switch($str_type){
    case "numeric" : return is_numeric($value);
    case "email"   : return filter_var($value, FILTER_VALIDATE_EMAIL);
    case "text"    : return is_string ($value);
    case "password": return is_string ($value); // TODO: password validation
    default        : return false;
  }
}

function input_valid($params, $data){
  // error_log("data".json_encode($data), 0);
  // error_log("params".json_encode($params), 0);
  if(empty($data)) return "post does not contain data";

  // if( count(array_diff_key($data, $params)) == 0 ){
  $validated = array();
  $invalid = array();
  foreach($params as $name => $type ) {
    $optional = is_string($type) && (strpos($type, "optional") === 0);
    $str_type = ($optional ? substr($type, 9) : $type);

    if(type_check($data[$name], $str_type ) ) {
      $validated[$name] = $data[$name];
    } else {
      if($optional == true && !empty($data[$name])) $invalid[] = $name." is not ".$str_type;
      if($optional == false) $invalid[] = $name." is not ".$str_type;
    }
  }
  if(!empty($invalid)) return implode(", ", $invalid);

  return $validated;
}


$data = json_decode(file_get_contents('php://input'), true);


if(isset($_GET['action']) && isset($params[$_GET['action']]) ){
  $valid = input_valid($params[$_GET['action']], $data);

  if(!is_array($valid)) {
    echo json_encode([
      "error"  => $valid,
      $_GET['action'] => $params[$_GET['action']]
    ]);
    return;
  }

  try{
    $userdata = check_authorization();

    if($userdata == false) {
      echo json_encode([
        "error" => "authorization invalid, login again",
        "token" => ""
      ]);
      return ;
    }

    $result = $_GET['action']($data, $userdata);
    echo json_encode($result);
  } catch(Exception $e) {
    echo '{"error":"' . $e->getMessage() .'"}';
  }

  return;

} else {
  echo json_encode([
    "error" => "required url param 'action'",
    "action" => $params
  ]);
}

// echo json_encode([
//   "error" =>  "data not set",
//   "actions" => isset($_GET['action']) ? $params[$_GET['action']] : $params,
//   "data" => $data,
//   "method" => $_SERVER['REQUEST_METHOD']
// ]);

exit();

?>
