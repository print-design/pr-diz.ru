<?php
include_once '../include/topscripts.php';
$request_uri = mb_substr($_SERVER['REQUEST_URI'], mb_strlen(APPLICATION.'/cutter/'));
$sql = "update user set request_uri='$request_uri' where id=". GetUserId();
$error_message = (new Executer($sql))->error;
if(!empty($error_message)) {
    exit($error_message);
}
?>
<div class="container-fluid header">
    <nav class="navbar navbar-expand-sm justify-content-start">
        <ul class="navbar-nav">
            <li class="nav-item">
                <?php
                $data_sources = "";
                for($i=1; $i<=19; $i++) {
                    if(!empty(filter_input(INPUT_GET, 'stream_'.$i))) {
                        $data_sources .= " data-stream".$i."=".filter_input(INPUT_GET, 'stream_'.$i);
                    }
                }
                ?>
                <button type="button" class="nav-link btn btn-link goto_cut" data-supplier_id="<?= filter_input(INPUT_GET, 'supplier_id') ?>" data-film_brand_id="<?= filter_input(INPUT_GET, 'film_brand_id') ?>" data-thickness="<?= filter_input(INPUT_GET, 'thickness') ?>" data-width="<?= filter_input(INPUT_GET, 'width') ?>" data-streams-count="<?= filter_input(INPUT_GET, 'streams_count') ?>"<?=$data_sources ?>><i class="fas fa-chevron-left"></i>&nbsp;Назад</button>
            </li>
        </ul>
    </nav>
</div>
<div id="topmost"></div>
<?php
print_r($_GET);
?>