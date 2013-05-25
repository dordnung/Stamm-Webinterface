<?php
/**
 * -----------------------------------------------------
 * File        admin.php
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


// Already logged in?
if (validateAdminLogin(false, "", "", $sql))
{
	// Go to index
	header("Location: index.php");
	
	// Exit
	exit;
}


// Empty Error
$error = '<div style="padding-bottom: 30px"></div>';


// Logged in
if (isset($_POST["login"]) && isset($_POST["user"]) && isset($_POST["pass"]))
{
	// Check if correct login
	if (validateAdminLogin(true, $_POST["user"], $_POST["pass"], $sql))
	{
		// Log
		logAction("Logging in to admin account - Successfull", $sql);
		
		// Go to index
		header("Location: index.php");
		
		// Exit
		exit;
	}
	else
	{
		// Log
		logAction("Logging in to admin account - Failed", $sql);
		
		// Invalid Login
		$error = '<div style="color:red; padding-bottom:30px"><strong>Invalid Login Data</strong></div>';
	}
}


// Get Skin
$skin = getSkin();


?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Stamm Admin Login</title>
		<link href="style.css" rel="stylesheet" />
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
		<style type="text/css">
		<!--
			.container 
			{
				width: 500px;
				text-align: center;
				<?php
				
				if ($skin == "light")
				{
					echo 'background-color: #FFF;';
					echo 'border: 5px solid #313843;';
				}
				else
				{
					echo 'background-color: #313843;';
					echo 'border: 1px solid #bbb;';
				}
				
				?>
				position: absolute;
				top: 50%;
				left: 50%;
				margin: -300px 0px 0px -300px;
				overflow: auto;
			}
		-->
		</style>
	</head>
	<body class="<?php echo $skin; ?>">
		<div class="container">
			<h1 style="padding-top: 10px; padding-bottom: 0; margin-bottom: 0">Admin Login</h1>
		
			<?php echo $error; ?>
		
			<form id="form1" name="form1" method="post">
				<label>
					<strong>Username</strong><br />
					<input type="text" name="user" id="user" />
				</label>
				
				<br /><br />
				
				<label>
					<strong>Password</strong><br />
					<input type="password" name="pass" id="pass" />
				</label>
				
				<p>&nbsp;</p>
				
				<input type="submit" name="login" id="login" value="Login" style="height: 25px; width: 100px" />
			</form>
			<a href="index.php" class="page_switch<?php echo $skin; ?>" style="float: right; padding-right: 10px; padding-bottom: 10px">Back</a> 
		</div>
	</body>
</html>
