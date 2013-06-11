<?php
/**
 * -----------------------------------------------------
 * File        paypal.php
 * Authors     David <popoklopsi> Ordnung
 * Version     1.2
 * License     GPLv3
 * Web         http://popoklopsi.de
 * -----------------------------------------------------
 * 
 * Stamm Webscript
 * Copyright (C) 2012-2013 David <popoklopsi> Ordnung
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 */


// Start session
session_start();


// Config and functions
include_once("inc/config.php");
include_once("inc/sql.php");
include_once("inc/function.php");


// Check install
checkInstall();



// SQL class
$sql = new SQL($dbHost, $dbUser, $dbPass, $dbName);


// Check login
$steam = validateSteamLogin(true);

// Now allowed to be here
if (!$steam)
{
	exit();
}
	


// Steamid
$steamid = calculateSteamid();

// Get server
$default = array_keys($serverOptions);
$servername = (isset($_GET['server'])) ? $_GET['server'] : ($serverOptions[$default[0]][0]);
$server = nameToTable($servername);



// Change skin?
if (isset($_GET['skin']))
{
	if ($_GET['skin'] == "dark" || $_GET['skin'] == "light")
	{
		setSkin($_GET['skin']);
	}
}


// Get Skin
$skin = getSkin();



 
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Stamm PayPal</title>
		<link href="style.css" rel="stylesheet" />
		<link href="SpryAssets/SpryMenuBarVertical.css" rel="stylesheet" type="text/css" />
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
		<script src="SpryAssets/SpryMenuBar.js" type="text/javascript"></script>
	</head>
	<body class="<?php echo $skin; ?>">
		<div class="container<?php echo $skin; ?>">
			<div class="header<?php echo $skin; ?>" style="text-align: center">
				<a href="index.php" style="text-decoration: none"><img src="img/logo.png" alt="Logo" /></a>
			</div>
			<div class="content">
				<ul id="MenuBar1" class="MenuBarVertical<?php echo $skin; ?>">
					<li>
						<a class="MenuBarItemSubmenu" href="#">Actions</a>
						<ul>
							<?php
							if ($paypalEnable)
							{
								echo '<li><a href="index.php?server=' .$servername. '">Home</a></li>';
							}
							
							echo '<li><a href="index.php?server=' .$servername. '&amp;logout=steam">Logout</a></li>';
							?>
						</ul>
					</li>
					<li>
						<a class="MenuBarItemSubmenu" href="#">Skins</a>
						<ul>
							<?php
							
							echo '<li><a href="paypal.php?server=' .$servername. '&amp;skin=light">Light Skin</a></li>';
							echo '<li><a href="paypal.php?server=' .$servername. '&amp;skin=dark">Dark Skin</a></li>';
							
							?>
						</ul>
					</li>
					<li>
						<a class="MenuBarItemSubmenu" href="#">Servers</a>
						<ul>
							<?php
						
							// Add all server
							foreach($serverOptions as $key => $value)
							{
								echo '<li><a href="paypal.php?server=' .$value[0]. '">' .$value[0]. '</a></li>';
							}
							
							?>
						</ul>
					</li>
				</ul>
				<?php
				
				// Color
				if ($skin == "light")
				{
					echo '
					<div style="border: 1px solid #CCC; border-left: none; height: 115px; margin-left: 130px; background-color: #EEE">
						<h1>Stamm Webinterface PayPal</h1>';
				}
				else
				{
					echo '<div style="border: 1px solid #000000; border-left: none; border-right: none; height: 115px; margin-left: 130px; background-color: #424954">
						<h1 style="color: #FFFFFF">Stamm Webinterface PayPal</h1>';
				}
				
				echo '<div style="text-align: center"><strong>Your Steamid:</strong> ' .$steamid. '</div>';
				
				?>
				</div>
				<br />
				<?php
				
				$akeys = array_keys($serverOptions[$server][1]);
				$avalues = array_values($serverOptions[$server][1]);
					
					
				echo '<div style="text-align: center; margin-right: auto; margin-left: auto; width: 95%;">';
				
				
				$result = $sql->query("SELECT `points` FROM `$server` WHERE `steamid`='$steamid'");
				
				if ($sql->foundData($result))
				{
					list($typepoints) = $sql->getRows($result);
					
					$left = 0;
					$levelname = $akeys[0];
					$level = pointsToLevel((int)$typepoints, $server);
					
					if ($level == 0)
					{	
						if ($avalues[0] != "")
						{
							$left = $avalues[0] - (int)$typepoints;
						}
						else
						{
							$left = " - "; 
						}
					}
					else if ($level != count($serverOptions[$server][1]))
					{
						$levelname = $akeys[$level];
						
						if ($avalues[$level] != "")
						{
							$left = $avalues[$level] - (int)$typepoints;
						}
						else
						{
							$left = " - "; 
						}
					}
					else
					{
						$left = " - ";
					}
					
					
					echo "<h2>You have $typepoints Stamm Points</h2>";
					
					
					
					if ($level != count($serverOptions[$server][1]) && $avalues[$level-1] != "") 
					{
						echo "<h2>You need $left Stamm Points to become $levelname VIP</h2>";
					}
					else if ($avalues[$level-1] == "")
					{
						echo "<h2>You are a Special VIP</h2>";
					}
					else
					{
						echo "<h2>You are already the highest VIP</h2>";
					}
					
					echo '
					<br />
					<form action="https://ipnpb.paypal.com/cgi-bin/webscr" method="post" target="theWin" onsubmit="window.open(\'\',\'theWin\',\'height=800px, width=1000px\');">
						<fieldset>
								<input type="hidden" name="cmd" value="_xclick" />
								<input type="hidden" name="notify_url" value="' .curPageURL(true). '" />
								<input type="hidden" name="item_name" value="Stamm Points" /> 
								<input type="hidden" name="business" value="' .$paypalEmail. '" />
								<input type="hidden" name="rm" value="2" />
								<input type="hidden" name="lc" value="' .$paypalLanguage. '" />
								<input type="hidden" name="custom" value="'. $steamid. ';' .$servername. '" />
								<input type="hidden" name="no_shipping" value="1" />
								<input type="hidden" name="address_override" value="1" />
								<input type="hidden" name="on0" value="Stamm Points" />
								<h1 style="text-decoration: underline">Buy Stamm Points:</h1>
								<div style="margin-left: auto; margin-right: auto; text-align: center;">
									<table style="margin-left: auto; margin-right: auto; text-align:center">
										<tr>
											<td>
												<select name="os0">';

												foreach($paypalPrices as $points => $value)
												{
													echo "<option value=\"$points\">$points Points: $value $paypalCountry</option>";
												}
												
					echo '
												</select> 
											</td>
										</tr>
									</table>
								</div>';
										
								$index = 0;
								
								foreach($paypalPrices as $points => $value)
								{
									echo '<input type="hidden" name="option_select' .$index. '" value="' .$points. '" />';
									echo '<input type="hidden" name="option_amount' .$index. '" value="' .$value. '" />';
									
									$index++;
								}
				
					echo '
								<input type="hidden" name="currency_code" value="' .$paypalCountry. '" />
								<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" name="submit" alt="PayPal - The safer, easier way to pay online!" />
						</fieldset>
					</form>
					<p>&nbsp;</p>
					';
				}
				else
				{
					echo '<h1>You are not registered on this Server</h1>
						<h1>Please visit it first!</h1>';
				}
					
				?>   
					</div>
				</div>
				<div class="footer<?php echo $skin; ?>">
					<div style="text-align: right">Stamm Webinterface v1.2 by Popoklopsi&nbsp;
				</div>
			</div>
		</div>
		<script type="text/javascript">
			var MenuBar1 = new Spry.Widget.MenuBar("MenuBar1", {imgRight:"SpryAssets/SpryMenuBarRightHover.gif"});

			function editUser(page, steamid)
			{
				window.location = page + "&steamid=" + steamid + "&value=" + document.getElementById("useredit").value;
			}
		</script>
	</body>
</html>