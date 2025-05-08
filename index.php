<?php

# Scan loading time
$time_start = microtime(true);

require("init.php");

if (!isset($_SESSION["auth"]) || $_SESSION["auth"] != 1)
{
   // If not authenticated, then redirect to login page
   header("Location: login.php");
   exit();
}

$domain = $_SESSION["domain"];

// Are there any lists?
if ( count( glob("$lists_path/$domain/*") ) !== 0 )
{
	// Get all folders and tranform into array
	$lists = explode("\n", shell_exec("cd $lists_path/$domain; ls -1d */ | cut -f1 -d'/'"));
}

if (isset($lists))
{
	// If the last string is empty then delete it
	if (end($lists) === "")
	{ 
		array_pop($lists);
	}

	$lists_new = [];

	# Iterate through all lists
	foreach($lists as $list)
	{
		# If list is in array of owned lists
		if (!in_array($list, $_SESSION["array_lists_owned"]))
		{
			$lists_new[$list]["iamowner"] = 0;
		}
		else
		{
			$lists_new[$list]["iamowner"] = 1;
		}

		# Get the owners of the list and put them into the array
		$owners = explode("\n", trim(shell_exec("/usr/bin/mlmmj-list -o -L $lists_path/$domain/$list")));
		$lists_new[$list]["owners"] = $owners;

		# Check whether there is a listdescription file
		if (file_exists("$lists_path/$domain/$list/control/listdescription") && @file_get_contents("$lists_path/$domain/$list/control/listdescription") != "")
		{
			// Get list description
			$listdescription = file_get_contents("$lists_path/$domain/$list/control/listdescription");
			// Remove trailing empty symbols
			$listdescription = trim($listdescription);
		}
		else
		{   
			# Set listdescription to none
			$listdescription = "none";
		}

		# Add the listdescription to the array
		$lists_new[$list]["description"] = $listdescription;
	}
}
else
{
	$lists = NULL;
}

# Scan loading time
$time_end = microtime(true);

# Calculate loading time
$loadingtime = round(($time_end - $time_start), 2);

$smarty->assign("headline", $headline);
$smarty->assign("web_url", $web_url);
$smarty->assign("lists", $lists_new);
$smarty->assign("domain", $domain);
$smarty->assign("username", $_SESSION["username"]);
$smarty->assign("loadingtime", $loadingtime);
$smarty->display("index.tpl");

?>
