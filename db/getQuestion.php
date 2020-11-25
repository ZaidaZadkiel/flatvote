<?php
include 'pdo.php';

function delete_col(&$array, $key)
{
    // Check that the column ($key) to be deleted exists in all rows before attempting delete
    foreach ($array as &$row)   { if (!array_key_exists($key, $row)) { return false; } }
    foreach ($array as &$row)   { unset($row[$key]); }
    unset($row);
    return true;
}

if(isset($_GET['id']) && is_numeric($_GET['id']) ){

      $valid = $conn->query("SELECT
      flatvote_questions.enm_status ,
      (select count(*) FROM flatvote_options where flatvote_options.id_question={$_GET['id']}) as choices
      from flatvote_questions where flatvote_questions.id={$_GET['id']}")->fetch();

    switch($valid["enm_status"]){
      default: echo '{"error":"enm_status is null or internal server error"}'; break;

      case 'canceled': echo "canceled"; break;
      case 'proposal period':

      break;
      case 'notify period': echo "notify period"; break;
      case 'vote period':

          $sql = "SELECT
                  flatvote_options.id,flatvote_options.txt_description, flatvote_options.ts_date as `ts_date_option`,
                  flatvote_questions.txt_question,flatvote_questions.ts_date as `ts_date_question`, flatvote_questions.enm_status
                  FROM `flatvote_questions` JOIN flatvote_options on flatvote_questions.id_options=flatvote_options.id_question where flatvote_questions.id={$_GET['id']}";
          $res = $conn->query($sql)->fetchAll();
          if($res!=false){
            /*TODO: join this two selects into one with two select rows */
            $sql = "SELECT count(*) FROM `flatvote_votes` where `id_question` = {$_GET['id']}";
            $count = $conn->query($sql)->fetchColumn();

            $sql = "SELECT timestamp, id_ballot, txt_comment FROM `flatvote_votes` where id_question = {$_GET['id']}  order by timestamp DESC";
            $comments = $conn->query($sql)->fetchAll();


            $result = array("id_question"=>$_GET['id'], "txt_question"=>$res[0]['txt_question'],"ts_date_question"=>$res[0]['ts_date_question'], "count"=>$count, "enm_status"=>$res[0]['enm_status']);
            delete_col($res, "txt_question");
            delete_col($res, "ts_date_question");
            $result["options"] = $res;
            $result["comments"] = $comments;
            echo(json_encode($result));
          } else {
            echo '{"error":"question id not found"}';
          }

        break;

      case 'vote over':
      echo "what"; break;
    }



} else {
  echo '{"error":"urlparam id not numeric or empty"}';
}
?>
