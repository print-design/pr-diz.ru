<?php if(IsInRole(CUTTER_USERS)): ?>
<script>
    function ShowCutterName() {
        $('span#top_user_name').load("<?=APPLICATION ?>/cut/_get_user.php");
    }
    
    ShowCutterName();
    setInterval(ShowCutterName, 60000);
</script>
<?php endif; ?>