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


$sqlList = "select distinct(metric) from perfdata where host='{$_GET['host']}' and topic='JFSFILE' and ". $begstr . $endstr . " order by metric";
$disks = array();
foreach ( $conn->query($sqlList) as $row )
  array_push($disks,$row[0]);
$i = 0;
foreach ( $disks as $disk )
{
  $sqlPer = "select value,daytime from perfdata where topic='JFSFILE' and metric='{$disk}' and host='" . $_GET['host'] ."' and " . $begstr . $endstr;
  $result = array();

  foreach ( $conn->query($sqlPer) as $row )
    $result[$row['daytime']] = $row['value'];
  ksort($result);
  $strresult = '';
  foreach ( $result as $daytime => $value )
    $strresult .= $daytime .','.$value.'\n';
  echo "<br><div style='font-size: 18pt; font-weight: bold; text-align: center;'>{$_GET['host']} Filesystem {$disk} % Usage</div><br>";
  if ( ! $i )
  {
?>
<div style="margin:15px auto 10px auto;">
  <form name="input" action="filesystems.php" method="get">
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
<div id="<?= $_GET['host'].$disk ?>sysdiv" style="width:<?= $GRAPHLENGTH ?>px; height:300px;"></div>
<script type="text/javascript">
  g = new Dygraph(

    // containing div
    document.getElementById("<?= $_GET['host'].$disk ?>sysdiv"),
    "Time, Used %\n" +
    "<?= $strresult ?>"
  );
</script>

    <?php
    $i++;
    }
?>
</body>
</html>
