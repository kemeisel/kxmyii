<?php
/*
	KXMController
	Base class for MY controller classes.
*/
class KXMController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
	
	public function init()
	{
		// Since all controllers should respond to the call to make context navigation, the functionality is factored out to here...
		
		$announcer = Yii::app()->announcer; /* var $announcer KXMAnnouncer */
		$announcer->addListener( $this, 'renderContextMenu', 'willRenderContextMenu' );

		parent::init();
	}
	
	/**
	 * model
	 * @brief Returns the name of the model associated with this controller.  Note - this assumes a single model/single controller paradigm which is (admittedly) pretty narrow.  When the revolution happens, this kind of built-in rigidity will be the first code against the wall...
	 * @return String the controller model
	 */
	public function model()
	{
		$model = ucfirst(strtolower($this->id));
		
		return $model;
	}
	
	/**
	 * filters
	 * @brief Defines and returns Action limits and filters
	 * @return Array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl',      // perform access control for CRUD operations
			'postOnly + delete',  // we only allow deletion via POST request
		);
	}

	/**
	 * accessRules
	 * @brief Specifies the access control rules. This method is used by the 'accessControl' filter.
	 * @return Array access control rules
	 */
	public function accessRules()
	{
		/* FYI:
		 * '*' = all users
		 * '?' = anonymous users
		 * '@' = authenticated users
		 */
		 
		$rules = array(
			// By default all authenticated users may Read
			array(
				'allow',
				'actions'=>array('index','view'),
				'users'=>array('@'),
				
				// KXM This is here to remind me that rules can be asserted by role
				//'roles'=>array('createRoles'),
			),
			
			// Admin users may do anything
			array(
				'allow',
				// omitting 'actions' means it applies to all actions
				'users' => array(
					// Implementing RBAC, this would actually be a call to checkAccess
					'admin'
				),
			),
			
			// By default, deny everything that isn't explicitly allowed.
			array(
				'deny'
			),
		);
		
		// Rules defined here supersede rules defined in the parent (Likewise for all derived classes of *this* class)
		return array_merge($rules, parent::accessRules());
	}

	/**
	 * visibilityRules
	 * @brief Default visibility rules
	 */
	public function visibilityRules()
	{
		// KXM The $user object should be able to handle a null argument, rather than waiting for RBAC implementation - there's no good reason to have to come here to turn nav on and off when it could (should) be handled in the RBAC module.  Get your shit together, dude...
		$rules = array(
			'create' => array(
				true,
				//$user->checkAccess('create'.$label_name)
			),
			'view' => array(
				$this->action->id != 'view',
				//$user->checkAccess('view'.$label_name)
			),
			'update' => array(
				$this->action->id != 'update',
				//$user->checkAccess('update'.$label_name)
			),
			'delete' => array(
				true,
				//$user->checkAccess('delete'.$label_name)
			),
			'index' => array(
				true,
				//$user->checkAccess('view'.$label_name)
			),
			'admin' => array(
				true,
				//$user->checkAccess('view'.$label_name)
			),
			'search' => array(
				true,
				//$user->checkAccess('view'.$label_name)
			),
		);
		
		return $rules;
	}
	
	/**
	 * renderContextMenu
	 * @brief The responder to the context menu creation announcement. Specific controllers have to overload this function for anything to show up in the context menu.
	 * @param KXMAnnouncement The announcement object
	 */
	public function renderContextMenu( KXMAnnouncement $announcement )
	{
		$app         = Yii::app(); /* @var $app KXMApplication */
		
		$user        = $app->user; /* @var $user CWebUser */
		$request     = $app->getRequest();
		$contrlr     = $app->controller;
	
		$route       = $contrlr->getRoute();
		$id          = $request->getParam('id');
		
		$m           = $this->model();
		$label_name  = $m::model()->generateAttributeLabel($m::model()->tableName());
		
		$menu        = $announcement->sender; /* @var $menu KXMMenu */
		//$menu->items = array();
		
		// If the current route references this controller then respond
		if( strpos($route, $this->id) !== FALSE ){
			
			// Index action
			$menu->items[] = array(
				'label' => 'List all '.$label_name,
				'url' => $this->announceUrl(
					'index'
				),
				'visible' => $this->parseVisible('index'),
			);
			
			// Admin action
			$menu->items[] = array(
				'label' => 'Search '.$label_name,
				'url' => $this->announceUrl(
					'search'
				),
				'visible' => $this->parseVisible('search'),
			);
			
			// Create action
			$menu->items[] = array(
				'label' => 'Create new '.$label_name,
				'url' => $this->announceUrl(
					'create'
				),
				'visible' => $this->parseVisible('create')
			);
			
			
			if( isset($id) )
			{
				// View action
				$menu->items[] = array(
					'label' => 'View '.$label_name,
					'url' => $this->announceUrl(
						'view',
						array(
							'id' => $id
						)
					),
					'visible' => $this->parseVisible('view'),
					'template'=>'<hr /> {menu}',
				);

				// Update action
				$menu->items[] = array(
					'label' => 'Update '.$label_name,
					'url' => $this->announceUrl(
						'update',
						array(
							'id' => $id
						)
					),
					'visible' => $this->parseVisible('update'),
					'template'=>'<hr /> {menu}',
				);

				// Delete action
				$menu->items[] = array(
					'label' => 'Delete '.$label_name,
					'url' => 'delete',
					'linkOptions'=>array(
						'submit'=>array(
							'delete',
							'id'=>$id,
						),
						'confirm'=>'Are you sure you want to delete this '.$label_name.'?'
					),
					'visible' => $this->action->id == 'view', // && $user->checkAccess("updateSurvey"),

				);
			}
		}
	}
	
	/**
	 * announceUrl
	 * @brief Creates and raises announcement of a particular Action
	 * @param $name String The action name in question
	 * @param $args Array An array of URL variables
	 * @param $announce Boolean Whether to announce this URL
	 * @return Array Url variables with announcement
	 */
	public function announceUrl( $name, $args = array(), $announce = true )
	{
		// CMenu will expect the first argument to be the action... add it here
		array_unshift($args, $name);
		
		// The announcing sender must be an oject
		$sender = new StdClass;
		// (but what I want is an array)
		$sender->args = $args;
		
		// There is really no reason for announce to be false...
		if( $announce == true ){
			$announcer = Yii::app()->announcer; /* @var $announcer KXMAnnouncer */
			$eventName = 'willBuild' . ucfirst(strtolower($name)) . 'Url';
			$announcer->announce( $eventName, $sender );
		}
		
		return $sender->args;
	}
	
	/**
	 * parseVisible
	 * @brief Parse visibility rules
	 * @param String The name of the item whose visibility is to be evaluated
	 * @return Boolean Whether the item should be displayed
	 */
	public function parseVisible($item)
	{
		// Initialize
		$retVal = true;
		$target = $this->visibilityRules();
		
		if( array_key_exists($item, $target) ){
			// Get pertinent rules
			$args = $target[$item];
			
			// Cycle through them...
			foreach($args as $arg){
				if($arg != true){
					// ...shorting out if any resolve to false
					$retVal = false;
					break;
				}
			}
			
		} else {
			/*
				This is hit when a visibility rule isn't found.  I'm 
				thinking this should be permissive (and therefore do
				nothing), but I'm open to suggestions.  In any case,
				retVal may be subject to any arbitrary test so long
				as it may sensibly evaluate as a boolean...
			*/
		}
		
		// Return the visibility status
		return $retVal;
	}
}
