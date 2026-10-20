<script src="<?=APPLICATION ?>/js/cut.js?v=<?=filemtime(__DIR__.'/../js/cut.js') ?>"></script>

<?php if(IsInRole(CUTTER_USERS)): ?>
<script>
    function ShowCutterName() {
        $('span#top_user_name').load("<?=APPLICATION ?>/cut/_get_user.php");
    }
    
    ShowCutterName();
    setInterval(ShowCutterName, 60000);
</script>
<?php endif; ?>
