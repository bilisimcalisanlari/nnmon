<?php require_once( "include-login.php" );
require_once( "siteconfig.php" );
require( "connectdb.php" );
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
   <META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=UTF-8">
   <TITLE>nnmon - Multi nmon</TITLE>
   <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
   <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7; IE=EmulateIE9"> 
   <!--[if IE]><script src="js/excanvas.compiled.js"></script><![endif]-->
<style>
    @import url( css/nnmonmain.css );
</style>
<script src="js/dygraph-combined.js"></script>
<script src="js/datetimepicker_css.js"></script>
</HEAD>
<BODY LANG="tr-TR" DIR="LTR">
<?php

$beg = $_GET['beg'];
$end = $_GET['end'];

$hours = 12;
if ( $_REQUEST['last'] == 'Last 8 Hours' )
  $hours = 8;
elseif ( $_REQUEST['last'] == 'Last 2 Hours' )
  $hours = 2;

$begstr = " daytime > now() - interval '{$hours} hours'";
$endstr = " and daytime < now()";

if ((( $beg ) && ( !$end )) || (( !$beg ) && ( $end )))
{
  print "Control begin and and dates.";
  exit;
}
if (( $beg ) && ( $hours == 12 ))
  $begstr = " daytime > '{$beg}' ";
else
  $beg = "";
if (( $end ) && ( $hours == 12 ))
  $endstr = " and daytime < '{$end}' ";
else
  $end = "";


$sqlEnt = "select distinct(metric) from perfdata where host='{$_GET['host']}' and topic='IOADAPT' and metric like '%_read-KB/s' and ". $begstr . $endstr . " order by metric";
$ents = array();
foreach ( $conn->query($sqlEnt) as $row )
{
  preg_match( '/(\w+)_read-KB\/s/', $row[0], $uyan );
  array_push($ents,$uyan[1]);
}
$i = 0;
foreach ( $ents as $ent )
{
  $result = array();
  $sqlRead = "select value,daytime from perfdata where topic='IOADAPT' and metric='{$ent}_read-KB/s' and host='" . $_GET['host'] ."' and " . $begstr . $endstr;
  foreach ( $conn->query($sqlRead) as $row )
    $result[$row['daytime']][0] = $row['value'];

  $sqlWrite = "select value,daytime from perfdata where topic='IOADAPT' and metric='{$ent}_write-KB/s' and host='" . $_GET['host'] ."' and " . $begstr . $endstr;
  foreach ( $conn->query($sqlWrite) as $row )
    $result[$row['daytime']][1] = $row['value'];

  $sqlXfer = "select value,daytime from perfdata where topic='IOADAPT' and metric='{$ent}_write-KB/s' and host='" . $_GET['host'] ."' and " . $begstr . $endstr;
  foreach ( $conn->query($sqlXfer) as $row )
    $result[$row['daytime']][2] = $row['value'];

  ksort($result);
  $strresult = '';
  foreach ( $result as $daytime => $value )
    $strresult .= $daytime .','.$value[0].','.$value[1].','.$value[2].'\n';
  echo "<div style='font-size: 18pt; font-weight: bold; text-align: center;'>{$_GET['host']} IO Adapter {$ent} Usage</div><br>";
  if (! $i )
  {
?>
<div style="margin:15px auto 10px auto;">
  <form name="input" action="ioadapter.php" method="get">
  <label for="demo1">Begin:</label>
  <input type="hidden" name="host" value="<?= $_GET['host'] ?>"/>
  <input type="Text" id="beg" name="beg"  maxlength="19" size="16" value="<?= $beg ?>"/>
  <img src="images/cal.gif" onclick="javascript:NewCssCal('beg','yyyyMMdd','dropdown',true,'24',true)" style="cursor:pointer"/>
  <label for="demo1">End:</label>
  <input type="Text" id="end" name="end" maxlength="19" size="16" value="<?= $end ?>"/>
  <img src="images/cal.gif" onclick="javascript:NewCssCal('end','yyyyMMdd','dropdown',true,'24',true)" style="cursor:pointer"/>
  <input type="submit" value="Submit" />
  <input type="submit" name="last" value="Last 2 Hours">
  <input type="submit" name="last" value="Last 8 Hours">
  </form>
</div>
<?php
  }
?>
<div id="<?= $_GET['host'].$ent ?>sysdiv" style="width:<?= $GRAPHLENGTH ?>px; height:300px;"></div>
<script type="text/javascript">
  g = new Dygraph(

    // containing div
    document.getElementById("<?= $_GET['host'].$ent ?>sysdiv"),
    "Time, Read KB/s, Write KB/s, Xfer\n" +
    "<?= $strresult ?>"
  );
</script>

<?php
    $i++;
}
?>
</body>
</html>
