<?php
/**
 * -----------------------------------------------------
 * File        paypal_back.php
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
 


// Log errors
ini_set('log_errors', true);
ini_set('error_log', '/ipn_errors.log');


// Includes
include_once("inc/config.php");
include_once("inc/sql.php");
include_once("inc/function.php");
include_once("inc/ipnlistener.php");


// SQL class
$sql = new SQL($dbHost, $dbUser, $dbPass, $dbName);


// IPN Listener
$listener = new IpnListener();
$verified = false;


// Try to verify
try
{
	$listener->requirePostMethod();
	$verified = $listener->processIpn();
}
catch (Exception $e) 
{
	// Error
	error_log($e->getMessage());
	
	exit;
}
 
 
 
// Read out callback
$payed = $_POST['mc_gross'];
$txnID = $_POST['txn_id'];
$payerEmail = $_POST['payer_email'];
$steamidTable = explode(";", $_POST['custom']);
$name = $_POST['first_name']." ".$_POST['last_name']."";
$payerID = $_POST['payer_id'];
$pointsGet = $_POST['option_selection1'];

// Steamid and Table
$steamid = $steamidTable[0];
$table = nameToTable($steamidTable[1]);



// Verified?
if ($verified)
{
	// Log
	logAction("Verified: Steamid $steamid payed $payed for $pointsGet Points. Status: " .$_POST['payment_status'], $sql);
	
	// Check same email
	if ($_POST['receiver_email'] == $paypalEmail)
	{	
		// Status completed?
		if ($_POST['payment_status'] == "Completed")
		{
			// Money is same
			if ($paypalPrices[$pointsGet] == $payed)
			{
				// Check for duplicate
				$result = $sql->query("SELECT * FROM `stamm_interface_payments` WHERE `txnid`='$txnID' LIMIT 1");
				
				// Check if valid result
				if ($result)
				{	
					// Not found?
					if (!$sql->foundData($result))
					{
						// Int
						$points_int = (int)$pointsGet;
						$oldpoints = $sql->query("SELECT `points` FROM `$table` WHERE `steamid`='$steamid'");
						
						list($oldpoint) = $sql->getRows($oldpoints);
						
						$newpoint = ((int)$oldpoint) + $points_int;
						
						
						// Update points
						$sql_update = "UPDATE `$table` SET `points`=`points` + $pointsGet WHERE `steamid`='$steamid'";
						$sql->query($sql_update);
						
						
						// Prepare Email
						$host = $_SERVER['HTTP_HOST'];

						
						// Format Message
						$message = 
							"
							Hello! 
							
							Thank you for buying Stamm Points. Here are your payment details...
							
							
							Your Name: $name
							Your Email: $payerEmail
							Your PayerID: $payerID
							Your Payment ID: $txnID
							Payed Points: $pointsGet
							Old Points: $oldpoint
							New Points: $newpoint
							Payed: $payed $paypalCountry
							
							
							Rejoin the Server to update your Stamm Profile!
							
							
							If you have Problems, please contact the administator!
							

							Thank You

							Administrator
							$host
							______________________________________________________
							THIS IS AN AUTOMATED RESPONSE. 
							***DO NOT RESPOND TO THIS EMAIL****
							";

						// We don't want \t
						$message = str_replace("\t", "", $message);
						
						// Send Email
						mail($payerEmail, "Stamm-Points Payment Details", $message,
						"From: \"Stamm Points Payment\" <auto-reply@$host>\r\n" .
						 "X-Mailer: PHP/" . phpversion());
					}
				}
			}
		}
	}
	
	// Insert Payment
	$sql_insert = "INSERT INTO `stamm_interface_payments`
	(`txnid`, `payerid`, `payed`, `name`, `email`, `points`, `steamid`, `server`, `date`)
	VALUES
	('$txnID', '$payerID', '$payed', '$name', '$payerEmail', '$pointsGet', '$steamid', '$table'
	,now())";
	
	
	// Query
	$sql->query($sql_insert);
}
else
{
	logAction("Not Verified: Steamid $steamid payed $payed for $pointsGet Points. Status: " .$_POST['payment_status'], $sql);
}