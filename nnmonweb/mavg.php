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
<div style="margin:15px auto 10px auto;">
  <form name="input" action="mavg.php" method="get">
  <label for="demo1">Begin:</label>
  <input type="hidden" name="serial" value="<?= $_GET['serial'] ?>"/>
  <input type="Text" id="beg" name="beg"  maxlength="19" size="16" value="<?= $_GET['beg'] ?>"/>
  <img src="images/cal.gif" onclick="javascript:NewCssCal('beg','yyyyMMdd','dropdown',true,'24',true)" style="cursor:pointer"/>
  <label for="demo1">End:</label>
  <input type="Text" id="end" name="end" maxlength="19" size="16" value="<?= $_GET['end'] ?>"/>
  <img src="images/cal.gif" onclick="javascript:NewCssCal('end','yyyyMMdd','dropdown',true,'24',true)" style="cursor:pointer"/>
  <input type="submit" value="Submit" />
  </form>
</div>
<?php

$beg = $_GET['beg'];
$end = $_GET['end'];
$serial = $_GET['serial'];

$begstr = " daytime > now() - interval '5 minutes'";
$endstr = "";

if ((( $beg ) && ( !$end )) || (( !$beg ) && ( $end )))
{
  print "Control begin and and dates.";
  exit;
}
if ( $beg )
  $begstr = " daytime > '{$beg}' ";
if ( $end )
  $endstr = " and daytime < '{$end}' ";

$sql = "select b.host,avg(value) from hosts a, perfdata b where " . $begstr . $endstr . " and a.host=b.host and a.serial='{$serial}' and topic='LPAR' and metric='PhysicalCPU' group by b.host order by avg(value) desc";

$hn = $conn->query("select value from parameter where param='".$_GET['serial']."'")->fetch();
if ( ! $hn )
  $hostname = $_GET['serial'];
else
  $hostname = $hn[0];

echo "<div style='font-size: 18pt; font-weight: bold; text-align: center;'>{$hostname}'s LPARs' Avg. CPU Usage</div><br>";

echo "<br><table class='center1' border=1 cellspacing='0' cellpadding='1'>";
echo "<tr><th>Host</th><th>Avg. CPU<br>Usage*</th>";


foreach ( $conn->query($sql) as $row )
{
  echo "<tr><td>{$row['host']}</td><td>". round($row[1],2) . "</td></th>";
  $total += $row[1];
}
echo "<tr style='font-weight:bold'><td>Total:</td><td>".(round($total,2))."</td></tr>";
$sqlHost = "select host from perfdata a where topic='LPAR' and metric='PoolIdle' and value>0 and {$begstr} {$endstr} and exists (select * from hosts b where a.host=b.host and b.serial='".$_GET['serial']."') limit 1";
$hn = $conn->query($sqlHost)->fetch();
$hostn = $hn[0];
if ( ! $hostn )
  exit;
$sqlCPUs = "select avg(value) from perfdata where topic='LPAR' and metric='poolCPUs' and host='{$hostn}' and " . $begstr . $endstr;

$avgcpu = $conn->query($sqlCPUs)->fetch();

$sqlIdle = "select avg(value) from perfdata where topic='LPAR' and metric='PoolIdle' and host='{$hostn}' and " . $begstr . $endstr;

$idlecpu = $conn->query($sqlIdle)->fetch();

$totalmcpu = $avgcpu[0] - $idlecpu[0];

echo "<tr style='font-weight:bold'><td>Total<br>Machine<br>CPU:</td><td>".(round($totalmcpu,2))."</td></tr>";


?>
</table></div>
<br>
* Avg. CPU Usage: Calculated from last 5 minute's values. You can change the interval.
</body>
</html>
