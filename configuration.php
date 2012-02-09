<?php
if(!defined('SCRAPPER_INCLUDE')) die('I\'m sorry but current you can\'t use this file outside the scrapper application');

/**
 *	Define the configuration for price scrapper application
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
	 	'dsn' => 'mysql:host=localhost;dbname=fohubcom_price',
	 	'user' => 'root',
	 	'pass' => '',
	 ),
	 '_default_datastore' => array(
	 	'dsn' => 'sqlite:./data/scrapper.db',
	 	'user'=> 'ashwanth',
	 	'pass'=> 'password',
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
	'socket_timeout' => 10,
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
		'agmarket' => array(
			'base_url' => "http://agmarknet.nic.in/rep1Newx1_today.asp",
			/**
			 *	Adds a perl-compatible regular expression (PCRE) to the list of rules that 
			 *	decide which URLs found on a page should be followd explicitly.
			 *	@optional
			 */
			// 'validMatch' => '',	// Uncomment for usage
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
			'pageLimit' => 1,
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
				"_bean" => "AGMarket",
				/**
				 *	XPath used for the finding the base content attribute to extract the content. 
				 *	All items will use this as the base node or contextual node for extracting their content.
				 *
				 *	@TODO	When this XPath does not provide any items, need to log the result and alert the admin
				 *			as there is possibility of site structure being modified. 
				 *
				 *	@TODO	I just can't get the libxml implemenatation of XPath working in PHP, so we are going to queue
				 *			the page URLs in the datastore and spawn another process to extract the content out of it using
				 *			YQL. This may sound creepy, but thats the best workaround I can get right now, until I get into 
				 *			the core of libxml implementation in PHP 5.3.2(-ubuntu).
				 *
				 *	@optional If this value is absent the content used
				 */
				"_base_xpath" => "//table/tr[position() > 7]",
				/**
				 *	Contains the list of properties that need to extracted from the URL
				 *	
				 *	Every item that needs to be extracted has to have 2 attributes associated with it.
				 *		1.	xpath	=>	XPath Query used to identify the object in the page.
				 *						@required	
				 *
				 */
				"props" => array(
					"name" => array(
						"xpath" => "td[1]",
					),
					"max_price" => array(	/* Max price is stored with the tag value in price as 1 */
						"xpath" => "td[2]",
					),
					"location1" => array(	/* Maps location containing max_price */
						"xpath" => "td[2]/a/@title",
					),
					"min_price" => array(	/* Min price is stored with the tag value in price as 2 */
						"xpath" => "td[3]",
					),
					"location2" => array(	/* Maps location containing min_price */
						"xpath" => "td[3]/a/@title",
					),
				),
			),
		),
	),
);

