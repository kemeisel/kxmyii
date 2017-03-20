<?php
	/**
	 * KXMAnnouncer 
	 *
	 * The main handler for system event notification between objects.
	 *
	 * Examples:
	 * Yii::app()->announcer->addListener( $this, 'callback', 'eventName' );
	 * Yii::app()->announcer->announce( 'eventName', $this );
	 */
	class KXMAnnouncer 
		extends CApplicationComponent
	{
		protected $_listeners = array();

		/**
		 * @param string $eventName - name of the event $sender is announcing
		 * @param object $sender - object announcing the event
		 * @param KXMAnnouncement $announcement - object containing
		 * @return void
		 */
		public function announce(
			$eventName, 
			$sender, 
			$eventData = NULL
		){
			$announcer_id = spl_object_hash($sender);
			$listeners = array();

			if( ! empty( $this->_listeners[$eventName][0] ) )
			{
				$listeners = array_values( $this->_listeners[$eventName][0] );
			}
			if( ! empty( $this->_listeners[$eventName][$announcer_id] ) )
			{
				$listeners = array_merge( $listeners, array_values( $this->_listeners[$eventName][$announcer_id] ));
			}

			if( count($listeners) > 0 ){
				$announcement = new KXMAnnouncement( $eventName, $sender, $eventData );
				foreach( $listeners as $listener )
				{
					if( 
						method_exists( $listener['object'], $listener['callback'] ) 
					){
						$listener['object']->$listener['callback']( $announcement );
			
					} else {
						throw new CException(Yii::t(
							'KXM',
							'Event "{class}.{method}" is not defined.',
							array( '{class}' => get_class($listener['object']), '{method}' => $listener['callback'] )
						));
					}
					if( $announcement->handled ){
						break;
					}
				}
			}
		}


		/**
		 * @param object $listener - object that gets called when event is announced
		 * @param string $callback - method in $listener object that is called
		 * @param string $eventName - name of the event the $listener is observing
		 * @param object $announcer (optional) - only listen when this object announces
		 * @return void
		 */
		public function addListener( 
			$listener, 
			$callback, 
			$eventName, 
			$announcer = NULL 
		){
			if( ! is_object( $listener ) ){
				throw new CException( Yii::t( 
					'KXM',
					'Listener must be an object for {event} event.',
					array( '{event}' => $eventName )
				));
			}
			$object_id = spl_object_hash($listener);
			$announcer_id = ($announcer != NULL) ? spl_object_hash($announcer) : 0;

			$this->_listeners[$eventName][$announcer_id][$object_id] = array( 
				'object'    => $listener,
				'callback'  => $callback,
				'announcer' => $announcer,
			);
		}


		/**
		 * @param object $listener - object that gets called when event is announced
		 * @param string $eventName - name of the event the $listener is observing
		 * @param object $announcer (optional) - remove only listeners tied to a specific announcer
		 * @return void
		 */
		public function removeListener( 
			$listener, 
			$eventName, 
			$announcer = NULL 
		){
			$object_id = spl_object_hash($listener);
			$announcer_id = ($announcer != NULL) ? spl_object_hash($announcer) : 0;
			if( 
				! empty( $this->_listeners[$eventName][$announcer_id][$object_id] ) 
			){
				unset( $this->_listeners[$eventName][$announcer_id][$object_id] );
			}
		}
	}


	/**
	 * KXMAnnouncement creates an object to handle
	 * communication between the Announcer and Listeners.
	 */
	class KXMAnnouncement 
		extends CEvent
	{
		/**
		 * name of the event
		 * @var string
		 */
		public $eventName;

		/**
		 * object announcing the event
		 * @var object
		 */
		public $sender;

		/**
		 * provides data related to the event
		 * @var mixed
		 */
		public $eventData;

		/**
		 * whether the event is handled - when a handler sets this true, 
		 * the rest uninvoked handlers will not be invoked anymore
		 * (default = FALSE)
		 * @var boolean
		 */
		public $handled = FALSE;

		/**
		 * Constructor.
		 * @param mixed sender of the event (required)
		 * @param mixed data related to the event
		 */
		public function __construct( $eventName, $sender, $eventData = NULL )
		{
			$this->eventName = $eventName;
			$this->sender    = $sender;
			$this->eventData = $eventData;
		}

	}


