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
 
	include 'inc/funktion.php';
	include 'config.php';
	include "inc/template.inc.php";
	
	$akeys = array_keys($level_settings);
	$avalues = array_values($level_settings);
	
	if ($stamm_type == "time")
	{
		foreach($level_settings as $level => $points)
		{
			$level_settings[$level] = $level_settings[$level] * 60;
		}
	}

	protect();

	$tpl = new Template();
	$tpl->set_file("header",       "templates/header.tpl.htm");
	$tpl->set_file("paypal",   	   "templates/paypal.tpl.htm");
	$tpl->set_file("footer",       "templates/footer.tpl.htm");

	$tpl->set_block("paypal", "paydetails", "paydetails_handle");
	$tpl->set_block("paypal", "paydetailsc", "paydetailsc_handle");
	$tpl->set_block("paypal", "valid", "valid_handle");
	$tpl->set_block("paypal", "invalid", "invalid_handle");

	session_start();

	$steamid = calculate_steamid();
	$sql = "SELECT points, level FROM $table WHERE steamid='$steamid'";
		  
	$result = mysql_query($sql) OR die(mysql_error());
		  
	if(mysql_num_rows($result))
	{
		list($typepoints, $level) = mysql_fetch_row($result);
		
		$left = 0;
		$levelname = $akeys[0];

		if ((int)$level == 0) 
			$left = $avalues[0] - (int)$typepoints;
		else if ((int)$level != count($level_settings))
		{
			$levelname = $akeys[(int)$level];
			$left = $avalues[(int)$level] - (int)$typepoints;
		}
		else
			$levelname = $akeys[(int)$level];
		
		if ((int)$level != count($level_settings)) $text = "<h2>You need $left Stamm Points to become $levelname VIP</h2>";
		else $text = "<h2>You are already the highest VIP</h2>";
		
		$tpl->set_var(array(
			"typepoints"   => $typepoints,
			"text"		   => $text,
			"returnURL"    => curPageURL2(),
			"PaypalEmail"  => $paypal_email,
			"Language"	   => $paypal_language,
			"steamid"	   => $steamid,
			"country"	   => $paypal_country
		));
		$tpl->parse("valid_handle", "valid", true);
		
		$index = 0;
		
		foreach($paypal_options as $points => $value)
		{
			$tpl->set_var(array(
				"points"		  => $points,
				"value"		 	  => $value,
				"paypal_country"  => $paypal_country
			));
			$tpl->parse("paydetails_handle", "paydetails", true);
		}
		
		foreach($paypal_options as $points => $value)
		{
			$tpl->set_var(array(
				"points_set"		  => $points,
				"value_set"		 	  => $value,
				"index_set"  	      => $index
			));
			$tpl->parse("paydetailsc_handle", "paydetailsc", true);
			
			$index++;
		}
	}
	else
	{
		$tpl->set_var(array(
			"steamid"	   => $steamid
		));
		$tpl->parse("invalid_handle", "invalid", true);
	}

	$tpl->set_var(array(
		"header"       => $tpl->parse("out", "header"),
		"footer"       => $tpl->parse("out", "footer")
	));

	$tpl->parse("out", "paypal");
	$tpl->p("out");  
?>