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

// --> Begin Settings ----------------------------------------------------------

$vips 		= 0;				 	// 1 = show only VIP's, 0 = all Players
									// 1 = Zeige nur VIP's, 0 = alle Spieler
$show_users = "20";					// How much users per page
									// Spieler pro Seite
$points_min = "100"; 				// Minimum Stamm Points of Players in VIP List
									// Minimale Punkte um in der Liste zu stehen

// PayPal Settings -------------------------------------------------------------

// Instant Payment Notification (https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_admin_IPNSetup)

$paypal_enable = 1; 				// 1 = enable Paypal, 0 = Disable
									// 1 = PayPal aktivieren, 0 = Deaktivieren
$paypal_email = "your@email.com"; 	// PayPal Email
$paypal_language = "US"; 			// Two Letter language code , all see here: https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_country_codes
									// Sprach Code, siehe hier: https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_country_codes
$paypal_country = "USD"; 			// Your currency, list over all currencies: https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_currency_codes
									// Deine Währung, siehe hier: https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_api_nvp_currency_codes
$paypal_options = 
array
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

); 									// Your point prices "points" => "price" (Please use points, not commas, for prices!)
									// Deine Preise! "Punkte" => "Preis" (Bitte Punkte und keine Kommas für Preise benutzen!)

// Stamm Settings --------------------------------------------------------------

$level_settings =
array
(
	"bronze" => "500",
	"silver" => "1000",
	"gold" => "1500",
	"platinum" => "2000",
	"diamond"  => "2500",
	"god" => "3000"
);									// Your level settings, please order right from low points to high points!
									// Deine Level Einstellungen von Stamm, bitte von unten nach oben sortieren!

// MySQL Settings --------------------------------------------------------------

$servername = "YourMYSQLServer"; 	// DB hostname
$dbusername = "YourMYSQLUser";      // DB username
$dbpassword = "YourMYSQLPassword";  // DB password
$dbname     = "YourMYSQLDatabase";  // DB name

$table      = "STAMM_DB_1";      	// Name of your Stamm Table
									// Name deiner Stamm Datenbank

// --> End Settings ------------------------------------------------------------
?>
