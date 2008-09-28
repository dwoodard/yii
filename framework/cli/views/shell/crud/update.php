<h2>Update <?php echo $modelClass." <?php echo \${$modelVar}->{$ID}; ?>"; ?></h2>

<div class="actionBar">
[<?php echo "<?php echo CHtml::link('{$modelClass} List',array('list')); ?>"; ?>]
[<?php echo "<?php echo CHtml::link('New {$modelClass}',array('create')); ?>"; ?>]
</div>

<div class="yiiForm">
<?php echo "<?php echo CHtml::form(); ?>\n"; ?>

<?php echo "<?php echo CHtml::errorSummary(\${$modelVar}); ?>\n"; ?>

<?php foreach($columns as $name=>$column): ?>
<div class="simple">
<?php echo "<?php echo CHtml::activeLabel(\${$modelVar},'$name'); ?>\n"; ?>
<?php echo "<?php echo ".$this->generateInputField($model,$modelVar,$column)."; ?>\n"; ?>
</div>
<?php endforeach; ?>

<div class="action">
<?php echo "<?php echo CHtml::submitButton('Save'); ?>\n"; ?>
</div>

</form>
</div><!-- yiiForm -->