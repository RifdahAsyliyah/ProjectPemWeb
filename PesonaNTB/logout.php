<?php
session_start();
session_destroy();
header("Location: config/login.php");
exit();
?>