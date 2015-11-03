<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
   <META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=UTF-8">
   <TITLE>nnmon Login</TITLE>
   <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<style>
    @import url( css/nnmonmain.css );
</style>
</head>
<body>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br>
<form name="frmentry" method="post" action="login.php">
<table class='center1'>
<tr>
<th colspan="2"><div align="center">nnmon - multi nmon</div></th>
</tr>
<tr>
  <?php if (( isset( $_GET['err'] )) && ( $_GET['err']== 'userpass' )): ?>
<th colspan="2"><div align="center">Wrong user name or password.</div></th>
<?php elseif (( isset( $_GET['err'] )) && ( $_GET['err']== 'actdir' )): ?>
<th colspan="2"><div align="center">Cannot access Active Directory.</div></th>
<?php elseif (( isset( $_GET['err'] )) && ( $_GET['err']== 'notalpha' )): ?>
<th colspan="2"><div align="center">User name must be alphanumerical.</div></th>
<?php elseif (( isset( $_GET['err'] )) && ( $_GET['err']== 'userrestricted' )): ?>
<th colspan="2"><div align="center">This user is not allowed on this site. Contact with your system administrator.</div></th>
<?php elseif (( isset( $_GET['err'] )) && ( $_GET['err']== 'nodbconn' )): ?>
<th colspan="2"><div align="center">Can not connect the nnmon database. Contact with your system administrator.</div></th>
<?php endif; ?>
</tr>
<tr>
<td width="136">User Name</td>
<td width="204"><input type="text" name="user" id="user"></td>
</tr>
<tr>
<td>AD Password</td>
<td><input type="password" name="password" id="password"></td>
</tr>
<tr>
<td colspan="2" style='text-align: center'><input type="submit" name="Submit" id="Submit" value="Login"></td>
</tr>
</table>
</form>
</body>
</html>
