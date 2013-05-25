<?php
/**
 * -----------------------------------------------------
 * File        function.php
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
 


// Includes
include_once("config.php");
include_once("sql.php");
require_once("openid.php");



// Check for install file
function checkInstall()
{
	// File exist?
	if (file_exists("install.php"))
	{
		// Die
		die("Attention: Found install.php! Delete this file if you already installed Stamm Webinterface!");
	}
}



// Servername to Tablename
function nameToTable($name)
{
	global $serverOptions;
	
	
	// Loop trough servers
	foreach($serverOptions as $key => $value)
	{
		// Found server
		if ($value[0] == $name)
		{
			return $key;
		}
	}
	
	
	// Found nothing
	$default = array_keys($serverOptions);
	
	// Return first table
	return $default[0];
}



// Get Skin
function getSkin()
{
	// Security check
	if (isset($_SESSION['HTTP_USER_AGENT']))
	{
		if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
		{
			return "light";
		}
	}
	
	
	// Session not set?
	if (!isset($_SESSION['stamm_skin']))
	{
		// Maybe cookie set?
		if (isset($_COOKIE['stamm_skin']) && ($_COOKIE['stamm_skin'] == "dark" || $_COOKIE['stamm_skin'] == "light"))
		{
			// Regenerate session
			session_regenerate_id();
			
			// Set Session
			$_SESSION['stamm_skin'] = $_COOKIE['stamm_skin'];
			$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
			
			// Extend cookie
			setcookie("stamm_skin", $_SESSION['stamm_skin'], time() + 60 * 60 * 24 * 10, "/");

			
			return $_SESSION['stamm_skin'];
		}
		else
		{
			// Default
			return "light";
		}
	
	}
	else
	{
		// Extend cookie
		setcookie("stamm_skin", $_SESSION['stamm_skin'], time() + 60 * 60 * 24 * 10, "/");

		return $_SESSION['stamm_skin'];
	}
}



// Set new skin
function setSkin($skin)
{
	// Set Session
	$_SESSION['stamm_skin'] = $skin;
	$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);


	// Set cookie
	setcookie("stamm_skin", $skin, time() + 60 * 60 * 24 * 10, "/");
}



// Validate login
function validateSteamLogin($doLogin)
{
	global $paypalEnable;
	
	
	// Paypal enabled?
	if (!$paypalEnable)
	{
		return false;
	}
	
	
	// Security check
	if (isset($_SESSION['HTTP_USER_AGENT']))
	{
		if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
		{
			logout(true);
			
			return false;
		}
	}
	
	
	// Session not set?
	if (!isset($_SESSION['stamm_steamid64']))
	{
		// Maybe cookie set?
		if (isset($_COOKIE['stamm_steamid64']) && $_COOKIE['stamm_steamid64'] != "")
		{
			// Regenerate session
			session_regenerate_id();
			
			// Set Session
			$_SESSION['stamm_steamid64'] = $_COOKIE['stamm_steamid64'];
			$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
			
			// Extend cookie
			setcookie("stamm_steamid64", $_SESSION['stamm_steamid64'], time() + 60 * 60 * 24 * 10, "/");

			return true;
		}
	
	}
	else
	{
		// Extend cookie
		setcookie("stamm_steamid64", $_SESSION['stamm_steamid64'], time() + 60 * 60 * 24 * 10, "/");
			
		return true;
	}
	
	
	// Generate OpenID
	$openid = new LightOpenID(curPageURL(false));
	
	
	// Need login
	if (!$openid->mode)
	{
		// Want login?
		if (!$doLogin)
		{
			return false;
		}
		
		
		// Set openid
		$openid->identity = "http://steamcommunity.com/openid";
		$openid->returnUrl = curPageURL(false);
		
		// Go to login
		header("Location: ".$openid->authUrl());
		
		return false;
	}
	
	
	// Validate mode
	if ($openid->validate())
	{
		// Get CommunityID
		$communityid = $openid->identity;
		$communityid = explode("id/", $communityid);
		$communityid = $communityid[2];

		
		// Get session
		session_regenerate_id();
		
		// Set Login
		$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
		$_SESSION['stamm_steamid64'] = $communityid;
		
		// Cookie
		setcookie("stamm_steamid64", $_SESSION['stamm_steamid64'], time() + 60 * 60 * 24 * 10, "/");
		
		return true;
	}

	// Nop
	return false;
}



// Validate Admin 
function validateAdminLogin($doLogin, $user, $pw, $sql)
{
	// Security check
	if (isset($_SESSION['HTTP_USER_AGENT']))
	{
		if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT']))
		{
			logout(false);
			
			return false;
		}
	}
	
	
	// Session set?
	if (!isset($_SESSION['stamm_user']) || !isset($_SESSION['stamm_pass']))
	{
		// Maybe cookie set
		if (isset($_COOKIE['stamm_user']) && isset($_COOKIE['stamm_pass']))
		{
			session_regenerate_id();
			
			$_SESSION['stamm_user'] = $_COOKIE['stamm_user'];
			$_SESSION['stamm_pass'] = $_COOKIE['stamm_pass'];
			$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
			
			
			// Check login
			$result = $sql->query("SELECT * FROM `stamm_interface_users` WHERE `username` = '" .$_SESSION['stamm_user']. "' AND `password` = '" .$_SESSION['stamm_pass']. "' LIMIT 1");
			
			// Login successfull
			if ($result && $sql->foundData($result))
			{
				// Extend Cookie
				setcookie("stamm_user", $_SESSION['stamm_user'], time() + 60 * 60 * 24 * 10, "/");
				setcookie("stamm_pass", $_SESSION['stamm_pass'], time() + 60 * 60 * 24 * 10, "/");
				
				return true;
			}
			else
			{
				logout(false);
				
				return false;
			}
		}
	
	}
	else
	{
		// Check login
		$result = $sql->query("SELECT * FROM `stamm_interface_users` WHERE `username` = '" .$_SESSION['stamm_user']. "' AND `password` = '" .$_SESSION['stamm_pass']. "' LIMIT 1");
			
		// Login successfull
		if ($result && $sql->foundData($result))
		{
			// Extend Cookie
			setcookie("stamm_user", $_SESSION['stamm_user'], time() + 60 * 60 * 24 * 10, "/");
			setcookie("stamm_pass", $_SESSION['stamm_pass'], time() + 60 * 60 * 24 * 10, "/");
				
			return true;
		}
		else
		{
			logout(false);
			
			return false;
		}
	}
	
	
	// Want login?
	if (!$doLogin)
	{
		return false;
	}
	

	// Check login
	$result = $sql->query("SELECT * FROM `stamm_interface_users` WHERE `username` = '$user' AND `password` = MD5('$pw') LIMIT 1");
	
	// Login sucessfull
	if ($result && $sql->foundData($result))
	{
		// Set session
		session_regenerate_id();
		
		// Set Login
		$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
		$_SESSION['stamm_user'] = $user;
		$_SESSION['stamm_pass'] = md5($pw);
		
		// Extend Cookie
		setcookie("stamm_user", $_SESSION['stamm_user'], time() + 60 * 60 * 24 * 10, "/");
		setcookie("stamm_pass", $_SESSION['stamm_pass'], time() + 60 * 60 * 24 * 10, "/");
			
		return true;
	}
	else
	{
		logout(false);
		
		return false;
	}
}




// Get the current page
function curPageURL($isPayPal)
{
	// http
	$pageURL = 'http';
	
	
	// Maybe it's https?
	if (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") 
	{
		$pageURL .= "s";
	}
	
	
	// Append ://
	$pageURL .= "://";
	
	
	// Need to add port?
	if ($_SERVER["SERVER_PORT"] != "80") 
	{
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	}
	else
	{
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	
	// Split at ?
	$split = explode("?", $pageURL);
	$pageURL = $split[0];
	

	// To index or to paypal back?
	if (!$isPayPal)
	{
		return $pageURL;
	}

	return str_replace("paypal", "paypal_back", $pageURL);

}



// Logout
function logout($steam)
{
	// Logout steam
	if ($steam)
	{
		// Unset steamid cookie
		unset($_SESSION['stamm_steamid64']);
		unset($_COOKIE['stamm_steamid64']);
		
		setcookie("stamm_steamid64", '', time()-1, "/");
	}
	else
	{
		// Logout admin
		// Unset user and pass session
		unset($_SESSION['stamm_user']);
		unset($_SESSION['stamm_pass']);
		
		unset($_COOKIE['stamm_user']);
		unset($_COOKIE['stamm_pass']);
		
		setcookie("stamm_user", '', time()-1, "/");
		setcookie("stamm_pass", '', time()-1, "/");
	}
}



// Calculate communityid
function calculateSteamid64($steamID)
{
	// Valid?
	if (preg_match('/^STEAM_[0-9]:[0-9]:[0-9]{1,}/i', $steamID))
	{
		// Convert
		$steamID = str_replace("_", ":", $steamID);
		list($part_one, $part_two, $part_three, $part_four) = explode(':', $steamID);
		
		$result = bcadd('76561197960265728', $part_four * 2);
		
		return bcadd($result, $part_three);
	}
	else 
	{
		return false;
	}
}



// Steamid out of communityid
function calculateSteamid()
{
	$commid = $_SESSION['stamm_steamid64'];
	
	if (substr($commid, -1) % 2 == 0) 
	{
		$server = 0; 
	}
	else
	{ 
		$server = 1;
	}
	
	$auth = bcsub($commid, '76561197960265728');
	
	if (bccomp($auth, '0') != 1)
	{
		return "";
	}
	
	$auth = bcsub($auth, $server);
	$auth = bcdiv($auth, 2);
	
	return 'STEAM_0:'.$server.':'.$auth;
}




// Log a action
function logAction($action, $sql)
{
	$sql->query("INSERT INTO `stamm_interface_log` (`action`) VALUES ('$action')");
}