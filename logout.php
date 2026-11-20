<?php
require "security.php";
session_destroy();
header("Location: login.php");
exit;
?>
