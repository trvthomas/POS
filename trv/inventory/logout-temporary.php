<?php include_once $_SERVER['DOCUMENT_ROOT'] . "/trv/include/DBData.php";

setcookie($prefixCoookie . "TemporaryInventoryIdUser", "", time() - 3600, "/");
setcookie($prefixCoookie . "TemporaryInventoryUsernameUser", "", time() - 3600, "/");
header('Location:/trv/home.php');
exit("Logged out");
?>