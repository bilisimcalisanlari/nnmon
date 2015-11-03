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


$sqlSys = "select value,daytime from perfdata where topic='CPU_ALL' and metric='Sys%' and host='" . $_GET['host'] ."' and " . $begstr . $endstr;

$result = array();

foreach ( $conn->query($sqlSys) as $row )
{
  $result[$row['daytime']][0] = $row['value'];
}

$sqlWait = "select value,daytime from perfdata where topic='CPU_ALL' and metric='Wait%' and host='" . $_GET['host'] ."' and " . $begstr . $endstr;

foreach ( $conn->query($sqlWait) as $row )
{
  $result[$row['daytime']][1] = $row['value'];
}

$sqlUser = "select value,daytime from perfdata where topic='CPU_ALL' and metric='User%' and host='" . $_GET['host'] ."' and " . $begstr . $endstr;

foreach ( $conn->query($sqlUser) as $row )
{
  $result[$row['daytime']][2] = $row['value'];
}

ksort($result);

foreach ( $result as $daytime => $value )
  $strresult .= $daytime .','.$value[0].','.$value[1].','.$value[2].'\n';

echo "<div style='font-size: 18pt; font-weight: bold; text-align: center;'>{$_GET['host']} CPU Usage Percentage</div><br>";
?>
<div style="margin:15px auto 10px auto;">
  <form name="input" action="cpu.php" method="get">
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
    "Time, Sys, Wait, User CPU\n" +
    "<?= $strresult ?>",
    { fillGraph:true, stackedGraph:true }
  );
</script>

<?php
$i = 1;
$result = array();
while (1)
{
  $cpu2 = sprintf('CPU%02d', $i );
  $cpu3 = sprintf('CPU%03d', $i );
  $sqlCpu = "select daytime, value from perfdata where host='{$_GET['host']}' and ( topic='{$cpu2}' or topic='{$cpu3}' ) and metric='Idle%' and ". $begstr . $endstr;
  $j = 0;
  foreach ( $conn->query($sqlCpu) as $row )
  {
    $result[$row['daytime']][$i] = 100 - $row['value'];
    $j++;
  }
  if ( ! $j )
    break;
  $i++;
}
ksort($result);
$strresult = '';
$cpu = 0;
foreach ( $result as $daytime => $values ) 
{
  $str = '';
  if ( ! $cpu )
  {
    for ( $i=1; $i<=sizeof($values); $i++ )
    {
      $desc .= $i.',';
    }
    $desc =   preg_replace( '/,$/', '', $desc );
    $cpu = 1;
  }
  for ( $i=1; $i<=sizeof($values); $i++ )
    $str .= $values[$i] .',';
  $str =   preg_replace( '/,$/', '', $str );
  $strresult .= $daytime.','.$str.'\n';
}
if ( $srtresult )
{
echo "<br><br><div style='font-size: 18pt; font-weight: bold; text-align: center;'>{$_GET['host']} All CPUs</div><br>";

?>

<div id="<?= $_GET['host'] ?>.cpus.sysdiv" style="width:<?= $GRAPHLENGTH ?>px; height:300px;"></div>
<script type="text/javascript">
  g = new Dygraph(

    // containing div
    document.getElementById("<?= $_GET['host'] ?>.cpus.sysdiv"),
    "Time,<?= $desc ?>\n" +
    "<?= $strresult ?>",
    { fillGraph:true, stackedGraph:true }
  );
</script>
<?php
}
?>
</body>
</html>
