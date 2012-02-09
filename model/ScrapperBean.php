<?php

/**
 *	Default Implementation of the Scrapable interface
 *
 *	@author	Ashwanth Kumar <ashwanth@ashwanthkumar.in>
 */
class ScrapperBean implements Scrapable {
	/**
	 *	Default implementation which will try to save all the properties of the object to the default SQLite datastore
	 */
	public function save() {
		R::selectDatabase('default');
		$object_vars = get_object_vars($this);
		$beanName = strtolower(get_class($this));	// Get the class name and use it as the bean name
		
		$obj = R::dispense($beanName);
		while($var = current($object_vars)) {
			$key = key($object_vars);
			$obj->$key = $this->$key;
			
			next($object_vars);
		}
		R::store($obj);
	}
}

