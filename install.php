<?php
/**
 * -----------------------------------------------------
 * File        install.php
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



// SQL class
$sql = new SQL($dbHost, $dbUser, $dbPass, $dbName);


// Empty Error
$error = '<div style="padding-bottom: 30px"></div>';
$finish = false;


// Clicked install
if (isset($_POST["install"]) && isset($_POST["user"]) && isset($_POST["pass"]) && isset($_POST["pass2"]))
{
	// Check username is correct
	if (strlen($_POST["user"]) < 3 || strlen($_POST["user"]) > 64)
	{
		$error = '<div style="color: red; padding-bottom: 30px"><strong>Username must be between 3 and 64 chars!</strong></div>';
	}

	// Check password is correct
	else if (strlen($_POST["pass"]) < 3 || strlen($_POST["pass"]) > 64)
	{
		$error = '<div style="color: red; padding-bottom: 30px"><strong>Password must be between 3 and 64 chars!</strong></div>';
	}
	
	// Check passwords match
	else if ($_POST["pass"] != $_POST["pass2"])
	{
		$error = '<div style="color: red; padding-bottom: 30px"><strong>Passwords doesn\'t match!</strong></div>';
	}
	
	else
	{
		// All good
		$finish = true;
		
		
		// Remove old tables
		$sql->query("DROP TABLE IF EXISTS `stamm_interface_log`, `stamm_interface_payments`, `stamm_interface_users`");
		
		
		// Create new Ones
		$sql->query("CREATE TABLE `stamm_interface_users` (`username` VARCHAR(64) NOT NULL, `password` VARCHAR(64) NOT NULL) COLLATE='utf8_general_ci' ENGINE=MyISAM");
		$sql->query("CREATE TABLE `stamm_interface_log` (`action` TEXT NOT NULL, `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) COLLATE='utf8_general_ci' ENGINE=MyISAM");
		$sql->query("CREATE TABLE `stamm_interface_payments` (`id` BIGINT(20) NOT NULL AUTO_INCREMENT, `txnid` VARCHAR(64) NOT NULL, `payerid` VARCHAR(64) NOT NULL, `payed` VARCHAR(32) NOT NULL, `name` VARCHAR(64) NOT NULL, `email` VARCHAR(64) NOT NULL, `points` VARCHAR(65) NOT NULL, `steamid` VARCHAR(64) NOT NULL, `server` VARCHAR(64) NOT NULL, `date` DATE NOT NULL DEFAULT '0000-00-00', PRIMARY KEY (`id`), UNIQUE INDEX `txnid` (`txnid`)) COLLATE='utf8_general_ci' ENGINE=MyISAM AUTO_INCREMENT=1");
		
		
		// Insert admin
		$sql->query("INSERT INTO `stamm_interface_users` (`username`, `password`) VALUES ('" .$_POST["user"]. "', MD5('" .$_POST["pass"]. "'))");
	}
	
}


?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<?php
		if ($finish)
		{
			echo '<meta http-equiv="refresh" content="7; URL=index.php">';
		}
		?>
		<title>Stamm Install</title>
		<link href="style.css" rel="stylesheet" />
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
		<style type="text/css">
		<!--
			.container 
			{
				width: 500px;
				text-align: center;
				background-color: #FFF;
				border: 5px solid #313843;
				position: absolute;
				top: 50%;
				left: 50%;
				margin: -300px 0px 0px -300px;
				overflow: auto;
			}
		-->
		</style>
	</head>
	<body class="light">
		<div class="container" style="padding-bottom: 10px">
			<h1 style="padding-top: 10px; padding-bottom: 0; margin-bottom: 0">Stamm Webscript Install</h1>
				<?php
				if (!$finish)
				{
					echo $error;
					echo '
					<form id="form1" name="form1" method="post">
						<label>
							<strong>Admin Username</strong><br />
							<input type="text" name="user" id="user" />
						</label>
						
						<br /><br />
					
						<label>
							<strong>Admin Password</strong><br />
							<input type="password" name="pass" id="pass" />
						</label>
						
						<br /><br />
						
						<label><strong>Retype Password</strong><br />
							<input type="password" name="pass2" id="pass2" />
						</label>
						
						<p>&nbsp;</p>
					
						<input type="submit" name="install" id="install" value="Install" style="height: 25px; width: 100px" />
					</form>';
				}
				else
				{
					echo '
					<p>&nbsp;</p> 
					<p style="color: red; padding-bottom: 30px"><strong>Install Successfull!<br />Please delete the install.php now!</strong></p>
					';
				}
				?>
		</div>
	</body>
</html>