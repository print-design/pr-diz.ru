<?php
include '../include/topscripts.php';
include './database_chinese.php';

$result = array('word' => '', 'transcription' => '', 'translation' => '');

$sql = "select word, transcription, translation from words order by rand() limit 1";
$fetcher = new FetcherChinese($sql);
if($row = $fetcher->Fetch()) {
    $result['word'] = $row['word'];
    $result['transcription'] = $row['transcription'];
    $result['translation'] = $row['translation'];
}

header("Access-Control-Allow-Origin: *");
echo json_encode($result);
?>