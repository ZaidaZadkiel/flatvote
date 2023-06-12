<?php

// global $log_fp;
// $log_fp = fopen('../_upl/api.log', 'a');//opens file in append mode  
// //fwrite($log_fp, 
//   'start ['.
//     date(DATE_ATOM, mktime(0, 0, 0, 7, 1, 2000)).
//   "] - " . 
//   (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' 
//       ? "https" 
//       : "http" ) . 
//   "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]\n"
// );  


include 'cors.php';
include 'pdo.php';
include 'auth.php';

if($mysql == null) {
  echo "error in database configuration";
  return;
}

if(empty($params)){
  echo json_encode([
    "error" => "internal server error: params is not found"
  ]);
  exit();
}


function is_json($value) {
  //check if associative array, disallow [0]=>$value but allow ['key']=>$value
  if(is_array($value) && strlen(array_keys($value)[0]!=="0")) return true;

  //not array, not string, wtf is it ?
  if(!is_string($value)) return false;

  json_decode($value);
  return json_last_error() === JSON_ERROR_NONE;
}

/* return bool: is value defined as str_type*/
function type_check($value, $str_type){
  if(is_array($value)==false && strlen($value)===0) return false;

  // $value = 'valid_arg1'; $str_type = "['valid_arg1', 'valid_arg2']";
  if(is_array($str_type)) return in_array($value, $str_type);

  switch($str_type){
    case "file"    : return is_json($value);
    case "object"  : return (is_array($value) || is_json($value));
    case "numeric" : return is_numeric($value);
    case "email"   : return filter_var($value, FILTER_VALIDATE_EMAIL);
    case "text"    : return is_string ($value);
    case "password": return is_string ($value); // TODO: password validation
    default        : return false;
  }
}

function input_valid($params, $data){
  // error_log("data".json_encode($data), 0);
  // echo "params".json_encode($params);

  if(empty($data) && !empty($params)) {
    //fwrite($fp_log, "error: ".json_encode($_GET['action'])." no data\n";
    return "post does not contain data";
  }

  $validated = array();
  $invalid   = array();
  foreach($params as $name => $type ) {
    $optional = is_string($type) && (strpos($type, "optional") === 0);
    $str_type = (
      $optional
        ? substr($type, 9)
        : $type
    );

    if( isset($data[$name]) && type_check($data[$name], $str_type ) ) {
      $validated[$name] = $data[$name];
      if( 
          ($str_type == "object" || $str_type == "file") 
          && gettype($data[$name]) === "string" 
      ){
        $validated[$name] = json_decode($data[$name]); //overwrite for simplicity
      } 
    } else {
      $str_type = (is_array($type) ? "[".implode(",",$type)."]" : $type);
      if(   $optional == true
        && (isset($data[$name]))
        && (strlen($data[$name])>0)
      ){
        $invalid[] = $name." is not ".$str_type;
      }

      if($optional == false){
        $invalid[] = $name." is not ".$str_type;
      }
    }
  }
  if(!empty($invalid)) {
    //fwrite($fp_log, "error: invalid data ".implode(", ", $invalid)."\n";
    return implode(", ", $invalid);
  }

  return $validated;
}

function get_granted_actions($params, $level){
  $granted = [];
  foreach($params as $action=>$config){
    //echo $action.' '.json_encode($config['grants'])."\n\n";
    if(!isset($config['grants'])){
      http_response_code(500);
      echo json_encode([
        "error" => "action '$action' does not have grants"
      ]);
      die();
    }
    $index = array_search($level, $config['grants']);
    if($index !== false) $granted[$action] = $config['params'];
  }
  return $granted;
}

/* script entry point */


if(
      !isset($_GET['action']) 
  ||  !isset($params[$_GET['action']]) 
){
  http_response_code(400);
  echo json_encode([
    "error"  => (
      isset($_GET['action'])
        ? "action not found"
        : "queryparam action is required"
    ),
    "action" => get_granted_actions($params, 'public')
  ]);

  return;
}

$data     = json_decode(file_get_contents('php://input'), true);
$valid    = input_valid($params[$_GET['action']]['params'], $data);
$userdata = check_authorization($params[$_GET['action']]);

if(!is_array($valid)) {
  echo json_encode([
    "error"         => $valid,
    $_GET['action'] => $params[$_GET['action']]
  ]);
  return;
}

try{
  $result = $_GET['action']($valid, $userdata);

  if(isset($result['query'])){
    $n = explode("\n", $result['query']);
    $v = array_filter(array_map("trim", $n));

    $v = preg_replace("/[[:blank:]]+/", " ", $v);
    // $ro = preg_replace('/\s+/', ' ', $row['message']);

    $result['query'] = implode(" ", $v);
  }
  if( isset($userdata['token']) ){
    $result['token'] = $userdata['token'];
  }
  echo json_encode($result);
} catch(Exception $e) {
  echo json_encode(["error" => $e->getMessage()]);
}

return;


?>
