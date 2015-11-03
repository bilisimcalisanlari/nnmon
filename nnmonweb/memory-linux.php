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

$sqlMt = "select value,daytime from perfdata where topic='MEM' and metric='memtotal' and host='" . $_GET['host'] ."' and " . $begstr . $endstr;

$result = array();

foreach ( $conn->query($sqlMt) as $row )
{
  $result[$row['daytime']][1] = $row['value'];
}

$sqlMf = "select value,daytime from perfdata where topic='MEM' and metric='memfree' and host='" . $_GET['host'] ."' and " . $begstr . $endstr;

foreach ( $conn->query($sqlMf) as $row )
{
  $result[$row['daytime']][0] = $result[$row['daytime']][1] - $row['value'];
}

ksort($result);
foreach ( $result as $daytime => $value )
  $strresult .= $daytime .','.$value[0].','.$value[1].'\n';

$sqlPage = "select value,daytime from perfdata where topic='MEM' and metric='swaptotal' and host='" . $_GET['host'] ."' and " . $begstr . $endstr;

$resultPage = array();

foreach ( $conn->query($sqlPage) as $row )
{
  $resultPage[$row['daytime']][1] = $row['value'];
}

$sqlPage = "select value,daytime from perfdata where topic='MEM' and metric='swapfree' and host='" . $_GET['host'] ."' and " . $begstr . $endstr;

foreach ( $conn->query($sqlPage) as $row )
{
  $resultPage[$row['daytime']][0] = $resultPage[$row['daytime']][1] - $row['value'];
}

ksort($resultPage);
foreach ( $resultPage as $daytime => $value )
  $strresultPage .= $daytime .','.$value[0].','.$value[1].'\n';

echo "<div style='font-size: 18pt; font-weight: bold; text-align: center;'>{$_GET['host']} Memory Usage</div><br>";
?>
<div style="margin:15px auto 10px auto;">
  <form name="input" action="memory-linux.php" method="get">
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

<div id="<?= $_GET['host'] ?>sysdiv" style="width:<?= $GRAPHLENGTH ?>px; height:300px;"></div>
<script type="text/javascript">
  g = new Dygraph(

    // containing div
    document.getElementById("<?= $_GET['host'] ?>sysdiv"),
    "Time, Used Mem, Total Mem\n" +
    "<?= $strresult ?>"
   );
</script>
<br><br><br><br>
<div style='font-size: 18pt; font-weight: bold; text-align: center;'><?= $_GET['host'] ?> Paging Space Usage</div><br>

<div id="<?= $_GET['host'] ?>sysdivPage" style="width:<?= $GRAPHLENGTH ?>px; height:300px;"></div>
<script type="text/javascript">
  g = new Dygraph(

    // containing div
    document.getElementById("<?= $_GET['host'] ?>sysdivPage"),
    "Time, Used Paging Space, Total Paging Space\n" +
    "<?= $strresultPage ?>"
  );
</script>
</div>
</body>
</html>
