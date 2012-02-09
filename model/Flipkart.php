<?php

/**
 *	Bean used for extracting the content from "Flipkart" books
 *	@href	http://www.flipkart.com/books/0805863095
 */
class Flipkart extends ScrapperBean {
	/**
	 *	@override	Some custom modifications of the data extracted are required. 
	 */
	 public function save() {
	 	global $siteKeyId;	// Value set from the Main Spider exec.
	 	
	 	if(isset($this->name) && isset($this->price) && isset($this->tag)) {
		 	$p = $this->getProduct(trim($this->name), $siteKeyId);
		 	$pr = $this->getPrice($p->id);
		 	$pr->price = str_replace("rs. ", "", $this->price);	// Remove the Rs. thingy of the price tag
		 	$pr->location_id = 1;	// Universal thingy
		 	
		 	$p->tag = $this->tag;
		 	$p->ownPrice[] = $pr;
		 	R::store($p);
		 	verbose("Updated the price for {$p->name}.", "info");
	 	}
	 	// Probably this function was called just like that? Duh! Need to make the crawler more intellgient.
	 }

	 public function getProduct($product, $source = 0) {
	 	$prd = R::findOne("products", "name = :name and pricesource_id = :source", array(":name" => $product, ":source" => $source));
	 	if(is_null($prd)) {
	 		verbose("Adding a new product - $product to the datastore.");
	 		$s = R::load('pricesource', $source);

	 		$p = R::dispense("products");
	 		$p->name = $product;

	 		$s->ownProducts[] = $p;
	 		R::store($s);
			verbose("Added a new Product {$p->name} to the datastore.", "info");
	 		return $p;
	 	} else {
	 		return $prd;
	 	}
	 }
	 
	 public function getPrice($p, $l = 1, $d = null) {
	 	if(is_null($d)) $d = date('Y-m-d', time());
	 	
	 	$ps = R::findOne('price', 'products_id = :p and location_id = :l and `date` = :d', array(":p"=> $p, ":l" => $l, ":d" => $d));
	 	if(is_null($ps)) {
	 		// Price is not added for today in the datastore
	 		$price = R::dispense('price');
	 		$price->date = $d;
	 		return $price;
	 	} else {
	 		// Just return the got instance
	 		return $ps;
	 	}
	 }
}

