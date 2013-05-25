<?php 

// --> Begin of Settings -----------------------------------------------------------





// General Settings ----------------------------------------------------------------

// 1 = Show only VIP's, 0 = Show all Players
$onlyVips = 0;

// Users per page
$usersPerPage = "20";

// Minimum Stamm points a players needs to get in VIP List
$minPoints = "100";





// PayPal Settings -------------------------------------------------------------
// Needs activated Instant Payment Notification (https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_admin_IPNSetup)

// 1 = Enable Paypal, 0 = Disable
$paypalEnable = 1;

// PayPal Email
$paypalEmail = "your@email.com";

// Two Letter language code, all see here: https://www.x.com/developers/paypal/documentation-tools/api/country-codes
$paypalLanguage = "US";

// Your currency, list over all currencies: https://www.x.com/content/currency-codes-e_howto_api_soap_currency_codes-html
$paypalCountry = "USD";


// Your point prices. Use "<points>" => "<price>", (Please use dots, not commas, for prices!)
$paypalPrices = array
(
	"100" => "1.00", 
	"300" => "2.95",
	"500" => "4.90",
	"800" => "7.85",
	"1000" => "9.80",
	"1300" => "12.75",
	"1500" => "14.70",
	"1800" => "17.65",
	"2000" => "19.60",
);





// Server Settings -----------------------------------------------------------------

// Here you can define your servers. Please order the points from low points to high points!
// For Special levels leave points field blank.
// IMPORTANT: Keep this structur!
$serverOptions = array
(
	// Tablename
	"STAMM_DB_1" => array 
	(
		// Servername
		"Server1",
		
		// Level Settings
		array
		(
			"Bronze" => "500",
			"Silver" => "1000",
			"Gold" => "1500",
			"Platinum" => "2000",
			"Diamond"  => "2500",
			"God" => "3000",
			//"Special" => "",
		)
	),
	
	/* To activate this, remove this line
	
	// Tablename
	"STAMM_DB_2" => array 
	(
		// Servername
		"Server2",
		
		// Level Settings
		array
		(
			"Bronze" => "500",
			"Silver" => "1000",
			"Gold" => "1500",
			"Platinum" => "2000",
			"Diamond"  => "2500",
			"God" => "3000",
			//"Special" => "",
		)
	),
	
	To activate this, remove this line */ 
);




// MySQL Settings ------------------------------------------------------------------

// DB hostname
$dbHost = "DBHOST";

// DB username
$dbUser = "DBUSERNAME";

// DB password
$dbPass = "DBPASSWORD";

// DB name
$dbName = "DBNAME";





// --> End Of Settings ----------------------------------------------------------------
