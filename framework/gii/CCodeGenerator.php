<?php

class CCodeGenerator extends Controller
{
	public $layout='generator';
	public $codeModel;

	private $_viewPath;
	private $_templatePath;

	public function init()
	{
		parent::init();
		$this->breadcrumbs=array(ucwords($this->id.' generator'));
	}

	public function getViewPath()
	{
		if($this->_viewPath===null)
		{
			$class=new ReflectionClass(get_class($this));
			$this->_viewPath=dirname($class->getFileName()).DIRECTORY_SEPARATOR.'views';
		}
		return $this->_viewPath;
	}

	public function setViewPath($value)
	{
		$this->_viewPath=$value;
	}

	public function getTemplatePath()
	{
		if($this->_templatePath===null)
		{
			$class=new ReflectionClass(get_class($this));
			$this->_templatePath=dirname($class->getFileName()).DIRECTORY_SEPARATOR.'templates';
		}
		return $this->_templatePath;
	}

	public function setTemplatePath($value)
	{
		$this->_templatePath=$value;
	}

	public function renderGenerator($model)
	{
		$this->renderPartial('gii.views.common.generator', array('model'=>$model));
	}

	public function getSuccessMessage($model)
	{
		return 'The code has been generated successfully.';
	}

	public function actionIndex()
	{
		if($this->codeModel===null)
			throw new CException(get_class($this).'.codeModel property must be specified.');
		$this->generate($this->codeModel, 'index');
	}

	public function actionCode()
	{
		if($this->codeModel===null)
			throw new CException(get_class($this).'.codeModel property must be specified.');
		if(!isset($_GET['id']))
			throw new CHttpException(404,'Unable to find the code you requested.');
		$this->view($this->codeModel, $_GET['id']);
	}

	protected function view($modelClass, $code)
	{
		$modelClass=Yii::import($modelClass,true);
		$model=new $modelClass;
		if(isset($_POST[$modelClass]))
		{
			$model->attributes=$_POST[$modelClass];
			$model->status=CCodeModel::STATUS_PREVIEW;
			if($model->validate())
			{
				$model->prepare($this->getTemplatePath());
				if(isset($model->files[$code]))
				{
					echo $model->files[$code]->content;
					return;
				}
			}
		}
		throw new CHttpException(404,'Unable to find the code you requested.');
	}

	protected function generate($modelClass, $view)
	{
		$modelClass=Yii::import($modelClass,true);
		$model=new $modelClass;
		if(isset($_POST[$modelClass]))
		{
			$model->attributes=$_POST[$modelClass];
			$model->status=CCodeModel::STATUS_PREVIEW;
			if($model->validate())
			{
				$model->prepare($this->getTemplatePath());
				if(isset($_POST['generate'], $_POST['answers']))
				{
					$model->answers=$_POST['answers'];
					$model->status=$model->save() ? CCodeModel::STATUS_SUCCESS : CCodeModel::STATUS_ERROR;
				}
			}
		}

		$this->render($view,array(
			'model'=>$model,
		));
	}
}