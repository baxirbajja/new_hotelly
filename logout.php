<?php
session_start();
session_destroy();
header('Location: /new_hotelly/index.php');
exit;
?>
