<?php

/**
 *	Bean used for extracting the content from "All India Level Price Range"
 *	@href	http://agmarknet.nic.in/rep1Newx1_today.asp
 */
class AGMarket extends ScrapperBean {
	/**
	 *	@override	Some custom modifications of the data extracted are required. 
	 */
	 public function save() {
	 	global $siteKeyId;	// Value set from the Main Spider exec.
	 	
	 	// Now since location1 and location2 contains an array value separated by , we need to split them and then combine it
	 	if(isset($this->location1)) $this->location1  = explode(",", $this->location1);
	 	if(isset($this->location2)) $this->location2  = explode(",", $this->location2);
	 	
	 	// I am using RedbeanPHP for PDO Abstraction (http://www.redbeanphp.com/)
	 	$p = $this->getProduct(trim($this->name), $siteKeyId);
		
 		if(isset($this->location1)) {
 			foreach($this->location1 as $l1) {
	 			$l = $this->getLocation($l1);
	 			R::store($l);
	 			$this->savePriceForLocation($l, $p, 1);	// Storing maximum price
 			}
 		}
 		
 		if(isset($this->location2)) {
 			foreach($this->location1 as $l2) {
	 			$l = $this->getLocation($l2);
	 			R::store($l);
	 			$this->savePriceForLocation($l, $p, 2);	// Storing minimum price
 			}
 		}
	 }
	 
	 public function savePriceForLocation($l, $p, $tag) {
	 		$price = $this->getPrice($p->id, $l->id);
	 		
	 		if($price->id < 1) verbose("Adding prices for {$p->name} in {$l->name} on {$price->date} with {$tag}");
	 		else verbose("Updating prices for {$p->name} in {$l->name} on {$price->date} with {$tag}");
	 		
	 		$price->date = date('Y-m-d', time());
	 		$price->price = $this->min_price;
	 		$price->tag = $tag;
	 		
	 		$l->ownPrice[] = $price;
	 		$p->ownPrice[] = $price;
	 		R::store($l);
	 		R::store($p);
	 }
	 
	 public function getLocation($location) {
	 	$loc = R::findOne("location", "name = ?", array($location));
	 	if(is_null($loc)) {
	 		verbose("Adding a new location - $location to the datastore.");
	 		$l = R::dispense("location");
	 		$l->name = trim($location);
	 		R::store($l);
	 		return $l;
	 	} else {
	 		return $loc;
	 	}
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

	 		return $p;
	 	} else {
	 		return $prd;
	 	}
	 }
	 
	 public function getPrice($p, $l, $d = null) {
	 	if(is_null($d)) $d = date('Y-m-d', time());
	 	
	 	$ps = R::findOne('price', 'products_id = :p and location_id = :l and `date` = :d', array(":p"=> $p, ":l" => $l, ":d" => $d));
	 	if(is_null($ps)) {
	 		// Price is not added for today in the datastore
	 		$price = R::dispense('price');
	 		return $price;
	 	} else {
	 		// Just return the got instance
	 		return $ps;
	 	}
	 }
}

