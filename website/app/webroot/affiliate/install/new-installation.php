<?php

//DATA REQUIRED
$host = filter_input(INPUT_POST, 'dbhost', FILTER_SANITIZE_STRING);
$user = filter_input(INPUT_POST, 'dbusername', FILTER_SANITIZE_STRING);
$dbname = filter_input(INPUT_POST, 'dbname', FILTER_SANITIZE_STRING);
$install_folder = filter_input(INPUT_POST, 'install', FILTER_SANITIZE_STRING);
$pass = $_POST['dbpassword'];

//CREATE CONFIGURATION FILE
$new_config = fopen("../auth/config.inc.php", "w") or die("Unable to open file!");
$txt = "<?php \n";
fwrite($new_config , $txt);
$txt = "define('HOST', '".$host."'); \n";
fwrite($new_config , $txt);
$txt = "define('USER', '".$user."'); \n";
fwrite($new_config , $txt);
$txt = "define('PASSWORD', '".$pass."'); \n";
fwrite($new_config , $txt);
$txt = "define('DATABASE', '".$dbname."'); \n";
fwrite($new_config , $txt);
$txt = "//SET TO THE NAME OF THE FOLDER YOUR INSTALLATION IS INSIDE \n";
fwrite($new_config , $txt);
$txt = "\$INSTALL_FOLDER = '".$install_folder."'; \n";
fwrite($new_config , $txt);
$txt = "define('CAN_REGISTER', 'any'); \n";
fwrite($new_config , $txt);
$txt = "define('DEFAULT_ROLE', 'member'); \n";
fwrite($new_config , $txt);
$txt = "define('SECURE', FALSE); \n";
fwrite($new_config , $txt);
$txt = "\$DOMAIN = \$_SERVER['SERVER_NAME']; \n";
fwrite($new_config , $txt);
$txt = "\$protocol = stripos(\$_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://'; \n";
fwrite($new_config , $txt);
$txt = "\$main_url = \$protocol.\$DOMAIN; \n";
fwrite($new_config , $txt);
$txt = "\$domain_path = \$protocol.\$DOMAIN.'/'.\$INSTALL_FOLDER; \n";
fwrite($new_config , $txt);

$txt = "error_reporting(0); \n";
fwrite($new_config , $txt);

fclose($new_config );

//CONNECT TO DATABASE
include_once('../auth/db-connect.php');
/* check connection */
if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

