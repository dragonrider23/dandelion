<?php
/**
  * Dandelion - Web based log journal
  *
  * @author Lee Keitel  <keitellf@gmail.com>
  * @copyright 2015 Lee Keitel, Onesimus Systems
  *
  * @license GNU GPL version 3
  */
namespace Dandelion;

$configDir = __DIR__.'/../config';

$config = [
    'db' => [
        'type' => $_POST['db_type'] ?: '',
        'dbname' => $_POST['db_name'] ?: '',
        'hostname' => $_POST['db_host'] ?: '',
        'username' => $_POST['db_user'] ?: '',
        'password' => $_POST['db_pass'] ?: '',
        'tablePrefix' => $_POST['db_prefix'] ?: 'dan_',
    ],

    'cheesto' => [
        'statusOptions' => [
            'Available',
            'Away From Desk',
            'At Lunch',
            'Out for Day',
            'Out',
            'Appointment',
            'Do Not Disturb',
            'Meeting',
            'Out Sick',
            'Vacation'
        ]
    ],

    'hostname' => $_POST['hostname'] ? rtrim($_POST['hostname'], '/') : 'http://localhost',
    'cookiePrefix' => $_POST['cookie_prefix'] ?: 'dan_',
    'phpSessionName' => 'session_1',
    'gcLottery' => [2, 100],
    'sessionTimeout' => 360,
    'debugEnabled' => false,
    'installed' => true,
    'appTitle' => $_POST['apptitle'] ?: 'Dandelion Web Log',
    'tagline' => $_POST['tagline'] ?: '',
    'defaultTheme' => 'modern',
    'cheestoEnabled' => true,
    'publicApiEnabled' => false
];

try {
    if (!is_writable($configDir)) { // Is it possible to write the config file?
        throw new Exception('Dandelion does not have sufficient write permissions to create configuration.<br />Please make the app/config directory writeable to Dandelion and try again.');
    }

    switch ($config['db']['type']) {
        case 'mysql':
            if ($config['db']['hostname'] && $config['db']['dbname']) {
                $db_connect = "mysql:host={$config['db']['hostname']};dbname={$config['db']['dbname']}";
            } else {
                throw new Exception('No hostname or database name specified');
            }
            break;

        default:
            throw new Exception('No database driver loaded.');
            break;
    }

    $conn = new PDO($db_connect, $config['db']['username'], $config['db']['password']);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents(__DIR__."/base_{$config['db']['type']}_db.sql");

    // Replace the prefix placeholder with the user defined prefix
    $sql = str_replace('{{prefix}}', $config['db']['tablePrefix'], $sql);

    $exec = $conn->prepare($sql);
    $success = $exec->execute();

    if (!$success) {
        throw new Exception('Problem installing initial data into database.');
    }

    $conn = null;

    // Save as new configuration file
    $configFile = '// This file was generated on '.date(DATE_RFC2822)."\n";
    $configFile .= '<?php return '.var_export($config, true).';';
    file_put_contents($configDir.'/config.php', $configFile);

    // Change config directory to user:readonly for security
    chmod($configDir.'/config.php', 0400);
    chmod($configDir, 0500);

    session_destroy();
    $_SESSION['error'] = 'Dandelion has been successfully setup! Please go to: <a href="'.$config['hostname'].'">'.$config['hostname'].'</a>';
    header("Location: {$config['hostname']}");
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error setting up database. Please verify database name, address, username, and password. ';
} catch (Exception $e) {
    $_SESSION['error'] = 'Error: '.$e->getMessage();
}
