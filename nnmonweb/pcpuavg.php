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

$sqlAvg = "select date,avgcpu,avgcpu9_18 from perfsum where host='". $_GET['host'] . "' and date like '________' order by date desc";

$result = array();

foreach ( $conn->query($sqlAvg) as $row )
{
  $result[$row['date']][0] = $row['avgcpu'];
  echo $row['cpuavg'];
  $result[$row['date']][1] = $row['avgcpu9_18'];
}

$strresult = '';
foreach ( $result as $date => $value ) {
  $strresult .= $date .','.$value[0].','.$value[1].'\n';
  $tablestr .= "<tr><td>{$date}</td><td>".round($value[0],2)."</td><td>".round($value[1],2)."</td></tr>";
}

echo "<div style='font-size: 18pt; font-weight: bold; text-align: center;'>{$_GET['host']} Physical CPU Usage Average</div><br>";
?>
<div id="<?= $_GET['host'] ?>sysdiv" style="width:<?= $GRAPHLENGTH ?>px; height:300px;"></div>
<script type="text/javascript">
  g = new Dygraph(

    // containing div
    document.getElementById("<?= $_GET['host'] ?>sysdiv"),
    "Date,PCPU Avg,PCPU 918 Avg\n" +
    "<?= $strresult ?>"
  );
</script>
<br>
<table class='center1' border='1' cellspacing='0' cellpadding='1'>
<tr><th>Zaman</th><th>PCPU Avg.</th><th>PCPU Avg. 9-18</th><tr>
<?= $tablestr ?>
</table>
</body>
</html>