/* check if server is alive */
if ($mysqli->ping()) {
    printf ("Our connection is ok!<br>");
  
  //INSTALL DATABASE
  
 /* ===========================================
	MARKETING MATERIALS / BANNERS 
   ========================================= */
$sql = "CREATE TABLE IF NOT EXISTS `ap_banners` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `filetype` varchar(50) NOT NULL,
  `adsize` int(1) NOT NULL,
  `url` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;";

if ($mysqli->query($sql) === TRUE) {
    echo "Marketing Materials table created successfully<br>";
} else {
    echo "Error creating table: " . $mysqli->error;
}

//INSERT INTO BANNERS TABLE
$sql = "INSERT INTO `ap_banners` (`id`, `filename`, `filetype`, `adsize`, `url`, `description`) VALUES
(5, 'aplogo_0.png', 'png', 2, 'http://mycustomlink.com', '');";

if ($mysqli->query($sql) === TRUE) {
    echo "Marketing Materials data inserted successfully<br>";
} else {
    echo "Error inserting data: " . $mysqli->error;
}
  
  /* ===========================================
	COMMISSION SETTINGS
   ========================================= */
$sql = "CREATE TABLE IF NOT EXISTS `ap_commission_settings` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `percentage` int(3) NOT NULL,
  `sales_from` decimal(10,2) NOT NULL,
  `sales_to` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;";

if ($mysqli->query($sql) === TRUE) {
    echo "Commission Settings table created successfully<br>";
} else {
    echo "Error creating table: " . $mysqli->error;
}

//INSERT INTO COMMISSION SETTINGS TABLE
$sql = "INSERT INTO `ap_commission_settings` (`id`, `percentage`, `sales_from`, `sales_to`) VALUES
(1, 5, '0.00', '200.00'),
(5, 12, '200.00', '201.00'),
(6, 15, '201.00', '202.00'),
(7, 20, '202.00', '299.00'),
(8, 3, '0.01', '50.00');";

if ($mysqli->query($sql) === TRUE) {
    echo "Commission settings data inserted successfully<br>";
} else {
    echo "Error inserting data: " . $mysqli->error;
}
  
 /* ===========================================
	EARNINGS TABLE
 ========================================= */
$sql = "CREATE TABLE IF NOT EXISTS `ap_earnings` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(15) NOT NULL,
  `product` varchar(255) NOT NULL,
  `comission` int(3) NOT NULL,
  `sale_amount` decimal(8,2) NOT NULL,
  `net_earnings` decimal(8,2) NOT NULL,
  `recurring` varchar(15) NOT NULL,
  `recurring_fee` int(10) NOT NULL,
  `last_reoccurance` datetime NOT NULL,
  `stop_recurring` int(1) NOT NULL,
  `country` varchar(2) NOT NULL,
  `datetime` datetime NOT NULL,
  `void` int(1) NOT NULL,
  `refund` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;";

if ($mysqli->query($sql) === TRUE) {
    echo "Earnings table created successfully<br>";
} else {
    echo "Error creating table: " . $mysqli->error;
}
  
 /* ===========================================
	LEADS TABLE
 ========================================= */
$sql = "CREATE TABLE IF NOT EXISTS `ap_leads` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(15) NOT NULL,
  `fullname` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `epl` decimal(10,6) NOT NULL,
  `converted` int(1) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

if ($mysqli->query($sql) === TRUE) {
    echo "Leads table created successfully<br>";
} else {
    echo "Error creating table: " . $mysqli->error;
} 

 /* ===========================================
	MEMBERS TABLE
  ========================================= */
$sql = "CREATE TABLE IF NOT EXISTS `ap_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `fullname` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `forgot_pin` varchar(255) NOT NULL,
  `forgot_key` varchar(60) NOT NULL,
  `terms` int(1) NOT NULL,
  `browser` varchar(255) NOT NULL,
  `balance` decimal(20,6) NOT NULL,
  `sponsor` int(15) NOT NULL,
  `admin_user` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;";

if ($mysqli->query($sql) === TRUE) {
    echo "Members table created successfully<br>";
} else {
    echo "Error creating table: " . $mysqli->error;
}

//INSERT INTO MEMBERS TABLE
$sql = "INSERT INTO `ap_members` (`id`, `username`, `fullname`, `email`, `password`, `forgot_pin`, `forgot_key`, `terms`, `browser`, `balance`, `sponsor`, `admin_user`) VALUES
(2, 'demo', 'John Doe', 'demos@joshuawebdesign.com', '$2y$10$5dEfCStEuUlB3k54ZDxqlOUwfT.3r3qYp12JNAnmVJPazQw.UP1ae', '', '', 1, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36', '0.000000', 0, 1),
(3, 'affiliate', 'Affiliate', 'affiliate@affiliate.com', '$2y$10$6G5WgoFibUxxmtc.uAL6lOsBuCF/ryUb48R41fZxzll7trLdBHtBC', '', '', 1, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36', '0.000000', 0, 0);";

if ($mysqli->query($sql) === TRUE) {
    echo "Members data inserted successfully<br>";
} else {
    echo "Error inserting data: " . $mysqli->error;
}

 /* ===========================================
	MULTI-TIER TABLE
 ========================================= */
$sql = "CREATE TABLE IF NOT EXISTS `ap_multi_tier_transactions` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(15) NOT NULL,
  `transaction_id` int(15) NOT NULL,
  `tier` int(2) NOT NULL,
  `commission` int(3) NOT NULL,
  `mt_earnings` decimal(10,2) NOT NULL,
  `datetime` datetime NOT NULL,
  `reversed` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

if ($mysqli->query($sql) === TRUE) {
    echo "Multi-tier table created successfully<br>";
} else {
    echo "Error creating table: " . $mysqli->error;
}   
 
 /* ===========================================
	OTHER COMMISSIONS TABLE
  ========================================= */
$sql = "CREATE TABLE IF NOT EXISTS `ap_other_commissions` (
  `id` int(1) NOT NULL,
  `sv_on` int(1) NOT NULL,
  `cpc_on` int(1) NOT NULL,
  `rc_on` int(1) NOT NULL,
  `mt_on` int(1) NOT NULL,
  `epc` decimal(10,6) NOT NULL,
  `lc_on` int(1) NOT NULL,
  `epl` decimal(10,6) NOT NULL,
  `tier2` int(3) NOT NULL,
  `tier3` int(3) NOT NULL,
  `tier4` int(3) NOT NULL,
  `tier5` int(3) NOT NULL,
  `tier6` int(3) NOT NULL,
  `tier7` int(3) NOT NULL,
  `tier8` int(3) NOT NULL,
  `tier9` int(3) NOT NULL,
  `tier10` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

if ($mysqli->query($sql) === TRUE) {
    echo "Other Commissions table created successfully<br>";
} else {
    echo "Error creating table: " . $mysqli->error;
}

//INSERT INTO OC TABLE
$sql = "INSERT INTO `ap_other_commissions` (`id`, `sv_on`, `cpc_on`, `rc_on`, `mt_on`, `epc`, `lc_on`, `epl`, `tier2`, `tier3`, `tier4`, `tier5`, `tier6`, `tier7`, `tier8`, `tier9`, `tier10`) VALUES
(1, 1, 1, 1, 1, '0.004000', 1, '1.000000', 4, 2, 1, 0, 0, 0, 0, 0, 0);";

if ($mysqli->query($sql) === TRUE) {
    echo "OC data inserted successfully<br>";
} else {
    echo "Error inserting data: " . $mysqli->error;
}  
  
 /* ===========================================
	PAYOUTS TABLE
 ========================================= */
$sql = "CREATE TABLE IF NOT EXISTS `ap_payouts` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(15) NOT NULL,
  `payment_method` int(1) NOT NULL,
  `payment_email` varchar(255) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `zip` int(10) NOT NULL,
  `bn` varchar(255) NOT NULL,
  `an` varchar(255) NOT NULL,
  `rn` varchar(255) NOT NULL,
  `status` int(1) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

if ($mysqli->query($sql) === TRUE) {
    echo "Payouts table created successfully<br>";
} else {
    echo "Error creating table: " . $mysqli->error;
} 
  
 /* ===========================================
	PRODUCTS TABLE
 ========================================= */
$sql = "CREATE TABLE IF NOT EXISTS `ap_products` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `product_img` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(16,2) NOT NULL,
  `commission` int(3) NOT NULL,
  `recurring` varchar(10) NOT NULL,
  `recurring_fee` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

if ($mysqli->query($sql) === TRUE) {
    echo "Products table created successfully<br>";
} else {
    echo "Error creating table: " . $mysqli->error;
} 
  
 /* ===========================================
	RECURRING HISTORY TABLE
 ========================================= */
$sql = "CREATE TABLE IF NOT EXISTS `ap_recurring_history` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(15) NOT NULL,
  `transaction_id` int(15) NOT NULL,
  `recurring_earnings` decimal(10,2) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";

if ($mysqli->query($sql) === TRUE) {
    echo "Recurring History table created successfully<br>";
} else {
    echo "Error creating table: " . $mysqli->error;
}     
  
 /* ===========================================
	REFERRAL TRAFFIC TABLE
  ========================================= */
$sql = "CREATE TABLE IF NOT EXISTS `ap_referral_traffic` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `affiliate_id` int(15) NOT NULL,
  `agent` varchar(255) NOT NULL,
  `ip` varchar(60) NOT NULL,
  `host_name` varchar(255) NOT NULL,
  `device_type` int(1) NOT NULL,
  `browser` varchar(20) NOT NULL,
  `os` varchar(20) NOT NULL,
  `country` varchar(3) NOT NULL,
  `landing_page` varchar(255) NOT NULL,
  `cpc_earnings` decimal(10,6) NOT NULL,
  `void` int(1) NOT NULL,
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;";

if ($mysqli->query($sql) === TRUE) {
    echo "Referral Traffic table created successfully<br>";
} else {
    echo "Error creating table: " . $mysqli->error;
}

//INSERT INTO referral TABLE
$sql = "INSERT INTO `ap_referral_traffic` (`id`, `affiliate_id`, `agent`, `ip`, `host_name`, `device_type`, `browser`, `os`, `country`, `landing_page`, `cpc_earnings`, `void`, `datetime`) VALUES
(17, 3, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36', '41.151.84.142', 'fios.verizon.net', 1, 'Chrome', 'Mac OS X', 'US', 'http://jdwebdesigner.com/affiliate-pro16/demo_landing?ref=3', '0.004000', 0, '2016-02-06 04:56:24'),
(18, 2, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2564.116 Safari/537.36', '51.191.24.145', 'fios.verizon.net', 2, 'Chrome', 'Mac OS X', 'US', 'http://jdwebdesigner.com/affiliate-pro16/demo_landing?ref=2', '0.004000', 0, '2016-03-06 04:56:44');";

if ($mysqli->query($sql) === TRUE) {
    echo "Referral Traffic sample data inserted successfully<br>";
} else {
    echo "Error inserting data: " . $mysqli->error;
}

 /* ===========================================
	SETTINGS TABLE
  ========================================= */
$sql = "CREATE TABLE IF NOT EXISTS `ap_settings` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `meta_title` varchar(255) NOT NULL,
  `meta_description` varchar(255) NOT NULL,
  `site_title` varchar(255) NOT NULL,
  `site_email` varchar(255) NOT NULL,
  `site_logo` varchar(255) NOT NULL,
  `default_commission` int(3) NOT NULL,
  `min_payout` decimal(8,2) NOT NULL,
  `currency_fmt` varchar(3) NOT NULL,
  `paypal` int(1) NOT NULL,
  `stripe` int(1) NOT NULL,
  `skrill` int(1) NOT NULL,
  `wire` int(1) NOT NULL,
  `checks` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;";

if ($mysqli->query($sql) === TRUE) {
    echo "Settings table created successfully<br>";
} else {
    echo "Error creating table: " . $mysqli->error;
}

//INSERT INTO settings TABLE
$sql = "INSERT INTO `ap_settings` (`id`, `meta_title`, `meta_description`, `site_title`, `site_email`, `site_logo`, `default_commission`, `min_payout`, `currency_fmt`, `paypal`, `stripe`, `skrill`, `wire`, `checks`) VALUES
(1, 'Affiliate Pro', 'Affiliate Pro | PHP Affiliate Tracking Software', 'Affiliate Pro', 'demos@jdwebdesigner.com', '', 10, '25.00', 'USD', 1, 1, 1, 1, 1);";

if ($mysqli->query($sql) === TRUE) {
    echo "Settings data inserted successfully<br>";
} else {
    echo "Error inserting data: " . $mysqli->error;
}

echo 'Installation Complete... <a href="../index">Click here to go to your site.';  
  
} else {
    printf ("Error: %s\n", $mysqli->error);
    echo 'Installation FAILED... Please follow the documenation to manually install Affiliate Pro.';
}

$mysqli->close();
