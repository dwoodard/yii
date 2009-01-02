<?php $this->pageTitle=Yii::app()->name . ' - Login'; ?>

<h1>Login</h1>

<div class="yiiForm">
<?php echo CHtml::form(); ?>

<?php echo CHtml::errorSummary($form); ?>

<div class="simple">
<?php echo CHtml::activeLabel($form,'username'); ?>
<?php echo CHtml::activeTextField($form,'username') ?>
</div>

<div class="simple">
<?php echo CHtml::activeLabel($form,'password'); ?>
<?php echo CHtml::activePasswordField($form,'password') ?>
<p class="hint">
Hint: You may login with <tt>demo/demo</tt> or <tt>admin/admin</tt>.
</p>
</div>

<div class="action">
<?php echo CHtml::activeCheckBox($form,'rememberMe'); ?> Remember me next time<br/>
<?php echo CHtml::submitButton('Login'); ?>
</div>

</form>
</div><!-- yiiForm -->