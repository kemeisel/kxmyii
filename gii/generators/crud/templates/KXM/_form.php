<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>
/* @var $this <?php echo $this->getControllerClass(); ?> */
/* @var $model <?php echo $this->getModelClass(); ?> */
/* @var $form CActiveForm */
?>

<div class="form">

<?php echo "<?php \$form=\$this->beginWidget('CActiveForm', array(
	'id'=>'".$this->class2id($this->modelClass)."-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>\n"; ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo "<?php echo \$form->errorSummary(\$model); ?>\n"; ?>
	
	<?php echo "<?php foreach(\$model->contextAttributes() as \$column){ 
	\$html_options = array();
	
	// First, get the column object...
	\$thisColumn = \$model->tableSchema->columns[\$column];
	
	// ...and see if this column is the primary key.
	if(\$thisColumn->isPrimaryKey) { 
		// If so, then set the read-only html option:
		\$html_options['readOnly'] = 'readOnly';
	}
	
	// In any case, display the generic text-box input
	?>\n"; ?>
	<?php echo "\t<?php echo \$form->labelEx(\$model, \$column); ?>\n"; ?>
	<?php echo "\t<?php echo \$form->textField(\$model, \$column); ?>\n"; ?>
	<?php echo "\t<?php echo \$form->error(\$model, \$column); ?>\n"; ?>
	<?php echo "<?php } // end foreach ?>\n"; ?>

	<div class="row buttons">
		<?php echo "<?php echo CHtml::submitButton(\$model->isNewRecord ? 'Create' : 'Save'); ?>\n"; ?>
	</div>

<?php echo "<?php \$this->endWidget(); ?>\n"; ?>

</div><!-- form -->