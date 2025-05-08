<?php

require("init.php");

$list_name = isset($_GET["list_name"]) ? $_GET["list_name"] : "";
$domain = $_SESSION["domain"];
$success = isset($_GET["success"]) ? true : false;

if (!isset($_SESSION["auth"]) || $_SESSION["auth"] != 1)
{
   // If not authenticated, then redirect to login page
   header("Location: login.php");
   exit();
}

// We do not print any error in the next three cases, because a legitimate
// user will never produce such results, even with disabled javascript
if ( preg_match("/[^a-z0-9_-]/", $list_name) )
{
    header("Location: error.php");
    exit();
}

if ( strlen($list_name) > 50 )
{
    header("Location: error.php");
    exit();
}

// Test list existence
if( !is_dir("$lists_path/$domain/$list_name") || $list_name == "" )
{
    header("Location: error.php");
    exit();
}

# Check whether the user may edit this list as he owns it
if (!in_array($list_name, $_SESSION["array_lists_owned"]))
{
    $_SESSION["error_code"] = 11;
    header("Location: error.php");
    exit;
}

// Get a subscribers list
$subscribers = shell_exec("/usr/bin/mlmmj-list -L $lists_path/$domain/$list_name");
// Remove trailing empty symbols
$subscribers = trim($subscribers);

# Check whether there is a moderators file
if (file_exists("$lists_path/$domain/$list_name/control/moderators"))
{
    // Get a moderators list
    $moderators = file_get_contents("$lists_path/$domain/$list_name/control/moderators");
    // Remove trailing empty symbols
    $moderators = trim($moderators);

    # If theres no @ inside the file it seems to be empty
    if (!preg_match("/[@]/", $moderators))
    {
        $moderators = NULL;
    }
}
else
{
    $moderators = NULL;
}

// Get a prefix
$prefix = file_get_contents("$lists_path/$domain/$list_name/control/prefix");
// Remove trailing empty symbols
$prefix = trim($prefix);

# Check whether there is a listdescription file
if (file_exists("$lists_path/$domain/$list_name/control/listdescription"))
{
    // Get list description
    $listdescription = file_get_contents("$lists_path/$domain/$list_name/control/listdescription");
    // Remove trailing empty symbols
    $listdescription = trim($listdescription);
}
else
{
    $listdescription = NULL;
}


// Load page
$smarty->assign("headline", $headline);
$smarty->assign("web_url", $web_url);
$smarty->assign("subscribers", $subscribers);
$smarty->assign("list_name", $list_name);
$smarty->assign("domain", $domain);
$smarty->assign("moderators", $moderators);
$smarty->assign("prefix", $prefix);
$smarty->assign("listdescription", $listdescription);
$smarty->assign("username", $_SESSION["username"]);
$smarty->assign("success", $success);
$smarty->display("edit_list.tpl");

?>
