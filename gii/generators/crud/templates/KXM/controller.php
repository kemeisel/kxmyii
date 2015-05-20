<?php
/**
 * This is the template for generating a controller class file for CRUD feature.
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>

/**
 * <?php echo $this->controllerClass; ?>
 * A controller class for model <?php echo $this->modelClass; ?>.  Additional models may be included per need.
 **/
class <?php echo $this->controllerClass; ?> extends KXMController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * The cached model, leveraged by loadModel
	 */
	public $_model = null;
	
	/**
	 * init
	 * @brief Initializes the controller, adding key event listeners
	 */
	public function init()
	{
		// Listeners may not be necessary, but here they are
		$announcer = Yii::app()->announcer; /* var $announcer KXMAnnouncer */
		$announcer->addListener( $this, 'buildCreateUrl', 'willBuildCreateUrl' );
		$announcer->addListener( $this, 'buildViewUrl',   'willBuildViewUrl' );
		$announcer->addListener( $this, 'buildUpdateUrl', 'willBuildUpdateUrl' );
		
		parent::init();
	}
	
	/**
	 * loadModel
	 * @brief Returns the data model based on the primary key given in the GET variable. If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return <?php echo $this->modelClass; ?> the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$Model = $this->model();
		if( $this->_model === null ){
			// If the cached copy isn't yet set then set it
			$this->_model = $Model::model()->findByPk($id);
		}
		
		if( $this->_model === null ){
			// If it's still null then it wasn't found... throw an error...
			throw new CHttpException(404,'The requested <?php echo $this->modelClass; ?> object can not be found.');
		}

		return $this->_model;
	}

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		$rules = array(
			// This is where Controller-specific rules are defined.
			// They would be in the form:
			/** /
			array(
				'allow',
				'actions'=>array('index','view'),
				'users'=>array('*'),
				'roles'=>array('Platform_Admin'),
			),
			/**/
			// ... or something very much like that...
		);
		
		return array_merge($rules, parent::accessRules());
	}

	/**
	 * Local visibility rules
	 */
	public function visibilityRules()
	{
		/* 
			This array contains visibility rules specific to this controller
			in the form:
			[controller action] = array(
				[snippet that evaluates to boolean]
			),
			
			There may be many snippets.  Visibility defaults to true and shorts 
			out at first false evaluation.  Local rules supersede parent rules.
		*/
		$visibility = array(
		);
		
		$retVal = array_merge_recursive($visibility, parent::visibilityRules());
		return $retVal;
	}
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$model = $this->loadModel($id);
		
		$this->render('view',array(
			'model' => $model,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model = new <?php echo $this->modelClass; ?>;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['<?php echo $this->modelClass; ?>']))
		{
			try{
				$model->attributes=$_POST['<?php echo $this->modelClass; ?>'];
				
				if($model->save()){
					$this->redirect(array(
						'view',
						'id' => $model-><?php echo $this->tableSchema->primaryKey; ?>
					));
				}
				
			} catch(CDbException $e){
				$model->addError(null, $e->errorInfo[2]);
			}
		}

		$this->render('create',array(
			'model' => $model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model = $this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['<?php echo $this->modelClass; ?>']))
		{
			$model->attributes=$_POST['<?php echo $this->modelClass; ?>'];
			if($model->save()){
				$this->redirect(array(
					'view',
					'id' => $model-><?php echo $this->tableSchema->primaryKey; ?>
				));
			}
		}

		$this->render('update',array(
			'model' => $model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax'])){
			$this->redirect(
				isset($_POST['returnUrl']) 
					? $_POST['returnUrl'] 
					: array('search')
				);
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$criteria = new CDbCriteria(array(
		));
		
		$dataProvider=new CActiveDataProvider('<?php echo $this->modelClass; ?>', array(
			'criteria' => $criteria,
			'sort'=>array(
				// todo: Correct the sort column as needed
				//'defaultOrder'=>strtoupper(<?php echo $this->modelClass; ?>).'NAME ASC',
			),
			'pagination' => array(
				'pageSize' => 20,
			),
		));
		
		$this->render('index',array(
			'dataProvider' => $dataProvider,
		));
	}

	/**
	 * Search for models
	 */
	public function actionSearch()
	{
		$Name = '<?php echo $this->modelClass; ?>';
		$model = new $Name('search');
		$model->unsetAttributes(); // clear any default values
		if( isset($_GET[$Name]) )
			$model->attributes = $_GET[$Name];
			
		$this->render('search', array(
			'model' => $model,
		));
	}

	/**
	 * Performs the AJAX validation.
	 * @param <?php echo $this->modelClass; ?> $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='<?php echo $this->class2id($this->modelClass); ?>-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	/**
	 * Responds to announcement to willBuildCreateUrl 
	 */
	public function buildCreateUrl( KXMAnnouncement $announcement )
	{
		$locals  = array();
		$sender  = $announcement->sender;
		
		$request = Yii::app()->getRequest();
		$id      = $request->getParam('id');
		
		// Condition block for URL variable addition or mapping
		/** /
		if( !empty($request->getParam('boo')) )
			$locals['boo'] = $request->getParam('boo');
		/**/
		
		$sender->args = array_merge($sender->args, $locals);
	}
	
	/**
	 * Responds to announcement to willBuildViewUrl 
	 */
	public function buildViewUrl( KXMAnnouncement $announcement )
	{
	}
	
	/**
	 * Responds to announcement to willBuildUpdateUrl 
	 */
	public function buildUpdateUrl( KXMAnnouncement $announcement )
	{
	}
	
	/**
	 * renderContextMenu
	 * The responder to the context menu creation announcement.  
	 * Specific KXMControllers have to overload this function 
	 * for anything to show up in the context menu.
	 */
	public function renderContextMenu( KXMAnnouncement $announcement )
	{
		$app         = Yii::app(); /* @var $app KXMApplication */
		$request     = $app->getRequest();
		$id          = $request->getParam('id');
		$menu        = $announcement->sender; /* @var $menu KXMMenu */
		
		// Prepend the default menu set:
		/** /
		$menu->items[] = array(
			'label' => 'View Something',
			'url' => $this->announceUrl(
				'something/view',
				array(
					'id' => $account,
				)
			),
			'visible' => $this->parseVisible('viewParentAccount'),
			//'template' =>'<hr /> {menu}',
		);
		/**/
		
		// Get the default menu set:
		parent::renderContextMenu( $announcement );
		
		// Append the default menu set:
		/** /
		$menu->items[] = array(
			'label' => 'Return somewhere',
			'url' => $this->announceUrl(
				'view',
				array(
					'id' => $id,
				)
			),
			'visible' => $this->parseVisible('viewSomewhere'),
			'template' =>'<hr /> {menu}',
		);
		/**/
	}
}
