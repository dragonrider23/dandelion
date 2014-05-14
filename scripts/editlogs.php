<?php
/**
  * Handle requests to save edits to log entries
  *
  * This file is a part of Dandelion
  * 
  * @author Lee Keitel
  * @date March 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/

require_once 'grabber.php';

if (!authenticated()) {
	header( 'Location: index.php' );
}

if ($_SESSION['rights']['editlog']) {
    $editedlog = isset($_POST['editlog']) ? $_POST['editlog'] : '';
    $editedtitle = isset($_POST['edittitle']) ? $_POST['edittitle'] : '';
    $logid  = isset($_POST['choosen']) ? $_POST['choosen'] : '';
    
    if (!empty($editedlog) && !empty($editedtitle) && !empty($logid)) {
    	$conn = new dbManage();
    
    	$stmt = 'UPDATE `'.DB_PREFIX.'log` SET `title` = :eTitle, `entry` = :eEntry, `edited` = 1 WHERE `logid` = :logid';
    	$params = array(
    	    'eTitle' => urldecode($editedtitle),
    	    'eEntry' => urldecode($editedlog),
    	    'logid' => $logid
    	);
    	$conn->queryDB($stmt, $params);
    	
    	echo '"'.urldecode($editedtitle).'" edited successfully.';
    }
    else {
        echo '<span class="bad">Log entries must have a title, category, and entry text.</span>';
    }
}

else {
    echo 'This account can\'t edit logs';
}