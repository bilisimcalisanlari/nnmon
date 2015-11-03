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

$sqlPoolIDs = "select distinct(value),host from perfdata where topic='LPAR' and metric='Pool_id' and host in (select host from hosts where serial='".$_GET['serial']."') and " . $begstr . $endstr . " order by value";

$prevpoolid = -1;
$ind = -1;
$hosts = array();
foreach ($conn->query($sqlPoolIDs) as $row)
{
  $poolid = $row['value'];
  if ($poolid != $prevpoolid)
  {
    $ind += 1;
    $prevpoolid = $poolid;
    $hosts[$ind][0] = $poolid;
  }
  $hosts[$ind][1] .= ",'" . $row['host']."' ";
}

foreach ($hosts as $host)
{
  $host[1] = preg_replace('/^,/', '', $host[1]);
  print $host[0]."=>".$host[1]."<br>";
  $sqlHost = "select host from perfdata a where topic='LPAR' and metric='PoolIdle' and value>0 and {$begstr} {$endstr} and host in ({$host[1]}) limit 1";
  $hn = $conn->query($sqlHost)->fetch();
  //  print $hn[0]."<br>";
  
  $sqlCPUs = "select value,daytime from perfdata where topic='LPAR' and metric='poolCPUs' and host='{$hn[0]}' and " . $begstr . $endstr;

  $result = array();
  foreach ( $conn->query($sqlCPUs) as $row )
    $result[$row['daytime']][0] = $row['value'];

  $sqlIdle = "select value,daytime from perfdata where topic='LPAR' and metric='PoolIdle' and host='{$hn[0]}' and " . $begstr . $endstr;
  
  foreach ( $conn->query($sqlIdle) as $row )
    $result[$row['daytime']][1] = $result[$row['daytime']][0] - $row['value'];
  
  ksort($result);
  $strresult = '';
  foreach ( $result as $daytime => $value )
    $strresult .= $daytime .','.$value[0].','.$value[1].'\n';
  
  echo "<div style='font-size: 18pt; font-weight: bold; text-align: center;'>Pool ID = {$host[0]} {$hn[0]} System's Total CPU Pool Usage</div><br>";
  
?>
<div id="<?= $hn[0] ?>hn.sysdiv" style="width:<?= $GRAPHLENGTH ?>px; height:300px;"></div>
<script type="text/javascript">
  g2 = new Dygraph(

    // containing div
    document.getElementById("<?= $hn[0] ?>hn.sysdiv"),
    "Time, Total CPUs, Used CPUs %\n" +
    "<?= $strresult ?>"
  );
</script>
<?php
    }
?>
</body>
