<?php
/*
	KXMCActiveRecord
	Base class for MY active record classes.  This class should be extended for any custom database connections, overriding getCustomDbConnection() in the process
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
	 * model
	 * @brief Returns the static model of the specified AR class.  Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CompanyConfig the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * getId
	 * @brief Retrieve the field name of the primary key of this model, by convention [CLASS NAME]_ID
	 * @return String the field name of the primary key
	 */
	public function getId()
	{
		$field_name = strtoupper($this->tablename() . '_ID');
		return $this->$field_name;
	}
	
	/**
	 * rules
	 * @brief Defines (and returns) validation rules for a model, against which update and create actions are tested.
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		 /*
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
		 */

		// Default rules for attributes set as not nullable:
		return array(
			// The should be required,
			array(implode(', ', $this->requiredAttributes()), 'required'),
			
			// and they should be marked as 'safe' for input
			array(implode(', ', $this->requiredAttributes()), 'safe'),
		);
	}
	
	/**
	 * requiredAttributes
	 * @brief Compiles an array of non-nullable table attributes
	 * @param $tableName String I think this is deprecated, a remnant of an earlier incarnation of the function, but now I'm afraid to remove it...
	 * @return Array Required attributes
	 */
	// KXM Now that we're tapping into $this->getTableSchema(), is $tableName even necessary?
	public function requiredAttributes($tableName = null)
	{
		// Define default (empty) array
		$retVal = array();
		
		// If we haven't already done this...
		if( $this->_requiredAttributes == null ){
			
			// ...then get the schema...
			$schema = $this->getTableSchema();
			
			// ...and cycle over the columns...
			foreach($schema->columns as $name => $attributes){
				// ...(skipping the ID)...
				// KXM Probably better to add 'ID' to the filtered attributes list of contextAttributes() and tap into it there... study on that, as the thinking on primary key field names is morphing...
				if( $name == 'id' )
					continue;
				
				// ...adding any columns for which NULL is not allowed.
				if($attributes->allowNull == false){
					$retVal[$name] = $name;
				}
			}
			
			// Set the cached array
			$this->_requiredAttributes = $retVal;
		}
		
		// Return the (perhaps newly) cached array
		return $this->_requiredAttributes;
	}
	
	/**
	 * attributeLabels
	 * @brief Dynamically creates an array of table attribute/label names
	 * @return Array dynamic attribute labels (name=>label)
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
	 * contextAttributes
	 * @brief Compiles a filtered array of attribute labels appropriate for general use.  Note this will likely be overridden in any derived class, but it does provide a more intellegent starting point
	 * @return Array Returns the CDetailView attributes appropriate for the context
	 */
	public function contextAttributes()
	{
		// Get the complete set
		$all_keys = array_keys($this->attributeLabels());
		
		// Define those keys that are unlikely to be useful or that should not be exposed...
		// KXM This feels kludgey, but it serves the purpose... I could pull it out and stick in in the main config file, but anything beyond that is overkill - most derived classes will override the function anyway...
		$filter = array(
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
		);
		
		// ...and filter em out.
		$retVal = array_diff($all_keys, $filter);
		
		return $retVal;
	}
}
