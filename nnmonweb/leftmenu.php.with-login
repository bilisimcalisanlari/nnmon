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
<style>
    @import url( css/nnmonmain.css );
    body {
      font-family: Georgia;
      font-size: 11pt;
    }
    .treeMenuDefault {
    }    
    .treeMenuBold {
      font-style: italic;
      font-weight: bold;
    }
</style>
<script src="js/TreeMenu.js" language="JavaScript" type="text/javascript"></script>
</HEAD>
<BODY LANG="tr-TR" DIR="LTR">
<div style='text-align: right;'><a href=logout.php target=_parent>Logout</a></div>
<?php
require_once('inc/TreeMenu.php');

$icon         = 'folder.gif';
$expandedIcon = 'folder-expanded.gif';
$iconp780= 'p780.gif';
$iconAix= 'aix.gif';
$iconCPU= 'cpu.gif';
$iconMem= 'mem.gif';
$iconIO= 'io.gif';
$iconNet= 'net.gif';
$iconDisk= 'disk.gif';
$iconFs= 'fs.gif';
$iconCP= 'cpupool.gif';
$iconAvg= 'avg.gif';
$iconSea= 'sea.gif';

$iconTux= 'tux.gif';

$menu  = new HTML_TreeMenu();

$node1 = new HTML_TreeNode(array('text' => "All Systems", 'link' => "", 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'expanded' => true), array('onclick' => "alert('All Systems'); return false"));

//$sql = "select host,os,serial from hosts order by os,serial";
$sql = "select host,os,serial,b.value,vio from hosts a left join parameter b on a.serial=b.param order by os,b.value,host";

