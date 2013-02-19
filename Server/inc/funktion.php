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
	include dirname(__FILE__)."/../config.php";
	require_once("openid.php");

	$link = mysql_connect($servername, $dbusername, $dbpassword) or die("Couldn't make connection.");
	$db = mysql_select_db($dbname, $link) or die("Couldn't select database");

	function validate_login($doLogin)
	{
		global $paypal_enable;
		
		if ($paypal_enable == 0) 
			return false;
		
		if (isset($_SESSION['HTTP_USER_AGENT']))
		{
			if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
			{
				logout();
				
				return false;
			}
		}
		
		if (!isset($_SESSION['stamm_steamid64']))
		{
			if(isset($_COOKIE['stamm_steamid64']) && $_COOKIE['stamm_steamid64'] != "")
			{
				session_regenerate_id();
				
				$_SESSION['stamm_steamid64'] = $_COOKIE['stamm_steamid64'];
				$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);

				return true;
			}
		
		}
		else 
			return true;
		
		$openid = new LightOpenID(curPageURL(false));
		
		if(!$openid->mode)
		{
			if (!$doLogin)
				return false;
				
			$openid->identity = "http://steamcommunity.com/openid";
			$openid->returnUrl = curPageURL(false);
			
			header("Location: ".$openid->authUrl());
			
			return true;
		}
		
		if($openid->validate())
		{
			$communityid = $openid->identity;
			$communityid = SplitId($communityid);
				
			session_regenerate_id (true);
			
			$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
			$_SESSION['stamm_steamid64'] = $communityid;
			
			setcookie("stamm_steamid64", $_SESSION['stamm_steamid64'], time()+60*60*24*10, "/");
			
			return true;
		}

		return false;
	}
	
	function SplitId($communityid)
	{
		$string = explode("id/", $communityid);
		
		return $string[2];
	}

	function protect()
	{
		if (!validate_login(true))
		{
			header("Location: index.php");
			
			exit();
		}
	}
	
	function curPageURL($isPayPal)
	{
		$pageURL = 'http';
		
		if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") 
			$pageURL .= "s";
		
		$pageURL .= "://";
		
		if ($_SERVER["SERVER_PORT"] != "80") 
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		else
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		
		if (!$isPayPal)
			return str_replace("paypal", "index", $pageURL);
		else
			return str_replace("paypal", "paypal_back", $pageURL);
	}

	function logout()
	{
		unset($_SESSION['stamm_steamid64']);
		unset($_SESSION['HTTP_USER_AGENT']);
		
		session_unset();
		session_destroy();
		
		setcookie("stamm_steamid64", '', time()-60*60*24*10, "/");
	}

	function calculate_steamid64($steam_id)
	{
		if (preg_match('/^STEAM_[0-9]:[0-9]:[0-9]{1,}/i', $steam_id))
		{
			$steam_id = str_replace("_", ":", $steam_id);
			list($part_one, $part_two, $part_three, $part_four) = explode(':', $steam_id);
			
			$result = bcadd('76561197960265728', $part_four * 2);
			$result = bcadd($result, $part_two);
			
			return bcadd($result, $part_three);
		}
		else 
			return false;
	}

	function calculate_steamid()
	{
		$commid = $_SESSION['stamm_steamid64'];
		
		if (substr($commid, -1)%2 == 0) 
			$server = 0; 
		else 
			$server = 1;
		 
		$auth = bcsub($commid, '76561197960265728');
		
		if (bccomp($auth, '0') != 1) 
			return "";
		
		$auth = bcsub($auth, $server);
		$auth = bcdiv($auth, 2);
		
		return 'STEAM_0:'.$server.':'.$auth;
	}
	
	function pointsToLevel($points)
	{
		global $level_settings;
		
		$last = 0;
		$level = 0;
		
		foreach($level_settings as $levels => $value)
		{
			if ($points < (int)$value)
				break;
			$last = (int)$value;	
			$level++;
		}
		
		return $level;
	}

	function show_search($tpl, $allsearchcategories, $searchcat, $searchstring, $extracommand)
	{
		global $table;
		
		$tpl->set_file("inhalt4", "templates/inc/search.tpl.htm");
		$tpl->set_block("inhalt4", "scrollsearchblock", "scrollsearchblock_handle");

		for ($x = 0; $x <= count($allsearchcategories['value']) - 1; $x++)
		{
			$tpl->set_var(array(
				"scrollserachcatvalue" => $allsearchcategories['value'][$x],
				"scrollserachcat" => $allsearchcategories['view'][$x]
			));

			$tpl->set_var(array("scrollserachcatselected" => ""));

			$tpl->parse("scrollsearchblock_handle", "scrollsearchblock", true);
		}
		
		if (validate_login(false))
		{
			$steamid = calculate_steamid();
			$sql = "SELECT points ,level FROM $table WHERE steamid='$steamid'";
		  
			$result = mysql_query($sql) OR die(mysql_error());
				  
			if(mysql_num_rows($result)) 
				$showmore = '<input name="ownbutton" type="submit" id="ownbutton" value="&nbsp;&nbsp;&nbsp;&nbsp;Your Ranking&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" />';
			else 
				$showmore = "";
		}
		else $showmore = "";
		
		$tpl->set_var(array(
			"searchstring" => $searchstring,
			"extracommand" => $extracommand,
			"showmore"     => $showmore
		));
	  
		$tpl->set_var(array("search"  => $tpl->parse("out", "inhalt4")));

		return $tpl;
	}

	function search_sql_injection_filter($allsearchcategories, $searchcat, $searchstring, $extracommand)
	{
		global $link;
		$searchstring = mysql_real_escape_string($searchstring, $link);
		
		if ($searchstring <> '')
		{
			$x = 0;
			
			for (;;)
			{
				if ($allsearchcategories['value'][$x]==$searchcat) break;
				$x++;
			}

			if ($extracommand == "")
				return "WHERE ".$allsearchcategories['table'][$x]." LIKE '%$searchstring%'";
			else 
				return "WHERE ".$allsearchcategories['table'][$x]." LIKE '%$searchstring%' AND ".$extracommand."";
		
		}
		else
		{
			if ($extracommand == "") 
				return "";
			else 
				return "WHERE ".$extracommand."";
		}
	}

	function showentrys($tpl ,$text ,$entryname, $start_entry, $all_entrys, $show_clients)
	{

		if (($all_entrys - $start_entry) < $show_clients) 
			$end_entry = $all_entrys;
		else 
			$end_entry = ($start_entry + $show_clients);


		if ($end_entry != 0) $start_entry = $start_entry +1;

		$text = str_replace('%entry%', $entryname, $text);
		$text = str_replace('%start%', $start_entry, $text);
		$text = str_replace('%end%', $end_entry, $text);
		$text = str_replace('%count%', $all_entrys, $text);

		$tpl->set_var(array("show_sites_antry_counts"  => $text));

		return $tpl;
	}

	function site_links($tpl, $all_entrys, $entrys_per_site, $current_site, $section, $searchcat, $searchstring, $extracommand)
	{
		$limit = 10;

		$leftlimit = $current_site - ($limit +1);
		$rightlimit = $current_site + ($limit +1);

		$count_sites = $all_entrys / $entrys_per_site;

		$tpl->set_file("inhalt3", "templates/inc/list_sites.tpl.htm");

		$tpl->set_block("inhalt3", "prew_firs_site", "prew_firs_site_handle");
		$tpl->set_block("inhalt3", "link_prew_firs_site", "link_prew_firs_site_handle");
		$tpl->set_block("inhalt3", "next_last_site", "next_last_site_handle");
		$tpl->set_block("inhalt3", "link_next_last_site", "link_next_last_site_handle");

		$tpl->set_var(array("section"  => $section));

		if ($searchcat != '') $tpl->set_var(array("searchcat" => 'searchcat='.$searchcat.''));
		if ($searchstring != '') $tpl->set_var(array("searchstring" => '&amp;search='.$searchstring.''));
		if ($extracommand != '') $tpl->set_var(array("extracommand" => $extracommand));

		if  ($current_site == 1) {}
		else
		{
			if($current_site > $limit +1)
			{
				$tpl->set_var(array(
					"first_site"  => 1,
					"prev_site" => $current_site - 1
				));
				$tpl->parse("link_prew_firs_site_handle", "link_prew_firs_site", true);
			}
		}

		if  ($current_site == ceil($count_sites)){}
		else
		{
			if($current_site < ($count_sites - $limit))
			{

				$tpl->set_var(array(
					"next_site" => $current_site + 1,
					"last_site" => ceil($count_sites)
				));
				
				$tpl->parse("link_next_last_site_handle", "link_next_last_site", true);
			}
		}

		$tpl->set_block("inhalt3", "linktosite_left", "linktosite_left_handle");
		$tpl->set_block("inhalt3", "current_site_block", "current_site_block_handle");
		$tpl->set_block("inhalt3", "linktosite_right", "linktosite_right_handle");

		if ($count_sites == 0)
		{
			$tpl->set_var(array("current_site"  => '1'));
			$tpl->parse("current_site_block_handle", "current_site_block", true);
		}
		else
		{
			for($a=0; $a < $count_sites; $a++)
			{
				$b = $a + 1;

				if($current_site == $b)
				{
					$tpl->set_var(array("current_site"  => $b));
					$tpl->parse("current_site_block_handle", "current_site_block", true);
				}
				else
				{
					if (($b > $leftlimit) and ($b < $rightlimit))
					{
						$tpl->set_var(array("to_site"  => $b));
						
						if ($b < $current_site) $tpl->parse("linktosite_left_handle", "linktosite_left", true);
						if ($b > $current_site) $tpl->parse("linktosite_right_handle", "linktosite_right", true);
					}
				}

			}
		}
		
		$tpl->set_var(array(
			"switch_sites_links"  => $tpl->parse("out", "inhalt3"),
		));
		
		return $tpl;
	}
?>