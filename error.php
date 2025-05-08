<?php

require("init.php");

$error_code = isset($_SESSION["error_code"]) ? $_SESSION["error_code"] : "";
unset($_SESSION["error_code"]);

$smarty->assign("headline", $headline);
$smarty->assign("web_url", $web_url);
$smarty->assign("username", $_SESSION["username"]);
$smarty->assign("error_code", $error_code);
$smarty->display("error.tpl");

?>
