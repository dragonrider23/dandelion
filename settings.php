<?php
/**
 * This page allows users to change their password
 * and change the number of logs show on the home page.
 *
 * This file is a part of Dandelion
 *
 * @author Lee Keitel
 * @date January 28, 2014
 *
 * @license GNU GPL v3 (see full license in root/LICENSE.md)
 ***/
namespace Dandelion;

require_once 'lib/bootstrap.php';

if (!Gatekeeper\authenticated()) {
	header( 'Location: index.php' );
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="images/favicon.ico" />
		<?php echo loadCssSheets(); ?>
		<title>Dandelion Web Log</title>
	</head>
	<body>
        <header>
            <?php include 'views/header.php'; ?>
        </header>
		
		<h2>User Settings</h2>
		
		<?php require_once 'lib/saveSettings.php';?>

        <form method="post">
            <input type="submit" name="set_action" class="dButton adminButton" value="Reset Password" />
            <br /><br /><hr width="350" /><br />
            
            How many logs do you want to see on the main page:<br />
            <input type="text" name="show_limit" size="3" value="<?php echo $_SESSION['userInfo']['showlimit']; ?>" />
            <input type="submit" name="set_action" class="dButton" value="Save Limit" />
        </form>
        
        <br /><hr width="350" /><br />
        
        <form method="post">
        	Current theme:
        	
        	<?php echo getThemeList(); ?>
        	
            <input type="submit" name="set_action" class="dButton" value="Save Theme" />
        </form>
        
        <?php if ($_SESSION['app_settings']['public_api']) { ?>
        <br /><hr width="350" /><br />
        
        API Key: <span id="apiKey"></span>
        <br><br><input type="button" class="dButton" onClick="api.generateKey();" value="Generate New Key">
        <?php } ?>
        
        <footer>
            <?php include_once 'views/footer.php'; ?>
        </footer>
	</body>
	
	<?php echo loadJS('jquery','settings.js'); ?>
</html>