$prevos = '';
$prevserial = '';
$nodeOS = '';
foreach ( $conn->query( $sql ) as $row)
{
  if ( ! $row['os'] )
    continue;
  if ( $prevos != $row['os'] )
  {
    $nodeOS = &$node1->addItem(new HTML_TreeNode(array('text' => $row['os'], 'link' => "", 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'expanded' => true)));
    if ( $row['os'] == 'AIX' )
      $nodeTCPUAvg = &$nodeOS->addItem(new HTML_TreeNode(array('text' => 'Total Usage Averages', 'link' => "allavg.php", 'icon' => $iconp, 'expandedIcon' => $expandedIcon, 'expanded' => true)));
    $prevos = $row['os'];
  }
  if (( $row['os'] == 'AIX' ) && ( $prevserial != $row['serial'] ))
  {
    $nodeText = $row['serial'];
    if ( $row['value'] )
      $nodeText = $row['value'];
    $nodeSerial = &$nodeOS->addItem(new HTML_TreeNode(array('text' => $nodeText, 'link' => "hardware.php?serial=".$row['serial'], 'icon' => $iconp780, 'expandedIcon' => $iconp780, 'expanded' => true))); 
    $nodeCPUAvg = &$nodeSerial->addItem(new HTML_TreeNode(array('text' => 'LPARs\' CPU Avg.', 'link' => "mavg.php?serial=".$row['serial'], 'icon' => $iconAvg, 'expandedIcon' => $iconAvg, 'expanded' => true))); 
    $nodePools = &$nodeSerial->addItem(new HTML_TreeNode(array('text' => 'CPU Pools', 'link' => "pools.php?serial=".$row['serial'], 'icon' => $iconCP, 'expandedIcon' => $iconCP, 'expanded' => true))); 

    $prevserial = $row['serial'];
  }
  if ( $row['os'] == 'AIX' )
  {
    $nodeSystem = &$nodeSerial->addItem(new HTML_TreeNode(array('text' => $row['host'], 'link' => "", 'icon' => $iconAix, 'expandedIcon' => $iconAix, 'expanded' => false))); 
    $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "Physical CPU", 'link' => "pcpu.php?host=" .$row['host'], 'icon' => $iconCPU, 'expandedIcon' => $iconCPU, 'expanded' => false))); 
    $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "Physical CPU Avg.", 'link' => "pcpuavg.php?host=" .$row['host'], 'icon' => $iconAvg, 'expandedIcon' => $iconAvg, 'expanded' => false))); 
    $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "Memory", 'link' => "memory.php?host=" .$row['host'], 'icon' => $iconMem, 'expandedIcon' => $iconMem, 'expanded' => false))); 
    $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "IO Adapter", 'link' => "ioadapter.php?host=".$row['host'], 'icon' => $iconIO, 'expandedIcon' => $iconIO, 'expanded' => false)));
    if ( $row['vio'] == 1 ) 
      $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "SEA Network Stats", 'link' => "sea.php?host=".$row['host'], 'icon' => $iconSea, 'expandedIcon' => $iconSea, 'expanded' => false)));
    
 
  } else
  {
    $nodeSystem = &$nodeOS->addItem(new HTML_TreeNode(array('text' => $row['host'], 'link' => "", 'icon' => $iconTux, 'expandedIcon' => $iconTux, 'expanded' => false)));
    $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "Memory", 'link' => "memory-linux.php?host=".$row['host'], 'icon' => $iconMem, 'expandedIcon' => $iconMem, 'expanded' => false))); 
  }
  $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "CPU Usage", 'link' => "cpu.php?host=".$row['host'], 'icon' => $iconCPU, 'expandedIcon' => $iconCPU, 'expanded' => false))); 
  $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "Network Usage", 'link' => "network.php?host=".$row['host'], 'icon' => $iconNet, 'expandedIcon' => $iconNet, 'expanded' => false))); 
  $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "Network Usage2", 'link' => "network2.php?host=".$row['host'], 'icon' => $iconNet, 'expandedIcon' => $iconNet, 'expanded' => false)));
  $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "Disk Busy", 'link' => "diskbusy.php?host=".$row['host'], 'icon' => $iconDisk, 'expandedIcon' => $iconDisk, 'expanded' => false))); 
  $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "Disk Busy2", 'link' => "diskbusy2.php?host=".$row['host'], 'icon' => $iconDisk, 'expandedIcon' => $iconDisk, 'expanded' => false)));
  $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "Disk Read/Write", 'link' => "diskrw.php?host=".$row['host'], 'icon' => $iconDisk, 'expandedIcon' => $iconDisk, 'expanded' => false))); 
  $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "Disk Read/Write2", 'link' => "diskrw2.php?host=".$row['host'], 'icon' => $iconDisk, 'expandedIcon' => $iconDisk, 'expanded' => false))); 
  $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "Disk Xfer", 'link' => "diskxfer.php?host=".$row['host'], 'icon' => $iconDisk, 'expandedIcon' => $iconDisk, 'expanded' => false)));
  $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "Disk Xfer2", 'link' => "diskxfer2.php?host=".$row['host'], 'icon' => $iconDisk, 'expandedIcon' => $iconDisk, 'expanded' => false))); 
  $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "Disk Service", 'link' => "diskserv.php?host=".$row['host'], 'icon' => $iconDisk, 'expandedIcon' => $iconDisk, 'expanded' => false))); 
  $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "Disk Service2", 'link' => "diskserv2.php?host=".$row['host'], 'icon' => $iconDisk, 'expandedIcon' => $iconDisk, 'expanded' => false)));
  $nodeMenu = $nodeSystem->addItem(new HTML_TreeNode(array('text' => "Filesystems", 'link' => "filesystems.php?host=".$row['host'], 'icon' => $iconFs, 'expandedIcon' => $iconFs, 'expanded' => false))); 

} //foreach  

$menu->addItem($node1);
    
// Create the presentation class
$treeMenu = &new HTML_TreeMenu_DHTML($menu, array('images' => 'img', 'defaultClass' => 'treeMenuDefault', 'linkTarget' => 'rightif'));
//$listBox  = &new HTML_TreeMenu_Listbox($menu, array('linkTarget' => '_self'));
//$listBox  = &new HTML_TreeMenu_Listbox($menu);
//echo "Bugün: " . strtr(date("d F Y, l H.i.s"), $tarih)."<br><br>";
//echo "Today: " . date("d F Y, l")."<br><br>";
?>
<div style="text-align: center; font-size: 24pt; font-weight: bold"><?= $COMPANY ?></div>

<div style="text-align: center;"><?= date("d F Y, l") ?></div><br>
<br>
<script language="JavaScript" type="text/javascript">
<!--
    a = new Date();
    a = a.getTime();
//-->
</script>

<?$treeMenu->printMenu()?><br /><br />
    <?//$listBox->printMenu()?>

<script language="JavaScript" type="text/javascript">
<!--
    b = new Date();
    b = b.getTime();
    
    document.write("Time to render tree: " + ((b - a) / 1000) + "s");
//-->
</script>
<br>
  nnmon Version: <?= $VERSION ?>,<br>Oct 2015, Baris Ozel
</body>
</html>
