<?php
	/**
	 * KXMMenu
	 *
	 * A generalized menu class with announcement
	 */

	// zii widgets must be explicitly included...
	Yii::import('zii.widgets.CMenu');
	
	class KXMMenu extends CMenu{
		
		public $announce = false;
		
		/**
		 * init
		 * @property $announce bool Whether the menu will announce rendering. Default is false
		 * @property $viewFile string The layout to use for rendering. Default is blank
		 *  
		 * (non-PHPdoc)
		 * @see CWidget::init()
		 */
		public function init()
		{
			if( $this->announce )
			{
				$announcer = Yii::app()->announcer; /* @var $announcer KXMAnnouncer */
				$eventName = 'willRender' . $this->id;
				$announcer->announce( $eventName, $this );
			}
			
			// placed after the announcement so the parent can normalize the items array as expected
			parent::init();
		}
		
	}