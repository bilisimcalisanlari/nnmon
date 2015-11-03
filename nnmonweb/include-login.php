<?php
session_start();
if ( ! isset( $_SESSION['user'] ))
{
  $_SESSION['entrypoint'] = $_SERVER['REQUEST_URI'];
  header( 'Location: entry.php' );
}
?>
