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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<title>Stamm Webscript</title>
		<link href="style.css" rel="stylesheet" />
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	</head>
	<body style="background-color: #202732">
		<div style="text-align: center">
			<img src="pics/logo.jpg" alt="Logo" /><br /><br />
			
			<table class="anztable" cellspacing="1" cellpadding="4" style="margin-left:auto; margin-right:auto; text-align:left;">
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

							echo '<tr class="tableinhalt_'.$linecolor.'" align="center"><td colspan="5"><a href="'.$folder.'"><img src="pics/'.$picture.'" alt="Server" /></a></td></tr>';
						}
					?>
				</tbody>
			</table>
		</div>  
	</body>
</html>

