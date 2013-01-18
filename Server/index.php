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

	$current_site = (isset($_GET["page"])) ? $_GET["page"]:'1';
	$searchcat = (isset($_POST['serachcat'])) ? $_POST['serachcat']:'';
	$searchstring = (isset($_POST['searchstring'])) ? $_POST['searchstring']:'';
	
	if ($searchcat == '') $searchcat = (isset($_GET['searchcat'])) ? $_GET['searchcat']:'';
	if ($searchstring == '') $searchstring = (isset($_GET['search'])) ? $_GET['search']:'';

	if(isset($current_site)) settype($current_site, "integer");
	if ($current_site < 1) $current_site = 1;

	include "config.php";
	include "inc/template.inc.php";
	include "inc/funktion.php";
	
	$akeys = array_keys($level_settings);
	$avalues = array_values($level_settings);
	
	if (isset($_POST['ownbutton']) || isset($_GET['ownbutton']))
	{
		if (validate())
		{
			if ($vips == 0) $sql = "SELECT * FROM ".$table." WHERE points >= ".$points_min." ORDER by points DESC";
			else $sql = "SELECT * FROM ".$table." WHERE level > 0 AND points >= ".$points_min." ORDER by points DESC";
			
			$result = mysql_query($sql) or die(mysql_error());

			if(mysql_num_rows($result))
			{  
				$index = 1;
				$steamid = calculate_steamid();
				
				while($row = mysql_fetch_assoc($result))
				{
					if ($row['steamid'] == $steamid)
					{
						$current_site = ceil($index / $show_users);
						break;
					}
					
					$index++;
				}
				$searchstring = '';
				$searchcat = '';
			}
		}
	}

	$tpl = new Template();
	
	$tpl->set_file("header",       "templates/header.tpl.htm");
	$tpl->set_file("hauptseite",   "templates/mainpage.tpl.htm");
	$tpl->set_file("footer",       "templates/footer.tpl.htm");

	$tpl->set_block("hauptseite", "clientsanzeigeempty", "clientsanzeigeempty_handle");
	$tpl->set_block("hauptseite", "clientsanzeige", "clientsanzeige_handle");

	$allsearchcategories = array();
	$allsearchcategories['value'] = array('name', 'steamid');
	$allsearchcategories['view'] = array("Name", "SteamID");
	$allsearchcategories['table'] = array('name', 'SteamID');

	$tpl = show_search($tpl, $allsearchcategories, $searchcat, $searchstring, "");

	if ($vips == 0) 
		$searcquery = search_sql_injection_filter ($allsearchcategories, $searchcat, $searchstring, "points >= $points_min");
	else 
		$searcquery = search_sql_injection_filter ($allsearchcategories, $searchcat, $searchstring, "level > 0 AND points >= $points_min");

	$all_entrys = mysql_num_rows(mysql_query("SELECT * FROM ".$table." ".$searcquery));
	
	if ($current_site > ceil($all_entrys / $show_users)) 
		$current_site = 1;
	$start_entry = $current_site * $show_users - $show_users;

	$tpl = showentrys($tpl, "Show %entry% <b>%start%</b> to <b>%end%</b> from <b>%count%</b>" ,"Player", $start_entry, $all_entrys, $show_users);
	$tpl = site_links($tpl, $all_entrys, $show_users, $current_site, $section, $searchcat, $searchstring, '');

	$sql = "SELECT * FROM ".$table." ".$searcquery." ORDER by points DESC LIMIT ".$start_entry.",".$show_users."";
	   
	$result = mysql_query($sql) or die(mysql_error());

	if (mysql_num_rows($result))
	{  
		$linecolor = 2;
		$index = ($current_site - 1) * 20 + 1;
		
		while ($row = mysql_fetch_assoc($result))
		{
			if ($linecolor == 1) 
				$linecolor = 2;
			else 
				$linecolor = 1;
			
			$typename = "Points";
			$typepoints = $row['points'];
			$left = 0;

			if ((int)$row['level'] == 0)
			{
				$levelname = "No VIP";
				$left = $avalues[0] - (int)$typepoints;
			}
			else if ((int)$row['level'] != count($level_settings))
			{
				$levelname = "".$akeys[(int)$row['level']-1]." VIP";
				$left = $avalues[(int)$row['level']] - (int)$typepoints;
			}
			else
			{
				$levelname = "".$akeys[(int)$row['level']-1]." VIP";
				$left = " - ";
			}
	
			$name = str_replace("{", "", $row['name']);
			$name = str_replace("}", "", $name);
			$name = str_replace("<", "&lt;", $name);
			$name = str_replace("&", "&amp;", $name);
			
			if ($paypal_enable == 1)
			{
				if (validate())
				{
					$steamid = calculate_steamid();
				
					if ($row['steamid'] == $steamid) $linecolor = 4;
					
					if ($use_overview)
					{
						$login = '
							<tr class="tableinhalt_4" >
							<td align="center" colspan="3"><a class="link2" href="paypal.php">Buy Stamm Points</a></td>
							<td align="center" colspan="2"><a class="link2" href="logout.php">Logout</a></td>
							<td align="center" colspan="1"><a class="link2" href="../index.php">Back to Servers</a></td>
							</tr>';
					}
					else
					{
						$login = '
							<tr class="tableinhalt_4" >
							<td align="center" colspan="3"><a class="link2" href="paypal.php">Buy Stamm Points</a></td>
							<td align="center" colspan="3"><a class="link2" href="logout.php">Logout</a></td>
							</tr>';
					}
				} 
				else 
				{
					$url = genUrl(curPageURL());
					$notice = 'Login, to buy Stamm Points via PayPal!';
					
					if ($use_overview)
					{
						$login = '
							<tr class="tableinhalt_4" >
							<td align="center" colspan="2"><a class="link2" href="'.$url.'"><img src="http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_small.png" alt="Login"/></a></td>
							<td align="center" colspan="2"><b>Login, to buy Stamm Points</b></td>
							<td align="center" colspan="2"><a class="link2" href="../index.php">Back to Servers</a></td>
							</tr>';
					}
					else
					{
						$login = '
							<tr class="tableinhalt_4" >
							<td align="center" colspan="3"><a class="link2" href="'.$url.'"><img src="http://cdn.steamcommunity.com/public/images/signinthroughsteam/sits_small.png" alt="Login"/></a></td>
							<td align="center" colspan="3"><b>Login, to buy Stamm Points</b></td>
							</tr>';
					}
				}
			} 
			else
			{
				if ($use_overview)
				{
					$login = '
					<tr class="tableinhalt_4" >
							<td align="center" colspan="6"><a class="link2" href="../index.php">Back to Servers</a></td>
							</tr>';
				}
				else
				{
					$login = '
					<tr class="tableinhalt_4" >
							<td align="center" colspan="6"><b>Stamm VIP\'s</b></td>
							</tr>';
				}
			}
			
			$tpl->set_var(array(
				"rank"			  => $index,
				"steamid64"		  => calculate_steamid64($row['steamid']),
				"linecolor"		  => $linecolor,
				"steamid"         => $row['steamid'],
				"name"        	  => $name,
				"points"		  => $typepoints,
				"levelname"       => $levelname,
				"left"			  => $left,
				"login"			  => $login
			));
			$tpl->parse("clientsanzeige_handle", "clientsanzeige", true);
			
			$index++;
		}
	}
	else 
		$tpl->parse("clientsanzeigeempty_handle", "clientsanzeigeempty", true);
	  
	$tpl->set_var(array(
		"header"       => $tpl->parse("out", "header"),
		"footer"       => $tpl->parse("out", "footer"),
	));
	  
	$tpl->parse("out", "hauptseite");
	$tpl->p("out");  
?>