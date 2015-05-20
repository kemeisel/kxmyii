<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>
	/* @var $this <?php echo $this->getControllerClass(); ?> */
	/* @var $dataProvider CActiveDataProvider */
?>
<?php
$label=$this->pluralize($this->class2name($this->modelClass));
?>

<h2><?php echo $label; ?></h2>

<?php echo "<?php"; ?>
	$this->widget('zii.widgets.grid.CGridView', array(
		'dataProvider'=>$dataProvider,
		'columns'=><?php echo $this->modelClass; ?>::model()->gridColumns(true)
	)); 
?>
