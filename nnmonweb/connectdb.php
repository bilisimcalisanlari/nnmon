<?php
session_start();
try
{
  $conn = new PDO( "pgsql:dbname=$DBNAME;host=$DBHOST", $DBUSER, $DBPASS, array( PDO::ATTR_PERSISTENT => true ));
  $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
  $_SESSION['dbconnected'] = 1;
} catch ( PDOException $e )
{
  $_SESSION['dbconnected'] = 0;
  echo "<script>parent.window.location = 'entry.php?err=nodbconn'</script>";
}
?>
