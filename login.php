<?php

require("init.php");

$login_username = isset($_POST["login_username"]) ? $_POST["login_username"] : "";
$login_pass = isset($_POST["login_pass"]) ? $_POST["login_pass"] : "";

# Sanitize user input
$login_username = filter_var($_POST['login_username'], FILTER_SANITIZE_STRING);

# TODO: Maybe this filter applied to the password does not fit our password rules - we will see
#$login_pass = filter_var($_POST['login_pass'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

# Convert username to lower case
$login_username = strtolower($login_username);

if (!empty($login_username) && !empty($login_pass))
{
	$ldap_server = "localhost";
	$ldap_port = 30389;

	$connect = ldap_connect($ldap_server, $ldap_port);
	if (!$connect)
	{
		# If debug mode is on show error message
		if ($debug)
		{
			echo "Failed to connect to the LDAP server.";
		}
		exit;
	}

	ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);

	# bind user
	$auth_user = "uid=" . $login_username . ",<Enter domain name dc=example,dc=com";
	$auth_pass = $login_pass;
	$bind = ldap_bind($connect, $auth_user, $auth_pass);

	# If the bind was successfull
	if ($bind)
	{
		# Get list of all lists the person owns and tranform them into an array
		$array_lists_owned = explode("\n", shell_exec("cd $lists_path/$domain_global ; grep -r \"" . $login_username . "@example.com\" */control/owner | cut -d':' -f1 | cut -d'/' -f1"));

		// Authentication successful - Set session
		$_SESSION["auth"] = 1;
		$_SESSION["username"] = $login_username;
		$_SESSION["domain"] = $domain_global; # This is needed for the script to function properly
		$_SESSION["array_lists_owned"] = $array_lists_owned;

		# Audit log
		$return = audit_log("login");
		if (!$return["success"])
		{
			# If debug mode is on show error message
			if ($debug)
			{
				echo $return["message"];
			}
		}

		header("Location: index.php");
		exit();
	}
	else
	{
		// Incorrect password
		$_SESSION["error_code"] = 3;
		header("Location: error.php");
		exit();
	}
}
else
{
	// If no submission, display login form
	$smarty->assign("headline", $headline);
	$smarty->assign("web_url", $web_url);
	$smarty->display("login.tpl");
}

?>
