<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>
/* @var $this <?php echo $this->getControllerClass(); ?> */
/* @var $model <?php echo $this->getModelClass(); ?> */

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#<?php echo $this->class2id($this->modelClass); ?>-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo $this->pluralize($this->class2name($this->modelClass)); ?></h1>

<p>You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.</p>

<?php echo "<?php"; ?> 
	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'<?php echo $this->class2id($this->modelClass); ?>-grid',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'columns'=>$model->gridColumns($buttons = true),
	)); 
?>
