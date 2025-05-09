<?php

// Loading config
#$config = file_get_contents("misc/config.txt");
#preg_match("/lists_path[\s]*=[\s]*(.*)/", $config, $lists_path);
#$lists_path = $lists_path[1];
#preg_match("/web_path[\s]*=[\s]*(.*)/", $config, $web_path);
#$web_path = $web_path[1];
#preg_match("/web_url[\s]*=[\s]*(.*)/", $config, $web_url);
#$web_url = $web_url[1];
#preg_match("/language[\s]*=[\s]*(.*)/", $config, $language);
#$language = $language[1];

# -------------------------------------------------------------

# We want to have the config values in here to easily be able to extend it
$lists_path = "<Enter mlmmj directory>";
$web_path = "<Enter list.example.com/htdocs-ssl/mlmmj-light-web-ecg/ directory>";
$web_url = "<Enter https://list.example.com/>";
$language = "en";
$domain_global = "mlmmj";
$rc_webhook = "";

# No need to change this values
$current_version = "v1.2.1";
$headline = "Manage your Mailing Lists " . $current_version;
$debug = false;

# -------------------------------------------------------------

# Custom PHP functions

# Registers user's activity in a file (audit log)
function audit_log($action = "", $custom_input = "", $logfile = "audit_log.php")
{
	# Initially set return value to false
	$return['success'] = false;
	$return['message'] = "";

	# If $action is empty
	if ($action == "")
	{
		$action = "-empty-";
	}

	# If $custom_input is empty
	if ($custom_input == "")
	{
		$custom_input = "-empty-";
	}

	# Build the request string
	$request = date("Ymd-His") . " " . $_SESSION['username'] . "\n";
	$request .= "Action: " . $action . "\n";
	$request .= "Custom input: " . $custom_input . "\n";

	# For security reasons don't include the GET/POST requests during login process as they might contain passwords
	if ($action != "login")
	{
		$request .= "\$_SESSION['array_lists_owned']: " . json_encode($_SESSION['array_lists_owned']) . "\n";
		$request .= "\$_GET: " . json_encode($_GET) . "\n";
		$request .= "\$_POST: " . json_encode($_POST) . "\n";
	}
	
	$request .= "\$_SERVER['PHP_SELF']: " . basename($_SERVER['PHP_SELF']) . "\n";
	$request .= "\$_SERVER['REMOTE_ADDR']: " . basename($_SERVER['REMOTE_ADDR']) . "\n";
	$request .= "\n\n";

	# Let's make sure the log file exists and is writable
	if (is_writable($logfile))
	{
		# We're opening $filename in append mode.
		if (!$handle = fopen($logfile, 'a')) {
			 $return['message'] = "Cannot open file ($logfile)";
		}

		// Write $somecontent to our opened file.
		if (fwrite($handle, $request) === FALSE) {
			$return['message'] = "Cannot write to file ($logfile)";
		}

		$return['success'] = true;

		fclose($handle);

	}
	else
	{
		$return['message'] = "The file $logfile is not writable.";
	}

	return $return;
}


# -------------------------------------------------------------

if ($debug)
{
	error_reporting(E_ALL && ~E_NOTICE);
	ini_set("display_errors", 1);
}

// Initializing Smarty
require("misc/smarty_libs/Smarty.class.php");
$smarty = new Smarty();

$smarty->setTemplateDir("misc/smarty/templates_$language");
$smarty->setCompileDir("misc/smarty/templates_c");
$smarty->setCacheDir("misc/smarty/cache");
$smarty->setConfigDir("misc/smarty/configs");

session_start();

?>
