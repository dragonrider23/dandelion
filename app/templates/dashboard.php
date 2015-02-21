<?php
/**
 * Dashboard page
 */
$this->layout('layouts::main', ['requiredCssFiles' => ['dashboard','cheesto','jqueryui']]);
?>
<!-- Begin Page Body -->
<div id="dashboard">
    <?php
    if ($showCheesto):
    ?>
    <div id="presence">
        <h3><a href="#" onClick="presence.showHideP();"><span id="showHide">[ - ]</span></a> &#264;eesto: <a href="mail"><img id="mailicon" src="assets/images/nomail.png" width="32" height="16" alt="No Mail"></a></h3>

    	<div id="mainPresence"></div>
    </div>
    <?php
    endif;

    if ($showLog): ?>
        <div id="add_edit" title="">
            <form id="add_edit_form">
                Title: <input type="text" id="logTitle" name="logTitle" value="" size="60"><br><br>
                <textarea id="logEntry" name="logEntry" cols="80" rows="10"></textarea><br>
                <div class="categories">
                    Category: <span id="catSpace"></span>
                </div>
            </form>
            <div id="messages"></div>
        </div>
        <div id="dialogBox"></div>

        <div id="controlPanel">
            <form>
                <input type="search" id="searchquery" size="40" onKeyPress="return searchFun.check(event);" autocomplete="off"><!--
                --><input type="button" value="Search" class="dButton cpButton" onClick="searchFun.searchlog();"><!--
                --><?= $createButton ?>
            </form>
        </div>

        <div id="logs"><img src="assets/images/loaderAnimation.gif" alt="Loading content"> Loading content...</div>
    <?php endif; ?>
</div>

<?= $this->loadJS(['jquery','jqueryui','common','tinymce','cheesto','catManage','dashboard','mail']) ?>
<script type="text/javascript">
    refreshFun.runFirst();
    presence.checkstat(0);
    mail.areUnread();
    $('#presence').draggable();
</script>
<!-- End Page Body -->
