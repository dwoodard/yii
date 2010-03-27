<?php
Yii::app()->clientScript->registerCoreScript('jquery');
Yii::app()->clientScript->registerScriptFile($this->module->assetsUrl.'/js/tools.tooltip-1.1.3.min.js');
Yii::app()->clientScript->registerScriptFile($this->module->assetsUrl.'/js/fancybox/jquery.fancybox-1.3.1.pack.js');
Yii::app()->clientScript->registerCssFile($this->module->assetsUrl.'/js/fancybox/jquery.fancybox-1.3.1.css');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo $this->module->assetsUrl; ?>/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo $this->module->assetsUrl; ?>/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo $this->module->assetsUrl; ?>/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo $this->module->assetsUrl; ?>/css/main.css" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

	<script type="text/javascript" src="<?php echo $this->module->assetsUrl; ?>/js/main.js"></script>

</head>

<body>

<div class="container" id="page">

	<div id="header">
		<div class="top-menus">
		<?php echo CHtml::link('home',array('/gii/default/index')); ?> |
		<a href="http://www.yiiframework.com">yii framework</a>
		<?php if(!Yii::app()->user->isGuest): ?>
			| <?php echo CHtml::link('logout',array('/gii/default/logout')); ?>
		<?php endif; ?>
		</div>
		<div id="logo"><?php echo CHtml::link(CHtml::image($this->module->assetsUrl.'/images/logo.png'),array('/gii')); ?></div>
	</div><!-- header -->

	<?php echo $content; ?>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by <a href="http://www.yiisoft.com">Yii Software LLC</a>.<br/>
		All Rights Reserved.<br/>
		<?php echo Yii::powered(); ?>
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>