<?php
define('INSTALLED',TRUE);
    
$theme = 'default';
$language = 'en';
    
$db_host = 'workaholic-studios.net';
$db_port = '3306';
$db_user = 'dspweb';
$db_pass = '123qaz';
$db_name = 'dspweb_dspdb';
$xi_name = 'dspweb_xiweb';

$site_name = 'Old School';
$server_address = 'dspt.info';
$frontpage_title = 'XIWeb Installation';
$frontpage_message = 'This is the default front-page message for your XIWeb installation. To change this, please check the configuration file.';

// Character creation settings
$enable_creation = TRUE; // If set to TRUE, will allow characters to be created 
$allow_advanced_jobs = TRUE; // If set to TRUE, will allow characters to be created with advanced starting jobs (Default: FALSE)
$reserved_names = array(
'Justin',
);

$show_currencies = TRUE; // Should the 'currencies' tab be displayed on character sheets?

/* 
  TODO: Since this stuff isn't implemented yet, let's not show it on the menubar
  REMOVE AS IMPLEMENTED
*/

$bestiary = FALSE; // Temporary, so that Bestiary doesn't show up in the menu bar
$ah = FALSE; // Temporary, so that Auction House doesn't show up in the menu bar
$items = FALSE; // Temporary, so that items don't show up in the menu bar
$friends = FALSE; // Temporary, so that the friends list doesn't show up
$tickets = FALSE; // Temporary, so that tickets don't show up in the menu bar
?>