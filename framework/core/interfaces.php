<?php
/**
 * This file contains core interfaces for Yii framework.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * IApplicationComponent is the interface that all application components must implement.
 *
 * After the application completes configuration, it will invoke the {@link init()}
 * method of every loaded application component.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.core
 * @since 1.0
 */
interface IApplicationComponent
{
	/**
	 * Initializes the application component.
	 * This method is invoked after the application completes configuration.
	 */
	public function init();
	/**
	 * @return boolean whether the {@link init()} method has been invoked.
	 */
	public function getIsInitialized();
}

/**
 * ICache is the interface that must be implemented by cache components.
 *
 * This interface must be implemented by classes supporting caching feature.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.caching
 * @since 1.0
 */
interface ICache
{
	/**
	 * Retrieves a value from cache with a specified key.
	 * @param string a key identifying the cached value
	 * @return mixed the value stored in cache, false if the value is not in the cache or expired.
	 */
	public function get($id);
	/**
	 * Stores a value identified by a key into cache.
	 * If the cache already contains such a key, the existing value and
	 * expiration time will be replaced with the new ones.
	 *
	 * @param string the key identifying the value to be cached
	 * @param mixed the value to be cached
	 * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
	 * @param ICacheDependency dependency of the cached item. If the dependency changes, the item is labelled invalid.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	public function set($id,$value,$expire=0,$dependency=null);
	/**
	 * Stores a value identified by a key into cache if the cache does not contain this key.
	 * Nothing will be done if the cache already contains the key.
	 * @param string the key identifying the value to be cached
	 * @param mixed the value to be cached
	 * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
	 * @param ICacheDependency dependency of the cached item. If the dependency changes, the item is labelled invalid.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	public function add($id,$value,$expire=0,$dependency=null);
	/**
	 * Deletes a value with the specified key from cache
	 * @param string the key of the value to be deleted
	 * @return boolean whether the deletion is successful
	 */
	public function delete($id);
	/**
	 * Deletes all values from cache.
	 * Be careful of performing this operation if the cache is shared by multiple applications.
	 */
	public function flush();
}

/**
 * ICacheDependency is the interface that must be implemented by cache dependency classes.
 *
 * This interface must be implemented by classes meant to be used as
 * cache dependencies.
 *
 * Objects implementing this interface must be able to be serialized and unserialized.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.caching
 * @since 1.0
 */
interface ICacheDependency
{
	/**
	 * Evaluates the dependency by generating and saving the data related with dependency.
	 * This method is invoked by cache before writing data into it.
	 */
	public function evaluateDependency();
	/**
	 * @return boolean whether the dependency has changed.
	 */
	public function getHasChanged();
}


/**
 * IStatePersister is the interface that must be implemented by state persister calsses.
 *
 * This interface must be implemented by all state persister classes (such as
 * {@link CStatePersister}.
 *
 * @version $Id$
 * @package system.core
 * @since 1.0
 */
interface IStatePersister
{
	/**
	 * Loads state data from a persistent storage.
	 * @return mixed the state
	 */
	public function load();
	/**
	 * Saves state data into a persistent storage.
	 * @param mixed the state to be saved
	 */
	public function save($state);
}


/**
 * IFilter is the interface that must be implemented by action filters.
 *
 * @version $Id$
 * @package system.core
 * @since 1.0
 */
interface IFilter
{
	/**
	 * Performs the filtering.
	 * This method should be implemented to perform actual filtering.
	 * If the filter wants to continue the action execution, it should call
	 * <code>$filterChain->run()</code>.
	 * @param CFilterChain the filter chain that the filter is on.
	 */
	public function filter($filterChain);
}


/**
 * IAction is the interface that must be implemented by controller actions.
 *
 * @version $Id$
 * @package system.core
 * @since 1.0
 */
interface IAction
{
	/**
	 * Runs the action.
	 * This method is invoked by the controller owning this action.
	 */
	public function run();
	/**
	 * @return string id of the action
	 */
	public function getId();
	/**
	 * @return CController the controller instance
	 */
	public function getController();
}

/**
 * IWebServiceProvider interface may be implemented by Web service provider classes.
 *
 * If this interface is implemented, the provider instance will be able
 * to intercept the remote method invocation (e.g. for logging or authentication purpose).
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.core
 * @since 1.0
 */
interface IWebServiceProvider
{
	/**
	 * This method is invoked before the requested remote method is invoked.
	 * @param CWebService the currently requested Web service.
	 * @return boolean whether the remote method should be executed.
	 */
	public function beforeWebMethod($service);
	/**
	 * This method is invoked after the requested remote method is invoked.
	 * @param CWebService the currently requested Web service.
	 */
	public function afterWebMethod($service);
}