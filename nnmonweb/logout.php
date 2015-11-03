<?php
session_start();
$_SESSION = array();
session_unset();
session_destroy();
//header( 'Location: entry.php' );
echo "<script>parent.window.location = 'entry.php'</script>";
exit();
?>
