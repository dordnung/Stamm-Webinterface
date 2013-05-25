<?php
/**
 * -----------------------------------------------------
 * File        index.php
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


// Check logout
if (isset($_GET['logout']))
{
	// Logout admin
	if ($_GET['logout'] == "admin")
	{
		logout(false);
	}
	else if ($_GET['logout'] == "steam")
	{
		// Logout steam
		logout(true);
	}
}



// SQL class
$sql = new SQL($dbHost, $dbUser, $dbPass, $dbName);



// Check login
$steam = validateSteamLogin(false);

// Admin
if (isset($_POST["login"]) && isset($_POST["user"]) && isset($_POST["pass"]))
{
	$admin = validateAdminLogin(true, $_POST["user"], $_POST["pass"], $sql);
}
else
{
	$admin = validateAdminLogin(false, "", "", $sql);
}



// Get page and search
$default = array_keys($serverOptions);
$currentSite = (isset($_GET["page"])) ? $_GET["page"] : 1;
$searchTyp = (isset($_GET['type'])) ? $_GET['type'] : "";
$search = (isset($_GET['search'])) ? $_GET['search'] : "";

$servername = (isset($_GET['server'])) ? $_GET['server'] : ($serverOptions[$default[0]][0]);
$server = nameToTable($servername);


// Check edit user
if ($admin && isset($_GET["steamid"]) && isset($_GET["value"]))
{
	if ($_GET["value"] != '' && ((int)$_GET["value"] >= 0))
	{
		$sql->query("UPDATE `$server` SET `points`=" .$_GET["value"]. " WHERE `steamid`='" .$_GET["steamid"]. "'");
		
		logAction("Changed points of " .$_GET["steamid"]. " to " .$_GET["value"], $sql);
	}
}




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


// Site to int
if (isset($currentSite)) 
{
	settype($currentSite, "integer");
}
else
{
	$currentSite = 1;
}


// Check valid
if ($currentSite < 1)
{
	$currentSite = 1;
}




// Get Config
$minPoints = $minPoints;
$usersPerPage = $usersPerPage;



// WHERE clause
if ((int)$onlyVips == 1)
{
	$sqlSearch = "WHERE `level` > 0 AND `points` >= $minPoints";
}
else
{
	$sqlSearch = "WHERE `points` >= $minPoints";
}


// Search?
$site = "?";

if (($searchTyp == "name" || $searchTyp == "steamid") && $search != "")
{
	// Escape Search
	$search = $sql->escape($search);
	
	// Append to where clause
	$sqlSearch .= " AND `$searchTyp` LIKE '%" .$search. "%'";
	
	// Site
	$site .= "type=$searchTyp&amp;search=$search&amp;";
}



// Calculate Entrys
$totalEntrys = $sql->getRows($sql->query("SELECT COUNT(`steamid`) FROM `$server` $sqlSearch"));
$totalEntrys = (int)$totalEntrys[0];


// Pages
$totalPages = $totalEntrys / $usersPerPage;



// Check again current site
if ($currentSite > ceil($totalPages))
{
	$currentSite = 1;
}


// Calculate first item
$firstItem = $currentSite * $usersPerPage - $usersPerPage;



 
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Stamm Webinterface</title>
		<link href="style.css" rel="stylesheet" />
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
		<style type="text/css">
		<!--
			<?php
			if ($skin == "dark")
			{
				echo '
					table
					{
						border-color:#000000	
					}
				';
			}
			?>
		-->
		</style>
		<link href="SpryAssets/SpryMenuBarVertical.css" rel="stylesheet" type="text/css" />
		<script src="SpryAssets/SpryMenuBar.js" type="text/javascript"></script>
	</head>
	<body class="<?php echo $skin; ?>">
		<div class="container<?php echo $skin; ?>">
			<div class="header<?php echo $skin; ?>" style="text-align:center">
				<a href="index.php" style="text-decoration:none"><img src="img/logo.png" alt="Logo" /></a>
			</div>
			<div class="content">
				<ul id="MenuBar1" class="MenuBarVertical<?php echo $skin; ?>">
				<li>
					<a class="MenuBarItemSubmenu" href="#">Actions</a>
					<ul>
					<?php
					
					if ($paypalEnable)
					{
						echo '<li><a href="paypal.php?server=' .$servername. '">PayPal</a></li>';
					}
					
					if ($admin)
					{
						echo '<li><a href="index.php' .$site. 'server=' .$servername. '&amp;logout=admin">Admin Logout</a></li>';
					}
					else
					{
						echo '<li><a href="admin.php">Admin Login</a></li>';
					}
					
					?>
					</ul>
				</li>
				<li>
					<a class="MenuBarItemSubmenu" href="#">Skins</a>
					<ul>
						<?php
					
						echo '<li><a href="index.php' .$site. 'server=' .$servername. '&amp;skin=light">Light Skin</a></li>';
						echo '<li><a href="index.php' .$site. 'server=' .$servername. '&amp;skin=dark">Dark Skin</a></li>';
						
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
						echo '<li><a href="index.php' .$site. 'server=' .$value[0]. '">' .$value[0]. '</a></li>';
					}
					?>
					</ul>
				</li>
			</ul>
			<?php
				// Color
				if ($skin == "light")
				{
					echo '<div style="border: 1px solid #CCC; border-left: none; height: 115px; margin-left: 130px; background-color: #EEE">
					<h1>Stamm Webinterface</h1>';
				}
				else
				{
					echo '<div style="border: 1px solid #000000; border-left: none; border-right: none; height: 115px; margin-left: 130px; background-color: #424954">
					<h1 style="color: #FFFFFF">Stamm Webinterface</h1>';
				}
			?>
				<div style="text-align: center; margin-right: auto; margin-left: auto">
					<form action="index.php" method="get">
						<select name="type" id="type">
							<option value="name">Name</option>
							<option value="steamid">SteamID</option>
						</select>
						<input type="text" name="search" id="search" value="<?php echo $search; ?>"/>
						<input type="submit" value="Search" />
					</form>
				</div>
			</div>
			<p>&nbsp;</p>
			<div style="text-align: center; margin-right: auto; margin-left: auto">
				<table border="1" style="width: 95%; text-align: center; margin-right: auto; margin-left: auto; border-style:solid; border-collapse:collapse; border-spacing:3px; margin-bottom:5px"><?php
					// Color
					if ($skin == "light")
					{
						echo '<tr style="background-color: #99BB00">';
					}
					else
					{
						echo '<tr style="background-color: #663000">';
					}?>

					<td style="text-align: center">Player<?php 
			  
					// Sow player count
					if (($totalEntrys - $firstItem) < $usersPerPage) 
					{
						$endEntry = $totalEntrys;
					}
					else
					{ 
						$endEntry = ($firstItem + $usersPerPage);
					}
					
					
					// More than one player?
					if ($endEntry - $firstItem != 1)
					{
						echo 's ';
					}
					else
					{
						echo ' ';
					}
					

					if ($totalEntrys == "0")
					{
						echo $firstItem;
					}
					else
					{
						echo $firstItem+1;
					}
					
					echo " to ";
					echo $endEntry;
					echo " of ";
					echo $totalEntrys;
			  
					?>
						</td>
					</tr>
				</table>
			</div>
			<table border="1" style="width: 95%; margin-right: auto; margin-left: auto; border-style: solid; border-collapse: collapse; border-spacing: 3px;" ><?php

			// server?
			if (isset($_GET['server']))
			{
				// Site
				$site .= "server=$servername&amp;";
			}

			// Get entrys
			$result = $sql->query("SELECT * FROM `$server` $sqlSearch ORDER by `points` DESC LIMIT $firstItem, $usersPerPage");
			  
			// Have any entrys?
			if (!$sql->foundData($result))
			{
				// Empty Result
				// Color
				if ($skin == "light")
				{
					echo '<tr style="background-color: #CC6600">';
				}
				else
				{
					echo '<tr style="background-color: #1E1C1C">';
				}
				echo '
				<td style="text-align: center"><strong>Couldn\'t find any Results</strong></td>
				</tr>';
			}
			else
			{
				$akeys = array_keys($serverOptions[$server][1]);
				$avalues = array_values($serverOptions[$server][1]);

				// Color and rank
				if ($skin == "light")
				{
					$color = "#DDDDDD";
					echo '<tr style="background-color: #CC6600">';
				}
				else
				{
					$color = "#3C3939";
					echo '<tr style="background-color: #1E1C1C">';
				}
				
				$index = ($currentSite - 1) * $usersPerPage + 1;
				$cur = 1;
				
				// Table Layout
				echo '
					<td style="width: 7%; padding-left: 3px; padding-top: 2px; padding-bottom:2px"><strong>Rank</strong></td>
					<td style="width: 32%; padding-left: 3px; padding-top: 2px; padding-bottom:2px"><strong>Name</strong></td>
					<td style="width: 24%; padding-left: 3px; padding-top: 2px; padding-bottom:2px"><strong>SteamID</strong></td>
					<td style="width: 18%; padding-left: 3px; padding-top: 2px; padding-bottom:2px"><strong>Level</strong></td>
					<td style="width: 10%; padding-left: 3px; padding-top: 2px; padding-bottom:2px"><strong>Points</strong></td>
					<td style="width: 9%; padding-left: 3px; padding-top: 2px; padding-bottom:2px"><strong>To Next</strong></td>
				  </tr>';
				
				// Loop through query
				while ($row = $sql->getArray($result))
				{
					// Colors^^
					if ($color == "#DDDDDD")
					{
						$color = "#EEEEEE";
					}
					else if ($color == "#EEEEEE")
					{
						$color = "#DDDDDD";
					}
					
					if ($color == "#333030")
					{
						$color = "#3C3939";
					}
					else if ($color == "#3C3939")
					{
						$color = "#333030";
					}
					
					
					$typeName = "Points";
					$typePoints = $row['points'];
					$left = 0;
					$level = $row['level'];
			
					if ($level == 0)
					{
						$levelName = " - ";
						
						if ($avalues[0] != "")
						{
							$left = $avalues[0] - (int)$typePoints;
						}
						else
						{
							$left = " - "; 
						}
					}
					else if ($level != count($serverOptions[$server][1]))
					{
						$levelName = $akeys[$level-1];
						
						if ($avalues[$level] != "")
						{
							$left = $avalues[$level] - (int)$typePoints;
						}
						else
						{
							$left = " - "; 
						}
					}
					else
					{
						$levelName = $akeys[$level-1];
						$left = " - ";
					}
					
					$name = str_replace("{", "", $row['name']);
					$name = str_replace("}", "", $name);
					$name = str_replace("<", "&lt;", $name);
					$name = str_replace("&", "&amp;", $name);
					$name = substr($name, 0, 22);
					
					$edit = "";
					
					if ($admin)
					{
						if (isset($_GET["edit"]) && ((int)$_GET["edit"] == $cur))
						{
							$edit = '<input style="width: 75%" name="useredit" id="useredit" type="number" min="0" value="' .$typePoints. '" /><a href="#" onclick="editUser(\'index.php' .$site. 'page=' .$currentSite. '\', \'' .$row['steamid']. '\')"><img src="img/check.ico" width="16" height="16" /></a>';
						}
						else
						{
							$edit = "$typePoints <a href=\"index.php" .$site. "page=$currentSite&amp;edit=$cur\"><img alt=\"Edit\" src=\"img/edit.ico\" width=\"16\" height=\"16\" /></a>";			}
					}
					else
					{
						$edit = $typePoints;
					}
					
					
					if ($steam && $row['steamid'] == calculateSteamid())
					{
						if ($skin == "light")
						{
							echo '<tr style="background-color: #CC6600">';
						}
						else
						{
							echo '<tr style="background-color: #1E1C1C">';
						}
					}
					else
					{
						echo '<tr style="background-color: ' .$color. '">';
					}
					
					echo '
							<td style="padding-left:3px; padding-top:2px; padding-bottom:2px">' .$index. '</td>
							<td style="padding-left:3px; padding-top:2px; padding-bottom:2px"><a class="link1' .$skin. '" href="http://steamcommunity.com/profiles/' .calculateSteamid64($row['steamid']). '" target="_blank">' .$name. '</a></td>
							<td style="padding-left:3px; padding-top:2px; padding-bottom:2px"><a class="link1' .$skin. '" href="http://steamcommunity.com/profiles/' .calculateSteamid64($row['steamid']). '" target="_blank">' .$row['steamid']. '</a></td>
							<td style="padding-left:3px; padding-top:2px; padding-bottom:2px">' .$levelName. '</td>
							<td style="padding-left:3px; padding-top:2px; padding-bottom:2px">' .$edit. '</td>
							<td style="padding-left:3px; padding-top:2px; padding-bottom:2px">' .$left. '</td>
						</tr>';
						
					$index++;
					$cur++;
				}
			}
			?>
			</table>

			<table border="1" style="width: 95%; text-align: center; margin-right: auto; margin-left: auto; border-style: solid; border-collapse: collapse; border-spacing: 3px; margin-top:5px" ><?php
			
			// Color
			if ($skin == "light")
			{
				echo '<tr style="background-color: #99BB00">';
			}
			else
			{
				echo '<tr style="background-color: #663000">';
			}
			
			?>
				<td style="text-align: center"><span class="page_switch_links<?php echo $skin; ?>">Go to page: <?php 
		
				$leftLimit = $currentSite - 11;
				$rightLimit = $currentSite + 11;
			
			
				// To we need to append << and < ?
				if ($currentSite > 11)
				{
					echo '&nbsp;<a class="page_switch' .$skin. '" href="index.php' .$site. 'page=1">&laquo;&laquo;</a>&nbsp;<a class="page_switch' .$skin. '" href="index.php' .$site. 'page=' .($currentSite-1). '">&laquo;</a>&nbsp;';
				}
			
				// Only one page?
				if ($totalPages <= 1)
				{
					echo '&nbsp;<b>1</b>';
				}
				else
				{
					// Loop through all pages
					for ($i=0; $i < $totalPages; $i++)
					{
						$current = $i + 1;

						// Check if current page
						if ($currentSite == $current)
						{
							echo '&nbsp;<b>' .$current. '</b>';
						}
						else
						{
							if (($current > $leftLimit) && ($current < $rightLimit))
							{
								echo '&nbsp;<a class="page_switch' .$skin. '" href="index.php' .$site. 'page=' .$current. '">' .$current. '</a>';
							}
						}
					}
				}
				
				
				// To we need to append >> and < ?
				if ($currentSite < ($totalPages - 10))
				{
					echo '&nbsp;<a class="page_switch' .$skin. '" href="index.php' .$site. 'page=' .($currentSite+1). '">&raquo;</a>&nbsp;<a class="page_switch' .$skin. '" href="index.php' .$site. 'page=' .ceil($totalPages). '">&raquo;&raquo;</a>';
				}

				?>
						</span>
					</td>
				</tr>
			</table>
			<p>&nbsp;</p>
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