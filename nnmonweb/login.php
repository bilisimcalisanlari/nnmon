<?php 
$us = $_POST['user'];
if ( ! ctype_alnum($us) )
{
  header( 'Location: entry.php?err=notalpha' );
  exit;
}	
require_once( "siteconfig.php" );
require_once( "connectdb.php" );
$ru = $conn->query("select value from parameter where param='restrictusers'")->fetch();
if ( $ru[0] )
{
  $ut = $conn->query("select nnuser from nnusers where nnuser='".$_POST['user'] ."'")->fetch();
  if ( ! $ut[0] )
  {
    header( 'Location: entry.php?err=userrestricted' );
    exit;
  }
}
$ldaprdn = $_POST['user'].$ldapdomain; 
$ldaprdnG = $_POST['user'].$ldapdomainG; 
$ldappass = $_POST['password'];
$ldapconn = @ldap_connect( $ldapserver );
$ldapconnG = @ldap_connect( $ldapserverG );
$ldapbind = @ldap_bind($ldapconn, $ldaprdn, $ldappass); 
$ldapbindG = @ldap_bind($ldapconnG, $ldaprdnG, $ldappass); 
if ( $ldapbind OR $ldapbindG )
{
  session_start();
  $_SESSION['user'] = $_POST['user'];
  isset( $_SESSION['entrypoint'] ) ? header( 'Location: ' . $_SESSION['entrypoint'] ) : header( 'Location: index.php' );
}
else
  header( 'Location: entry.php?err=userpass' );
?>
