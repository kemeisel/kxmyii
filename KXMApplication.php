<?php

Class KXMApplication Extends CWebApplication
{
	public $urlRules = array();

	/**
	 * init
	 * @brief Initialize the class.
	 */
	protected function init()
	{
	
		// Initializing modules
		$modules = $this->getModules();
		
		foreach( $modules as $id => $config )
		{
			$this->getModule( $id );
		}
		/**
		 * Modules have the chance to add URL mapping, so add them to the 
		 * urlManager configuration. We do this here since urlManager 
		 * doesn't allowing adding rules after initializing.
		 */
		$this->setComponents( array(
			'urlManager' => array(
				'rules' => $this->urlRules
			)
		));

		parent::init();
	}

	/**
	 * run
	 * @brief Runs the application.  This method loads static application components. Derived classes usually override this method to do more application-specific tasks. Remember to call the parent implementation so static application components are loaded.
	 */
	public function run()
	{
		$this->announcer->announce( 'onBeginRequest', $this );
		if( $this->hasEventHandler('onBeginRequest') )
		{
			$this->onBeginRequest( new CEvent($this) );
		}

		$this->processRequest();

		if( $this->hasEventHandler('onEndRequest') )
		{
			$this->onEndRequest( new CEvent($this) );
		}
		$this->announcer->announce( 'onEndRequest', $this );
	}

	/**
	 * registerCoreComponents
	 * @brief Registers the core application components. This method overrides the parent implementation by registering additional core components.
	 * @see setComponents
	 */
	protected function registerCoreComponents()
	{
		parent::registerCoreComponents();

		$components = array(
			'announcer' => array(
				'class' => 'KXMAnnouncer',
			),
		);

		$this->setComponents( $components );
	}
	
	/**
	 * trace_and_die
	 * @brief Dumps the supplied value, killing the process (by default)
	 * @param Mixed The value to dump
	 * @param Boolean Whether to terminate processing (default = true)
	 * @param Boolean Whether to diaplay as HTML comment (default = false)
	 * @param Boolean Whether the value is already formatted for display (defaults to false)
	 */
	public function trace_and_die($val, $die = true, $comment = false, $formatted = false)
	{
		if( $formatted == true ){
			CVarDumper::dump($val, 10, true);
		
		} else {
			echo $comment === true 
				? "\n\n<!--\n"
				: "<pre>";

			CVarDumper::dump($val, 10, true);
			
			echo $comment === true 
				? "\n-->\n\n"
				: "</pre>";
		}
		
		if($die == true)
			die;
	}
	
	/**
	* tnd
	* @brief Convenience wrapper for trace_and_die, conditional upon debug status
	* @param Mixed The value to dump
	* @param Boolean Whether to terminate processing (default = true)
	* @param Boolean Whether to diaplay as HTML comment (default = false)
	* @param Boolean Whether the value is already formatted for display (defaults to false)
	*/
	public function tnd($val, $die = true, $comment = false, $formatted = false)
	{
		if( $this->debug() === true ){
			$this->trace_and_die($val, $die, $comment, $formatted);
		}
	}
	
	/**
	 * debug
	 * @brief Facilitates display of test and development output, dependent upon environmental conditions
	 * @return Boolean Whether the current state satisfies debug conditions.  This is very implementation-dependent and likely needs customization...
	 */
	public function debug()
	{
		// short-circuited for now
		return true;
		
		$ret_val = false;
		
		// Insert statements that return as boolean...
		$ret_val = (
			// dev server...
			strpos($_SERVER['HTTP_HOST'], 'dev.') !== false
			|| 
			// ...test server...
			strpos($_SERVER['HTTP_HOST'], 'test.') !== false
			|| 
			// ...and local server.
			strpos($_SERVER['HTTP_HOST'], 'local.') !== false
		);
		
		return $ret_val;
	}
	
	/**
	 * arrayParam
	 * @brief Returns an existent key value or the default value
	 * @param key String Needle
	 * @param array Array Haystack
	 * @param default Mixed Default return
	 * @param lower Boolean Adjust case to lower
	 * @return Mixed the value found at $key
	 */
	public function arrayParam($key, $array, $default = false, $lower = true)
	{
		// correct case
		if( $lower == true )
			$key = strtolower($key);
		
		if( array_key_exists($key, $array) ){
			// Return the value if it exists...
			return $array[$key];
			
		} else {
			// ...otherwise return the default
			return $default;
		}
	}
	
	/**
	 * systematize
	 * @brief Replaces non-alphanumeric characters with underscores
	 **/
	public function systematize($string)
	{
		return strtolower(preg_replace("/[^\w\d-]/ui", '_', $string));
	}
	
	/**
	 * sxml_append
	 * @brief Appends SimpleXml object to an existing SimpleXml object
	 */
	public function sxml_append(SimpleXMLElement $to, SimpleXMLElement $from)
	{
		$toDom = dom_import_simplexml($to);
		$fromDom = dom_import_simplexml($from);
		$toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
	}
}
