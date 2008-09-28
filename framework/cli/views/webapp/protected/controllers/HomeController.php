<?php

class HomeController extends CController
{
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		// renders the view file 'protected/views/home/index.php'
		// using the default layout 'protected/views/layouts/application.php'
		$this->render('index');
	}
}