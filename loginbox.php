<?php
namespace Dandelion;

if (!$indexCall) {
    // This redirect statement is an exception
    // Reason: This file doesn't load bootstrap.php (it doesn't need to).
    header('Location: index.php');
}

$status = isset($_SESSION['badlogin']) ? $_SESSION['badlogin'] : '&nbsp;';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="IE=9">
        <link rel="icon" type="image/ico" href="<?php echo FAVICON_PATH; ?>">
        <?php echo loadCssSheets(); ?>
        <title>Dandelion Web Log</title>
    </head>
    <body>
        <div id="login">
            <h1>Dandelion Web Log</h1>
            <form id="login_form" action="lib/authenticate.php" method="post">
            <?php echo $status;
                if (isset($_COOKIE['dan_username'])) {
                    $username = $_COOKIE['dan_username'];
                    $userFocus = '';
                    $passFocus = 'autofocus';
                    $checkBox = '';
                } else {
                    $username = $passFocus = '';
                    $userFocus = 'autofocus';
                    $checkBox = '<br><label><input type="checkbox" value="remember" name="rememberMe"> Remember my username</label><br>';
                }
            ?>
                Username:<br /><input type="text" value="<?php echo $username; ?>" name="in_name" autocomplete="off" <?php echo $userFocus; ?>><br><br>
                Password:<br /><input type="password" value="" name="in_pass" <?php echo $passFocus; ?>><br>
                <?php echo $checkBox; ?>
                <div style="margin-top: 0em; margin-left: 2em;"><input type="submit" value="Login" id="login_button"></div>
            </form>
        </div>
    </body>
</html>
