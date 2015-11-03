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

$sqlHosts = "select host from hosts a where os='AIX' and serial='".$_GET['serial']."' and exists(select host from perfdata b where topic='MEMNEW' and metric='System%' and daytime>now()-interval'12:00:00' and a.host=b.host) order by host";

$hn = $conn->query("select value from parameter where param='".$_GET['serial']."'")->fetch();
if ( ! $hn )
  $hostname = $_GET['serial'];
else
  $hostname = $hn[0];
$hosts = array();
foreach ( $conn->query($sqlHosts) as $row )
  array_push($hosts,$row[0]);

$result = array();
$i = 0;
foreach ( $hosts as $host )
{
  $sqlCpu = "select value,daytime from perfdata where topic='LPAR' and metric='PhysicalCPU' and host='{$host}' and mod(cast(to_char(daytime,'MI') as int),3)=0 and cast(to_char(daytime,'SS') as int)>29 and " . $begstr . $endstr;
  $desc .= $host.',';
  $showhide .= '<input type=checkbox id='.$i.' onClick="showhide(this)" checked>';
  $showhide .= '<label for="'.$i.'">'.$host.' </label>';
  foreach ( $conn->query($sqlCpu) as $row )
    $result[$row['daytime']][$i] = $row['value'];
  $i++;
}
ksort($result);

$desc = preg_replace('/,$/', '', $desc);
$lastvalues = array();
foreach ( $result as $daytime => $values )
{
  $str = '';
  for ( $j = 0; $j < $i ; $j++ )
  {
    if ( $values[$j] )
      $lastvalues[$j] = $values[$j];
    $str .= $lastvalues[$j].',';
  }
  $str = preg_replace('/,$/', '', $str);
  $strresult .= $daytime.','.$str.'\n';
}

  echo "<div style='font-size: 18pt; font-weight: bold; text-align: center;'>{$_GET['host']} Hardware {$hostname} Total CPU Usage</div><br>";
?>
<div style="margin:15px auto 10px auto;">
  <form name="input" action="hardware.php" method="get">
  <label for="demo1">Begin:</label>
  <input type="hidden" name="serial" value="<?= $_GET['serial'] ?>"/>
  <input type="Text" id="beg" name="beg"  maxlength="19" size="16" value="<?= $beg ?>"/>
  <img src="images/cal.gif" onclick="javascript:NewCssCal('beg','yyyyMMdd','dropdown',true,'24',true)" style="cursor:pointer"/>
  <label for="demo1">End:</label>
  <input type="Text" id="end" name="end" maxlength="19" size="16" value="<?= $end ?>"/>
  <img src="images/cal.gif" onclick="javascript:NewCssCal('end','yyyyMMdd','dropdown',true,'24',true)" style="cursor:pointer"/>
  <input type="submit" name="last" value="Submit" />
  <input type="submit" name="last" value="Last 2 Hours">
  <input type="submit" name="last" value="Last 8 Hours">
  </form>
</div>

<div id="<?= $_GET['host'] ?>sysdiv" style="width:<?=  $GRAPHLENGTH ?>px; height:300px;"></div>
<script type="text/javascript">
  g = new Dygraph(

    // containing div
    document.getElementById("<?= $_GET['host'] ?>sysdiv"),
    "Time,<?= $desc ?>\n" +
    "<?= $strresult ?>",
    { fillGraph:true, stackedGraph:true }
  );
function showhide(el) {
  g.setVisibility(el.id, el.checked);
}
</script>
<div id="dow_chart" style="width:<?= $GRAPHLENGTH ?>px;">
    <p><b>Show/Hide: </b>
    <?= $showhide ?>
</div>

<?php
$sqlHost = "select host from perfdata a where topic='LPAR' and metric='PoolIdle' and value>0 and {$begstr} {$endstr} and exists (select * from hosts b where a.host=b.host and b.serial='".$_GET['serial']."') limit 1";
$hn = $conn->query($sqlHost)->fetch();
$hostn = $hn[0];
if ( ! $hostn )
  exit;

$sqlCPUs = "select value,daytime from perfdata where topic='LPAR' and metric='poolCPUs' and host='{$hostn}' and " . $begstr . $endstr;

$result = array();
foreach ( $conn->query($sqlCPUs) as $row )
  $result[$row['daytime']][0] = $row['value'];

$sqlIdle = "select value,daytime from perfdata where topic='LPAR' and metric='PoolIdle' and host='{$hostn}' and " . $begstr . $endstr;

foreach ( $conn->query($sqlIdle) as $row )
  $result[$row['daytime']][1] = $result[$row['daytime']][0] - $row['value'];

ksort($result);
$strresult = '';
foreach ( $result as $daytime => $value )
  $strresult .= $daytime .','.$value[0].','.$value[1].'\n';

echo "<div style='font-size: 18pt; font-weight: bold; text-align: center;'>{$hostn} System's Total CPU Pool Usage</div><br>";

?>
<div id="<?= $_GET['host'] ?>hn.sysdiv" style="width:<?= $GRAPHLENGTH ?>px; height:300px;"></div>
<script type="text/javascript">
  g2 = new Dygraph(

    // containing div
    document.getElementById("<?= $_GET['host'] ?>hn.sysdiv"),
    "Time, Total CPUs, Used CPUs %\n" +
    "<?= $strresult ?>"
  );
</script>

</body>
