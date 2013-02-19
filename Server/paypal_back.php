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
	ini_set('log_errors', true);
	ini_set('error_log', dirname(__FILE__).'/ipn_errors.log');
	
	include 'inc/funktion.php';
	include 'config.php';
	include 'inc/ipnlistener.php';
	
	$listener = new IpnListener();
	$verified = false;
	
	try 
	{
		$listener->requirePostMethod();
		$verified = $listener->processIpn();
	}
	catch (Exception $e) 
	{
		error_log($e->getMessage());
		
		exit(0);
	}
	 
	$payed = $_POST['mc_gross'];
	$txn_id = $_POST['txn_id'];
	$payer_email = $_POST['payer_email'];
	$steamid = $_POST['custom'];
	$name = "".$_POST['first_name']." ".$_POST['last_name']."";
	$payer_id = $_POST['payer_id'];
	$points_get = $_POST['option_selection1'];
	
	if ($verified)
	{
		if ($_POST['receiver_email'] == $paypal_email) 
		{	
			if ($_POST['payment_status'] == "Completed") 
			{
				if ($paypal_options[$points_get] == $payed)
				{
					$sql = "SELECT COUNT(*) from payments where txnid='$txn_id'";
					$r = mysql_query($sql);
					
					if ($r)
					{	
						$exists = mysql_result($r, 0);
						mysql_free_result($r);
						
						if (!$exists)
						{
							$points_int = (int) $points_get;
							$oldpoints = mysql_query("select points from $table where steamid='$steamid'");
							list($oldpoint) = mysql_fetch_row($oldpoints);
							$newpoint = ((int) $oldpoint) + $points_int;
							
							$sql_update = "UPDATE $table SET points=points+$points_get where steamid='$steamid'";
							mysql_query($sql_update);
							
							$host = $_SERVER['HTTP_HOST'];
							$host_upper = strtoupper($host);

							$message = 
								"
								Hello! \n
								Thank you for buying Stamm Points. Here are your payment details...\n
								
								Your Name: $name\n
								Your Email: $payer_email \n
								Your PayerID: $payer_id\n
								Your Payment ID: $txn_id \n 
								Payed Points: $points_get \n
								Old Points: $oldpoint \n
								New Points: $newpoint \n
								Payed: $payed $paypal_country\n
								
								Rejoin the Server to update your Stamm Profile!\n
								
								If you have Problems, please contact the administator!\n

								Thank You

								Administrator
								$host_upper
								______________________________________________________
								THIS IS AN AUTOMATED RESPONSE. 
								***DO NOT RESPOND TO THIS EMAIL****
								";

							mail($payer_email, "Stamm Points Payment Details", $message,
							"From: \"Stamm Points Payment\" <auto-reply@$host>\r\n" .
							 "X-Mailer: PHP/" . phpversion());
						}
					}
				}
			}
		}
		
		$sql_insert = "INSERT into `payments`
		(`txnid`, `payer_id`, `payed`, `full_name`, `user_email`, `points`, `steamid`, `date`)
		VALUES
		('$txn_id', '$payer_id', '$payed', '$name', '$payer_email', '$points_get', '$steamid'
		,now())";
		
		mysql_query($sql_insert);
	}
?>