<?php
/**
 * CLogRouter class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CLogRouter manages log routes that record log messages in different media.
 *
 * For example, a file log route {@link CFileLogRoute} records log messages
 * in log files. An email log route {@link CEmailLogRoute} sends log messages
 * to specific email addresses. See {@link CLogRoute} for more details about
 * different log routes.
 *
 * Log routes may be configured in application configuration like following:
 * <pre>
 * array(
 *     'preload'=>array('log'), // preload log component when app starts
 *     'components'=>array(
 *         'log'=>array(
 *             'class'=>'CLogRouter',
 *             'routes'=>array(
 *                 array(
 *                     'class'=>'CFileLogRoute',
 *                     'levels'=>'trace, info',
 *                     'categories'=>'system.*',
 *                 ),
 *                 array(
 *                     'class'=>'CEmailLogRoute',
 *                     'levels'=>'error, warning',
 *                     'email'=>'admin@example.com',
 *                 ),
 *             ),
 *         ),
 *     ),
 * )
 * </pre>
 *
 * You can specify multiple routes with different filtering conditions and different
 * targets, even if the routes are of the same type.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.logging
 * @since 1.0
 */
class CLogRouter extends CApplicationComponent
{
	private $_routes=array();

	/**
	 * Initializes this application component.
	 * This method is required by the IApplicationComponent interface.
	 */
	public function init()
	{
		parent::init();
		foreach($this->_routes as $i=>$route)
		{
			if(is_array($route))
			{
				$route=Yii::createComponent($route);
				$route->init();
				$this->_routes[$i]=$route;
			}
		}
		Yii::app()->attachEventHandler('onEndRequest',array($this,'collectLogs'));
	}

	/**
	 * @return array the currently initialized routes
	 */
	public function getRoutes()
	{
		return $this->_routes;
	}

	/**
	 * @param array list of route configurations. Each array element represents
	 * the configuration for a single route and has the following array structure:
	 * <ul>
	 * <li>class: specifies the class name or alias for the route class.</li>
	 * <li>name-value pairs: configure the initial property values of the route.</li>
	 * </ul>
	 */
	public function setRoutes($config)
	{
		foreach($config as $c)
		{
			if(is_string($c))
				$c=array('class'=>$c);
			$this->_routes[]=$c;
		}
	}

	/**
	 * Collects log messages from a logger.
	 * This method is an event handler to application's onEndRequest event.
	 * @param mixed event parameter
	 */
	public function collectLogs($param)
	{
		$logger=Yii::getLogger();
		foreach($this->getRoutes() as $route)
			$route->collectLogs($logger);
	}
}
