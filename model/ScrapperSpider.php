<?php

if(!defined('SCRAPPER_INCLUDE')) die('I\'m sorry but current you can\'t use this file outside the scrapper application');

/**
 *	Default Spider instance in the system
 *
 *	@depends PHPCrawl 
 *	@link{See here http://phpcrawl.cuab.de/}
 */
class ScrapperSpider extends PHPCrawler {
	// Contains site specific data here so that we can use them on handlePageData()
	public $siteConfig;
	/**
	 *	Providing all the default set of Spider Options in here
	 *
	 *	@param	Array	$params	Contains the site specific settings passed on initially during the spider creation process
	 */
	function configure($params) {
		parent::__construct();
		// Setting the Parameters value
		$this->setUserAgentString($params['user_agent']);

		// Spider General configuration
		$this->addNonFollowMatch("/.(jpg|gif|png|bmp)$/ i");
		$this->setAggressiveLinkExtraction(false);	// By default its off here
		$this->addReceiveContentType("text/html");	// Default get only HTML pages
		
		if(isset($params['socket_timeout'])) {
			$c = $this->setStreamTimeout($params['socket_timeout']);
			if(!$c) verbose("Not a valid socket_timeout option value. Using the default 10.0", "warn");
			$this->setStreamTimeout(10.10);
		}
		else {
			$this->setStreamTimeout(10.10);
		}
		
		$this->setConnectionTimeout(10);	// Not configurable since does not work relaiably yet on all env.
	}
	
	/**
	 *	Providing the default implementation of what to be done after fetching
	 *	the content from the specified URL.
	 *
	 *	@link	{http://phpcrawl.cuab.de/classreference.html#handlepagedata}
	 */
	public function handlePageData(&$response) {
		if(!$response['received_completely'] || !$response['received']) {
			verbose("Content not fully received. Possible site may be too slow or blocking the requests.", "warn");
		}
		if($response['error_code'] != FALSE)  {
			verbose($response['error_string'], "error");
		}
		
		global $logger;
		$logger->info("Crawled the url - " . $response['url'] . "\n");
		verbose("Crawled the url - " . $response['url'], "trace");
		
		// Clean the Html content using Tidy
		$tidy = tidy_parse_string($response['content']);
		$responseContent = $tidy->html()->value;
		$dom = new DOMDocument;
		$dom->preserveWhiteSpace = false;
		$dom->strictErrorChecking  = false;
		$dom->substituteEntities  = true;
		@$dom->loadHTML($responseContent);
		$xpath = new DOMXPath($dom);
		/**
		 *	@algorithm
		 *		1.	Get the required attributes from the page
		 *		2.	Get the Bean instance used for handling the data
		 *		3.	Call the Bean->save() to persist the content
		 */
		// @TODO Add more object instance properties, next version 
		$item = $this->siteConfig['items'];
		$bean = $item['_bean'];
		if(class_exists($bean)) {
			$implements = class_implements($bean);
			if(!in_array("Scrapable",$implements)) {
				$logger->fatal("Bean class $bean does not implement the Scrapable Interface.");
				verbose("Bean class $bean does not implement the Scrapable interface. I'm sorry but I need it to save objects obtained by crawling. I'll now stop the crawling process. ", "error");
				$logger->info("You can also extend the ScrapperBean class which provides the default implementation.");
				verbose("You can also extend the ScrapperBean class which provides the default implementation.", "info");
				return false;
			}
			if(!isset($item['_base_xpath'])) {
				$contextNode = $xpath->query('/');
			} else {
				$contextNode = $xpath->query($item['_base_xpath']);
			}
			// verbose("Length of Base nodes for items are {$contextNode->length}", "info");
			
			foreach($contextNode as $node) {
				$value = trim($node->textContent);
				
				$obj = new $bean;
				// Loop through the required properties and create the instance
				while($prop = current($item['props'])) {
					$prop_value = key($item['props']);
					$itemNode = $xpath->query($prop['xpath'], $node);
					
					// We get the value only when it exists, isn't? Duh!
					if($itemNode->length > 0) {
						$obj->$prop_value = strtolower(trim(str_replace("\n|\r", " ", $itemNode->item(0)->nodeValue)));
					}
					next($item['props']);
				}
				reset($item['props']);
				$obj->save();	// @TODO Don't we need to check if all the properties are added? -- Update: Let the users handle it in the bean
			}
			
			// Add to the log if there aren't any base nodes
			if($contextNode->length < 1) {
				verbose("Base node(s) were not found. Possible site structure change. ", "warn");
				$logger->warn("Base node(s) were not found. Possible site structure change. ");
			}
		} else {
			verbose("Class $bean was not found. Sorry we cannot continue. Add the Required Bean model in the model folder with the same name as its class", "fatal");
			$logger->fatal("Class $bean was not found. Sorry we cannot continue. Add the Required Bean model in the model folder with the same name as its class");
			return false;
		}
		flush();
	}
}

