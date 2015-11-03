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
</HEAD>
<BODY LANG="tr-TR" DIR="LTR">
   <form name="frmdmy" action="allavg.php" method="post">
   Please select amount of the data:<br> 
  Days:
<select name="days" id="days" onchange="document.frmdmy.submit()">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
<option value="13">13</option>
<option value="14">14</option>
<option value="15">15</option>
<option value="16">16</option>
<option value="17">17</option>
<option value="18">18</option>
<option value="19">19</option>
<option value="20">20</option>
<option value="21">21</option>
<option value="22">22</option>
<option value="23">23</option>
<option value="24">24</option>
<option value="25">25</option>
<option value="26">26</option>
<option value="27">27</option>
<option value="28">28</option>
<option value="29">29</option>
<option value="30">30</option>
</select>
   Months:
<select name="months" id="months" onchange="document.frmdmy.submit()">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
</select>
   Years:
<select name="years" id="years" onchange="document.frmdmy.submit()">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
</select>
</form>
<script>
   document.getElementById("days").value = "3"
   document.getElementById("months").value = "3"
   document.getElementById("years").value = "1"
</script>

<?php
if ( $_POST['days'] )
  $dayCount = $_POST['days'];
else
  $dayCount = 3;
if ( $_POST['months'] )
  $monthCount = $_POST['months'];
else
  $monthCount = 3;
if ( $_POST['years'] )
  $yearCount = $_POST['years'];
else
  $yearCount = 1;
?>
<script>
   document.getElementById("days").value = "<?= $dayCount ?>"
   document.getElementById("months").value = "<?= $monthCount ?>"
   document.getElementById("years").value = "<?= $yearCount ?>"
</script>

<?php
$lastDay = date("Ymd",strtotime("-".($dayCount +1)." days"));
$lastMonth = date("Ym",strtotime("-".($monthCount +1)." months"));
$lastYear = date("Y",strtotime("-".($yearCount +1)." years"));
$sql = "select * from perfsum where host in (select host from hosts where os='AIX') and (( date like '________' and date > '".$lastDay."' ) or ( date like '______' and date > '".$lastMonth."' ) or ( date like '____' and date > '".$lastYear."' ))";
$perf = array();
foreach ( $conn->query( $sql ) as $row)
{
  $perf[$row['host']][$row['date']][0] = $row['avgcpu'];
  $perf[$row['host']][$row['date']][1] = $row['avgcpu9_18'];
  $perf[$row['host']][$row['date']][2] = $row['avgmem'];
}

//foreach ( $perf as $name => $dateArr )
//{
  //  print $name."#".date("Ymd",strtotime("-".$dayCount." days"))."#";
  //print $dateArr[date("Ymd",strtotime("-2 days"))][0]."<br>";
      //.$avg[0]."#".$avg[1]."#".$avg[2]."<br>";
//}
echo "<br><table border=1 cellspacing='0' cellpadding='1'>";

$sql = "select a.host,a.serial,b.value from hosts a left outer join parameter b on a.serial=b.param where os='AIX'  order by value,host";

$prevHrd = '';

foreach ( $conn->query( $sql ) as $row)
{
  $hrd = $row['value'];
  if ( ! $row['value'] )
    $hrd = $row['serial'];
  $host = $row['host'];
  if ( $hrd != $prevHrd )
  {
    echo "<tr><th colspan='100%'>".$hrd."</th></tr>";
    echo "<tr><th>Host</th>";
    $nextth = '';
    for ( $i = 1; $i < $dayCount + 1; $i++ )
    {
      echo "<th colspan='3'>".date("Ymd",strtotime("-".$i." days"))."</th>";
      $nextth .= "<th>avg<br>cpu</th><th>avg<br>cpu<br>9-18</th><th>avg<br>mem</th>";
    }
    for ( $i = 0; $i < $monthCount; $i++ )
    {
      echo "<th colspan='3'>".date("Ym",strtotime("-".$i." months"))."</th>";
      $nextth .= "<th>avg<br>cpu</th><th>avg<br>cpu<br>9-18</th><th>avg<br>mem</th>";
    }
    for ( $i = 0; $i < $yearCount; $i++ )
    {
      echo "<th colspan='3'>".date("Y",strtotime("-".$i." years"))."</th>";
      $nextth .= "<th>avg<br>cpu</th><th>avg<br>cpu<br>9-18</th><th>avg<br>mem</th>";
    }
    echo "</tr>";
    echo "<tr><th>&nbsp;</th>".$nextth;
    
    echo "</tr>";
    $prevHrd = $hrd;
  }
  echo "<tr><td>${host}</td>";
  for ( $i = 1; $i < $dayCount + 1; $i++ )
  {
    echo "<td>".round( $perf[$host][date("Ymd",strtotime("-".$i." days"))][0], 2 )."</td>";
    echo "<td>".round( $perf[$host][date("Ymd",strtotime("-".$i." days"))][1], 2 )."</td>";
    echo "<td>".round( $perf[$host][date("Ymd",strtotime("-".$i." days"))][2], 0 )."</td>";
  }

for ( $i = 0; $i < $monthCount; $i++ )
  {
    echo "<td>".round( $perf[$host][date("Ym",strtotime("-".$i." months"))][0], 2 )."</td>";
    echo "<td>".round( $perf[$host][date("Ym",strtotime("-".$i." months"))][1], 2 )."</td>";
    echo "<td>".round( $perf[$host][date("Ym",strtotime("-".$i." months"))][2], 0 )."</td>";
  }

for ( $i = 0; $i < $yearCount; $i++ )
  {
    echo "<td>".round( $perf[$host][date("Y",strtotime("-".$i." years"))][0], 2 )."</td>";
    echo "<td>".round( $perf[$host][date("Y",strtotime("-".$i." years"))][1], 2 )."</td>";
    echo "<td>".round( $perf[$host][date("Y",strtotime("-".$i." years"))][2], 0 )."</td>";
  }
  echo "</tr>";

}
?>
</table>
</body>
</html>
