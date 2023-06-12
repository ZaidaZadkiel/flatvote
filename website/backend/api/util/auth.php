<?php
$auth = "yes";

function check_authorization($action){
  
  if(
        !isset($_SERVER['HTTP_AUTHORIZATION'])
    &&  !isset($headers["authorization"])
  ) return 'public'; // auth not present, show only public methods

  /* just lovely. Fix case insensitive key for headers */
  $headers = array_change_key_case(getallheaders(), CASE_LOWER);
  
  $token = (
    isset($_SERVER['HTTP_AUTHORIZATION'])
      ? $_SERVER['HTTP_AUTHORIZATION']
      : $headers["authorization"]
  );

  //there is auth headers, validate with DB
  return get_profile($token);
}

function get_grant_level($action){
  // search actions that are allowed without requiring authorization
  $needsauth = array_search($action, 'public');
  if($needsauth !== false){
    return true; //we dont need to have user profile data for public endpoints
  }


  $userdata = get_profile($token);
}

function get_profile($token){

  if (!empty($token)){
    $data = json_decode(base64_decode($token), true);
    global $mysql;

    $users = tblUSERS;
    $sql = "SELECT `ts_last_login`, `id`, `txt_name` FROM $users WHERE `id`=:id and `txt_name`=:username";
    $query = $mysql->prepare($sql);
    $query->bindParam(":username", $data["user"]);
    $query->bindParam(":id",       $data["id"]);
    $query->execute();
    $user = $query->fetchAll();

    if(isset($user[0])){
      if($data["key"] == $user[0]["ts_last_login"]) return $data;

      // login not found, test for token

      $sql = "SELECT `ts_creation` FROM `tokens` WHERE  `token`=:token";
      $query = $mysql->prepare($sql);
      $query->bindParam(":token", $token);
      $query->execute();
      $refresh = $query->fetchAll(PDO::FETCH_COLUMN, 0);

      // print_r($data);
      // print_r($refresh);

      if(isset($refresh[0]) ){
        // echo $refresh['ts_creation'];
        $data['token'] = base64_encode(json_encode([
              "id"   => $user[0]["id"],
              "user" => $user[0]["txt_name"],
              "key"  => $user[0]["ts_last_login"]
        ]));
        // print_r($data);
        return $data;
      }

    }
  }

  return false;
}
?>