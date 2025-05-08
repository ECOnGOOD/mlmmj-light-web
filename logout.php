<?php

require("init.php");

# Audit log
$return = audit_log("logout");
if (!$return["success"])
{
	# If debug mode is on show error message
	if ($debug)
	{
	    echo $return["message"];
	}
}

unset($_SESSION["auth"]);
unset($_SESSION["domain"]);
unset($_SESSION["array_lists_owned"]);
unset($_SESSION["username"]);
unset($_SESSION["error_code"]);

header("Location: index.php");
exit();

?>
