<?php
/**
 *  Stamm Webinterface
 *
 *  The Webinterface for the Stamm Plugin
 *
 *  https://github.com/popoklopsi/Stamm-Webinterface
 *  http://forums.alliedmods.net/showthread.php?p=1338942
 *
 *  @author     Popoklopsi
 *  @copyright  (c) 2012 - David Ordnung
 *  @version    1.1
 */

include 'config.php'
?>

<!doctype html public "-//W3C//DTD HTML 4.0 //EN">

<html>
	<head>
		<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
		<title>Stamm Webscript</title>
		<link href="style.css" rel=stylesheet>
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	</head>
	<body bgColor=#36455a>
		<center>
			<img src="pics/logo.jpg" alt="Logo" border="0">

			<br><br>
			
			<table class="anztable" cellSpacing="1" cellPadding="4">
				<tbody>
					<tr>
						<td class="tablekopf" colspan="5">Servers</td>
					</tr>
					<tr class="tableinhalt_4" >
						<td align="center" colspan="5"><b>Please choose a Server</b></td>
					</tr>
					<?php
						$linecolor = 2;
						
						foreach($server_options as $folder => $picture)
						{
							if ($linecolor == 1) $linecolor = 2;
							else $linecolor = 1;

							echo '<tr class="tableinhalt_'.$linecolor.'" align="center"><td colspan="5"><a href="'.$folder.'"><img src="pics/'.$picture.'" border=0></a></td></tr>';
						}
					?>
				</tbody>
			</table>
		</center>  
	</body>
</html>

