<?php
	/*
		KXMCActiveRecord
		Base class for active record classes.  This class should be extended for any custom database connections
	*/
class KXMCActiveRecord extends CActiveRecord
{
	private $_attributeLabels = null;
	private $_requiredAttributes = null;
	
	private static $dbConnection = null;
	
	/**
	 * getCustomDbConnection
	 * @brief Returns a database connection, either as specified or 'db'
	 * @param $dbConnection String The name of a database connection component as defined in config/main.php
	 * @return CDbConnection An active database connection
	 * @throws CDbException If $dbConnection specifies a non-existent component or if the component can't be activated
	 */
	protected static function getCustomDbConnection($dbConnection = null)
	{
		// Supplied argument supersedes cache
		if( $dbConnection !== null ){
			self::$dbConnection = Yii::app()->$dbConnection;
		}
		
		// Use the cached copy if not null
		if( self::$dbConnection !== null ){
			return self::$dbConnection;
			
		} else {
			// This is where the magic happens... Yii::app()->[connection name] references 
			// a database connection component in the main config file...
			self::$dbConnection = Yii::app()->db;
			
			if( self::$dbConnection instanceof CDbConnection ){
				self::$dbConnection->setActive(true);
				return self::$dbConnection;
				
			} else {
				throw new CDbException(Yii::t('yii','Active Record requires a CDbConnection application component.'));
			}
		}
	}
	
	/**
	 * getDbConnection
	 * @brief Returns the appropriate database connection for an active record.  Individual models may override this function or extend this base class the better to provide a single point of change for all derived classes should the connection terms change...
	 */
	public function getDbConnection()
	{
		// All models that inherit this CActiveRecord class will get the custom database connection
		return self::getCustomDbConnection();
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CompanyConfig the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @return String the field name of the primary key, by convention [CLASS NAME]_ID
	 */
	public function getId()
	{
		$field_name = strtoupper($this->tablename() . '_ID');
		return $this->$field_name;
	}
	
	/**
	 * @return array validation rules for model attributes.
	 * Define validation rules in the form:
	 * 		array(
	 * 			[attribute list as csv], 
	 * 			[validator], 
	 * 			[option] => [scenario]
	 * 		)
	 * 		
	 * Examples of option => scenario:
	 * 		'on' => 'search',
	 * 		'except' => 'auto'
	 * 
	 */
	public function rules()
	{
		return array(
			// Default rules for attributes set as not nullable:
			array(implode(', ', $this->requiredAttributes()), 'required'),
			array(implode(', ', $this->requiredAttributes()), 'safe'),
		);
	}
	
	/**
	 * @return array of required attributes
	 */
	public function requiredAttributes($tableName = null)
	{
		$retVal = array();
		
		if( $this->_requiredAttributes == null ){
			$schema = $this->getTableSchema();
			foreach($schema->columns as $name => $attributes){
				if( $name == 'id' )
					continue;

				if($attributes->allowNull == false){
					$retVal[$name] = $name;
				}
			}
			
			$this->_requiredAttributes = $retVal;
		}
		
		return $this->_requiredAttributes;
	}
	
	/**
	 * @return array dynamic attribute labels (name=>label)
	 **/
	public function attributeLabels()
	{
		$retVal = array();
		
		if( $this->_attributeLabels == null ){
			$schema = $this->getTableSchema();
			foreach($schema->columns as $name => $attributes){
				$label = $this->generateAttributeLabel($name);
				$retVal[$name] = $label;
			}
			
			$this->_attributeLabels = $retVal;
		}
		
		return $this->_attributeLabels;
	}
	
	/**
	 * Returns the CDetailView attributes appropriate for the context
	 */
	public function contextAttributes()
	{
		// Get the complete set
		$all_keys = array_keys($this->attributeLabels());
		
		// Define those keys that are unlikely to be useful or that 
		// should not be exposed...
		$filter = array(
			/**/
			'CREATED_BY',
			'CREATE_BY',
			//'CREATE_DT',
			'DATE_CREATE',
			'DATE_CREATED',
			'DATE_UPDATE',
			'DATE_UPDATED',
			'DT_CREATE',
			'DT_CREATED',
			'DT_UPDATE',
			'DT_UPDATED',
			'UPDATED_BY',
			'UPDATE_BY',
			'UPDATE_DT',
			'create_by',
			'create_dt',
			'created_by',
			'date_create',
			'date_created',
			'date_update',
			'date_updated',
			'dt_create',
			'dt_created',
			'dt_update',
			'dt_updated',
			'update_by',
			'update_dt',
			'updated_by',
			/**/
		);
		
		// ...and filter em out.
		$retVal = array_diff($all_keys, $filter);
		
		return $retVal;
	}
}
