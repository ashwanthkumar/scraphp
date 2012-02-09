<?php
if(!defined('SCRAPPER_INCLUDE')) die('I\'m sorry but current you can\'t use this file outside the scrapper application');

/**
 *	Define the configuration for price scrapper application. This is a sample
 *	configuration which crawls all the products on Flipkart and adds to the 
 *	datastore. 
 *
 *	@author	Ashwanth Kumar <ashwanth@ashwanthkumar.in>
 */
return array(
	/**
	 *	Defining the Price Scrapper datastore configuration. Price scrapper uses
	 *	PDO as its abstraction layer for accessing the datastore.
	 *	
	 *	@optinal	Leaving this blank will use sqlite db named "scrapper.db" in 
	 *				data folder at $SCRAPPER_ROOT path.
	 */
	 '_datastore' => array(
	 	'dsn' => 'mysql:host=localhost;dbname=flipkart_price',
	 	'user' => 'root',
	 	'pass' => '',
	 ),
	 /**
	  *	Enable the verbose mode in displaying the data.
	  *		WARNING: This displays a lot of stupid stuff really
	  *	@optional	Enabled by default
	  */
	 '_verbose' => true,
	 /**
	  *	User Agent to be used with the Spider while crawling the data
	  *
	  *	@optional 	Default value is "Price Scrapper Spider"
	  */
	'user_agent' => "Ashwanth Kumar <ashwanth@ashwanthkumar.in> Hacking Challenge",
	/**
	 *	Contains the array of keys used to define new sites for the spider to fetch the data from
	 *
	 *	@required
	 */
	'sites' => array(
		/**
		 *	Define a custom sites key here. 
		 *	
		 *	@required
		 */
		'flipkart' => array(
			'base_url' => "http://www.flipkart.com/",
			// 'base_url' => 'http://www.flipkart.com/computers/list/',
			/**
			 *	Adds a perl-compatible regular expression (PCRE) to the list of rules that 
			 *	decide which URLs found on a page should be followd explicitly.
			 *	@optional
			 */
			/**
			 *	Some quick types might be like
			 *	/\/books\//		- Crawl only the book category of the site
			 *	/\/computers\//	- Crawl only the computers category of the site
			 */
			'validMatch' => '',	// Now this is everything =D 
			/**
			 *	Adds a perl-compatible regular expression (PCRE) to the list of rules that
			 *	decide which URLs found on a page should be ignored by the crawler. 
			 *	@optional	Follows 'validMatch' if present or crawls all of them
			 */
			// 'nonValidMatch' => '', // Uncomment for usage
			/**
			 *	Sets the limit of pages/files the crawler should crawl. If the limit is
			 *	reached, the crawler stops the crawling-process. 
			 *	@optional	The default-value is 0 (no limit).
			 */
			// 'pageLimit' => 1,
			/**
			 *	Items that you want to extract from the site. 
			 *
			 *	@TODO	Currently we extract only one set of item (may be right to 
			 *			call it as object?) from the page.
			 *
			 *	@optional	Leaving this blank does not extract any content. Instead 
			 *				the visited URLs are logged into the database.
			 */
			"items" => array(
				/**
				 *	Bean that will take of persisting the items from the site. This bean (class) has to implement
				 *	Scrapable Interface which will take care of any kind of CRUD operations with the items obtained.
				 *
				 *	There is also a default implementation provided called - "Scrapper" which will write the items 
				 *	to sqlite db called "scrapper.db". This db can be found in the data folder at $SCRAPPER_ROOT
				 *	path. 
				 *
				 *	@optional If this attribute is absent Scrapper is implicitily used.
				 */
				"_bean" => "Flipkart",
				/**
				 *	XPath used for the finding the base content attribute to extract the content. 
				 *	All items will use this as the base node or contextual node for extracting their content.
				 *
				 *	@optional If this value is absent the content used
				 */
				"_base_xpath" => "/",
				/**
				 *	Contains the list of properties that need to extracted from the URL
				 *	
				 *	Every item that needs to be extracted has to have 2 attributes associated with it.
				 *		1.	xpath	=>	XPath Query used to identify the object in the page.
				 *						@required	
				 *
				 *		2.	default	=>	Default value that is to be used when xpath query did not yield any value or is null or empty
				 *						@optinal	If ignored and xpath did not provide any value, the spider will log the error and 
				 *									stop the crawling process. 
				 *						@TODO 		Need to implement this in the spider.
				 */
				"props" => array(
					"name" => array(
						"xpath" => "/html/body/div/div/div/div/div/div/div[3]/div/h1",
					),
					"price" => array(
						"xpath" => "//*[@id='fk-mprod-our-id']",
					),
					"tag" => array(
						"xpath" => "//li[@class='fk-submenu-item firstitem']/a",
					),
				),
			),
		),
	),
);

