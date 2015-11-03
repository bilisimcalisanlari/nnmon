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

$sqlList = "select distinct(metric) from perfdata where host='{$_GET['host']}' and topic='DISKXFER' and daytime >now() -interval '12 hours' order by metric";

$disks = array();
foreach ( $conn->query($sqlList) as $row )
{
  preg_match( '/([a-zA-Z]+)([0-9]+)/', $row[0], $matched );
  if ($matched[0])
  {
    if ( ! $disks[$matched[1]] )
      $disks[$matched[1]] = array();
    array_push($disks[$matched[1]],$matched[2]);
    //print($matched[1]."-".$matched[2]."#");
  }
}
ksort($disks);
$numDisk = 4;
$countDisk = 0;
$first = 1;
$argDisk = "";
$loopArg = "";
foreach ( $disks as $diskName => $disk )
{
  sort($disk);
  foreach ( $disk as $dis )
    if ( $countDisk == 0 )
    {
      $argDisk = $diskName.$dis;
      $countDisk = 1;
    }
    else if ( $countDisk < $numDisk )
    {
      $argDisk .= "-".$diskName.$dis;
      $countDisk++;
    }
    else
    {
      $countDisk = 1;      
      $diskHref .= "<a href=\"diskxfer2.php?host={$_GET['host']}&beg={$beg}&end={$end}&diskarg={$argDisk}\">{$argDisk} </a>";
      if ( $first )
      {
	$loopArg = $argDisk;
	$first = 0;
      }
      $argDisk = $diskName.$dis;
    }
}
if (( $countDisk > 0 ) && ( $countDisk <= $numDisk ))
{
  $diskHref .= "<a href=\"diskxfer2.php?host={$_GET['host']}&beg={$beg}&end={$end}&diskarg={$argDisk}\">{$argDisk} </a>";
  if ( $first )
    $loopArg = $argDisk;
}
print($diskHref);

if ( $_GET['diskarg'] )
  $loopArg = $_GET['diskarg'];

//print($loopArg);

$i = 0;
preg_match_all("([a-zA-Z0-9]+)",$loopArg,$matchAll);
foreach ( $matchAll[0] as $disk )
{
  $sqlXfer = "select value,daytime from perfdata where topic='DISKXFER' and metric='{$disk}' and host='" . $_GET['host'] ."' and " . $begstr . $endstr;
  $result = array();
  foreach ( $conn->query($sqlXfer) as $row )
    $result[$row['daytime']][0] = $row['value'];
  $sqlRxfer = "select value,daytime from perfdata where topic='DISKRXFER' and metric='{$disk}' and host='" . $_GET['host'] ."' and " . $begstr . $endstr;
  foreach ( $conn->query($sqlRxfer) as $row )
    $result[$row['daytime']][1] = $row['value'];
  $strresult = '';
  ksort($result);
  foreach ( $result as $daytime => $value )
    $strresult .= $daytime .','.$value[0].','.$value[1].'\n';
  echo "<br><div style='font-size: 18pt; font-weight: bold; text-align: center;'>{$_GET['host']} Disk {$disk} Xfer Count</div><br>";
  if ( ! $i )
  {
?>
<div style="margin:15px auto 10px auto;">
  <form name="input" action="diskxfer2.php" method="get">
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
    "Time, Total Xfer, Read Xfer\n" +
    "<?= $strresult ?>"
  );
</script>

    <?php
    $i++;
    }
?>
</body>
</html>
