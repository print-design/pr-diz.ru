<?php
include '../include/topscripts.php';

$count = 0;
$sql = "select count(id) from roll";
$fetcher = new Fetcher($sql);
if($row = $fetcher->Fetch()) {
    $count = $row[0];
}
?>

<h1>Ревизия</h1>
<div style="font-size: xx-large;">Всего: <?=$count ?></div>
<br /><br />
<button type="button" id="delete">Сработать</button>
<br /><br />
<div style="font-size: xx-large;" id="deleted">0</div>
<br /><br />
<button type="button" id="restore">Восстановить</button>
<br /><br />
<div style="font-size: xx-large;" id="restored">0</div>
<script src='../js/jquery-3.5.1.min.js'></script>
<script>
    $('#delete').click(function() {
        DeleteNext();
    });
    
    function DeleteNext() {
        $.ajax({ url: "_revision.php" })
                .done(function(data) {
                    $('#deleted').text(data);
                    DeleteNext();
                })
                .fail(function() {
                    $('#deleted').text('Fail');
                });
    }
</script>