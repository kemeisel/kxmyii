<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>
	/* @var $this <?php echo $this->getControllerClass(); ?> */
	/* @var $model <?php echo $this->getModelClass(); ?> */
?>

<h2>View <?php echo $this->modelClass; ?></h2>

<?php echo "<?php"; ?> 
	$this->widget('zii.widgets.CDetailView', array(
		'data'=>$model,
		'attributes'=> $model->contextAttributes()
	)); 
?>
