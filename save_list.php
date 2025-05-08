<?php

require("init.php");

function trim_array($arr)
{
    // Trim each array element
    $clean = array();
    foreach($arr as $elem)
    {
        $elem = trim($elem);
        if ( !empty($elem) )
        {
            $clean[] = $elem;
        }
    }
    return $clean;
} 

$list_name = isset( $_POST["list_name"] ) ? $_POST["list_name"] : NULL;
$prefix = isset ( $_POST["prefix"] ) ? $_POST["prefix"] : NULL;
$listdescription = isset ( $_POST["listdescription"] ) ? $_POST["listdescription"] : NULL;
$new_subscribers = isset ( $_POST["subscribers"] ) ? $_POST["subscribers"] : NULL;
$moderators = isset ( $_POST["moderators"] ) ? $_POST["moderators"] : NULL;

if ( !isset($_SESSION["auth"]) || $_SESSION["auth"] != 1 )
{
   // If not authenticated, then redirect to login page
   header("Location: login.php");
   exit();
}

$domain = $_SESSION["domain"];

// We do not print any error in the next four cases, because a legitimate
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

if ( strlen($prefix) > 128 )
{
    // Prefix must not be longer than 128 characters
    $_SESSION["error_code"] = 7;
    header("Location: error.php");
    exit();
}

if ($new_subscribers != NULL)
{
    // Subscribe new subscribers and unsubscribe who is not present in the new list of subscribers
    $new_subscribers = explode("\n", $new_subscribers);
    $new_subscribers = trim_array($new_subscribers);

    $old_subscribers = shell_exec("/usr/bin/mlmmj-list -L $lists_path/$domain/$list_name");
    $old_subscribers = explode("\n", $old_subscribers);
    $old_subscribers = trim_array($old_subscribers);

    foreach ($new_subscribers as $new_subscriber)
    {
        if ( !in_array($new_subscriber, $old_subscribers) )
        {
            if ( !filter_var($new_subscriber, FILTER_VALIDATE_EMAIL) )
            {
                // Incorrect email
                $_SESSION["error_code"] = 9;
                header("Location: error.php");
                exit();
            }
            shell_exec("/usr/bin/mlmmj-sub -L $lists_path/$domain/$list_name -a $new_subscriber -fsq");
        }
    }

    foreach ($old_subscribers as $old_subscriber)
    {
        if ( !in_array($old_subscriber, $new_subscribers) )
        {
            shell_exec("/usr/bin/mlmmj-unsub -L $lists_path/$domain/$list_name -a $old_subscriber -sq");
        }
    }
}

# --- SECURITY CHECK ---

# Check if someone sent a manipulated POST request

# Check whether there is a moderators file
if (file_exists("$lists_path/$domain/$list_name/control/moderators"))
{
    // Get a moderators list
    $moderators_check = file_get_contents("$lists_path/$domain/$list_name/control/moderators");
    // Remove trailing empty symbols
    $moderators_check = trim($moderators_check);

    # If theres no @ inside the file it seems to be empty
    if (!preg_match("/[@]/", $moderators_check))
    {
        $moderators = NULL;
    }
}
else
{
    $moderators = NULL;
}

# --- SECURITY CHECK ---

if ($moderators !== NULL)
{
    # If theres an @ inside the new moderators variable it seems to be empty
    if (preg_match("/[@]/", $moderators))
    {
        $moderators_array = explode("\n", $moderators);
        $moderators_array = trim_array($moderators_array);

        // Check moderators emails
        foreach ($moderators_array as $moderator)
        {
            if ( !filter_var($moderator, FILTER_VALIDATE_EMAIL) )
            {
                // Incorrect email
                $_SESSION["error_code"] = 10;
                header("Location: error.php");
                exit();
            }
        }

        file_put_contents("$lists_path/$domain/$list_name/control/moderators", "$moderators");
    }
    else
    {
        # Someone wants to clear the moderators list which is not allowed (yet)
        #$_SESSION["error_code"] = 12;
        #header("Location: error.php");
        #exit();
    }
}

# Add prefix to the respective file
if ($prefix !== NULL)
{
    file_put_contents("$lists_path/$domain/$list_name/control/prefix", "$prefix");
}

# Add listdescription to the respective file
if ($listdescription !== NULL)
{
    file_put_contents("$lists_path/$domain/$list_name/control/listdescription", "$listdescription");
}

# The following code section is for audit log only

# -------------------------------------------------------------

# Check whether there is a moderators file
if (file_exists("$lists_path/$domain/$list_name/control/moderators"))
{
    // Get a moderators list
    $old_moderators = file_get_contents("$lists_path/$domain/$list_name/control/moderators");
    // Remove trailing empty symbols
    $old_moderators = trim($old_moderators);

    # If theres no @ inside the file it seems to be empty
    if (!preg_match("/[@]/", $old_moderators))
    {
        $old_moderators = NULL;
    }
}
else
{
    $old_moderators = NULL;
}

// Get old prefix
$old_prefix = file_get_contents("$lists_path/$domain/$list_name/control/prefix");
// Remove trailing empty symbols
$old_prefix = trim($old_prefix);

# -------------------------------------------------------------

$return = audit_log("save_list", "Old subscribers: " . json_encode($old_subscribers) . " - Old prefix: " . $old_prefix . " - Old moderators: " . json_encode($old_moderators));
if (!$return["success"])
{
    # If debug mode is on show error message
    if ($debug)
    {
        echo $return["message"];
    }
}

header("Location: edit_list.php?list_name=$list_name&success");

exit();

?>
