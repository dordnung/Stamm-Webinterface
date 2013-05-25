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





// Stamm Settings ------------------------------------------------------------------

// Your level settings. Use "<levelname>" => "<points>", (Please order from low points to high points!)
// For Special levels leave points field blank.
$levelSettings = array
(
	"Bronze" => "500",
	"Silver" => "1000",
	"Gold" => "1500",
	"Platinum" => "2000",
	"Diamond"  => "2500",
	"God" => "3000",
	//"Special" => "",
);





// Server Settings -----------------------------------------------------------------

// Add: "<tablename>" => "<servername>",
$serverOptions = array
(
	"STAMM_DB_1" => "Server1", 
	//"STAMM_DB_2" => "Server2", 
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
