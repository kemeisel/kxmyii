<?php
/**
 * This is the template for generating the model class of a specified table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 */
?>
<?php echo "<?php\n"; ?>

/**
 * This is the model class for table "<?php echo $tableName; ?>".
 *
 * Base class KXMCActiveRecord is required
 */
class <?php echo $modelClass; ?> extends KXMCActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '<?php echo $tableName; ?>';
	}

	/**
	 * @return string the associated controller name
	 */
	public function controllerName()
	{
		// Table names may have underscores... replace that naming 
		// convention with camelCase
		$ret_val = preg_replace("/(_)(.)/e", "strtoupper('\\2')", $this->tableName());
		
		return $ret_val;
	}

	/**
	 * @return array validation rules for model attributes.
	 * Define new validation rules in $locals in the form:
	 * 		array(
	 * 			[attribute list as csv], 
	 * 			[validator', 
	 * 			[[option] => [scenario]]
	 * 		)
	 * 		
	 * Examples of option => scenario:
	 * 		'on' => 'search',
	 * 		'except' => 'auto'
	 * 
	 */
	public function rules()
	{
		// todo Modify the safe rule below to be a bit more restrictive.  
		// This is here to allow for record creation without hassle, but 
		// some hassle is probably a good thing...
		$local = array(
			array(implode(', ', $this->requiredAttributes()), 'safe'),
		);
		
		return array_merge($local, parent::rules());
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
<?php foreach($relations as $name=>$relation): ?>
			<?php echo "'$name' => $relation,\n"; ?>
<?php endforeach; ?>
		);
	}
	
	/**
	 * @return Array GridView columns
	 */
	public function gridColumns($buttons = true)
	{
		// contextAttributes provides a convenient starting place, but you may 
		// also enumerate the attributes that should appear in the grid
		$retVal = $this->contextAttributes();
		
		if( $buttons == true ){
			$retVal[] = array(
				'class'    =>'CButtonColumn',
				'buttons'  => array(
					'view' => array(
						// customize 'view' url here
						'url' => 'Yii::app()->createUrl($data->controllerName()."/view", array("id"=>$data->id))'
					),
					'update' => array(
						// customize 'update' url here
						'url' => 'Yii::app()->createUrl($data->controllerName()."/update", array("id"=>$data->id))'
					),
				),
				'template' =>'{view}  {update}',
			);
		}
		
		return $retVal;
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return parent::attributeLabels($this->tableName());
	}
	
	/**
	 * Returns the CDetailView attributes appropriate for the context
	 */
	public function contextAttributes()
	{
		/*
			It is VERY likely you'll override this function as the parent merely returns all the (filtered) attributes without formatting of any kind.  When you do, you may delete the call to the parent function - it's only here so this placeholder function will return something
		*/
		$local = array(
		);
		
		return array_diff(parent::contextAttributes(), $local);
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;

<?php
foreach($columns as $name=>$column)
{
	if($column->type==='string')
	{
		echo "\t\t\$criteria->compare('$name',\$this->$name,true);\n";
	}
	else
	{
		echo "\t\t\$criteria->compare('$name',\$this->$name);\n";
	}
}
?>

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

<?php if($connectionId!='db'):?>
	/**
	 * getDbConnection
	 * @brief Return the database connection used for this class
	 * @return CDbConnection the database connection used for this class.
	 * @param String the database connection name for this class, defaults to 'db'.
	 *
	 * Consolidation of multiple models under a specific database connection may be achieved by:
	 *   1- changing the value of $conn_id 
	 *
	 *      - OR (useful for multiple classes) -
	 *
	 *   1- creating a new CActiveRecord class that extends KXMCActiveRecord, 
	 *   2- defining getDbConnection in the new class, specifying the correct connection name
	 *   3- changing the base class of this class to the new class
	 *   4- removing this function entirely
	 */
	public function getDbConnection()
	{
		$conn_id = '<?php echo $connectionId ?>';
		
		return self::getCustomDbConnection($conn_id);
	}

<?php endif?>
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return <?php echo $modelClass; ?> the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
