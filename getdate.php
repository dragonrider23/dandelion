<?php
/**
  * Gets return time and message for user status
  *
  * This file is a part of Dandelion
  * 
  * @author Lee Keitel
  * @date March 2014
  *
  * @license GNU GPL v3 (see full license in root/LICENSE.md)
***/

require_once 'scripts/grabber.php';

if (!authenticated()) {
	header( 'Location: index.php' );
}
?>
<html>
    <head>
        <meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="IE=9">
		<?php loadCssSheets("cheestoWin","jquery","datetimepicker.css",false); ?>
        <title>Dandelion cxeesto</title>
    </head>
    
    <body>
        <form id="getDate">
            <table>
                <thead>
                    <tr><td colspan="2">Quick Set:</td></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>10 Minutes<input type="radio" name="quicktime" onClick="setDateTime(10);"></td>
                        <td>20 Minutes<input type="radio" name="quicktime" onClick="setDateTime(20);"></td>
                    </tr>
                    <tr>
                        <td>30 Minutes<input type="radio" name="quicktime" onClick="setDateTime(30);"></td>
                        <td>40 Minutes<input type="radio" name="quicktime" onClick="setDateTime(40);"></td>
                    </tr>
                    <tr>
                        <td>1 Hour<input type="radio" name="quicktime" onClick="setDateTime(60);">
                        <td>1 Hour 15 Min.<input type="radio" name="quicktime" onClick="setDateTime(75);"></td>
                    </tr>
                    <tr>
                        <td>1 Hour 30 Min.<input type="radio" name="quicktime" onClick="setDateTime(90);"></td>
                        <td>1 Hour 45 Min.<input type="radio" name="quicktime" onClick="setDateTime(105);"></td>
                    </tr>
                    <tr>
                        <td>2 Hours<input type="radio" name="quicktime" onClick="setDateTime(120);"></td>
                        <td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
            <br>
            
            Return Time:<br>
            <input type="text" id="datepick" value="Today"><br><br>
            Message:<br>
            <textarea id="messagetext" cols="25" rows="10"></textarea><br>
            <input type="button" onclick="giveTime();" value="Done">
        </form>
        <?php loadJS("jquery","jqueryui","timepicker.js","slider.js","cheestoGetDate.js");?>
    </body>
</html>