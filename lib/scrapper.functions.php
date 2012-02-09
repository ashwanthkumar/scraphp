<?php

global $logger, $verbose;

/**
 *	Automatically load all the models
 */
function load_models($class) {
	if(is_file("./model/" . $class . ".php")) {
		include("./model/" . $class . ".php");
	}
}

/**
 *	Automatically load the library methods here
 */
function load_lib($name) {
	$class = array(
		'PHPCrawler' => './lib/phpcrawl/classes/phpcrawler.class.php',
		'PHPCrawlerUtils' => './lib/phpcrawl/classes/phpcrawlerutils.class.php',
		'PHPCrawlerPageRequest' => './lib/phpcrawl/classes/phpcrawlerpagerequest.class.php',
		'PHPCrawlerRobotsTxtHandler' => './lib/phpcrawl/classes/phpcrawlerrobotstxthandler.class.php',
		'R' => './lib/redbeanphp/rb.php',
	);
	
	if(isset($class[$name])) {
		include($class[$name]);
	}
}

/**
 *	Display a message if the verbose mode is enabled
 *
 *	@param	String	$message	Message that needs to logged
 *	@param	String	$type		Log type, can be anyone of (trace,debug, info, warn, error, fatal)
 */
function verbose($message, $type = "trace") {
	global $debug, $verbose;
	if($debug) {
		switch($type) {
			case "info":
				$verbose->info($message);
				break;
			case "error":
				$verbose->error($message);
				break;
			case "warn":
				$verbose->warn($message);
				break;
			case "trace":
				$verbose->trace($message);
				break;
			case "debug":
				$verbose->debug($message);
				break;
			case "fatal":
				$verbose->fatal($message);
				break;
			default:
				$verbose->trace($message);
				break;
		}
	}
}

/**
 *	Common exit method used in the application
 */
function end_scrapper() {
	echo '[ERROR] Scrapper terminated due to an error. Refer Logs for more details.' . "\n";
	die(1);
}

