<?php

/**
 *	Scrapable interface for use in beans to be able to save the object in the 
 *	datastore. Well currently we have only one we can always implement some more
 *	later.
 *
 *	@author	Ashwanth Kumar <ashwanth@ashwanthkumar.in>
 */ 
interface Scrapable {
	/**
	 *	Saves the properties to the datastore
	 */
	public function save();
}

