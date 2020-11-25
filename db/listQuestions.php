<?php
include 'pdo.php';

$sql = "SELECT DISTINCT

flatvote_questions.id,
flatvote_questions.txt_question,
flatvote_questions.enm_status,
flatvote_questions.ts_date,
(SELECT COUNT(*) FROM flatvote_options
        WHERE `flatvote_options`.`id_question` = `flatvote_questions`.`id_options`) AS choices,
(SELECT COUNT(*) FROM flatvote_votes
        WHERE `flatvote_votes`.`txt_comment` <> '' and `flatvote_questions`.`id` = flatvote_votes.id_question) AS comments,
(SELECT COUNT(*) FROM flatvote_votes WHERE `flatvote_questions`.`id` = flatvote_votes.id_question) AS votes

FROM flatvote_questions";
$res = $conn->query($sql)->fetchAll();
echo json_encode($res);
?>
