<?php
/**
 * CDummyCache class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2009 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CDummyCache is a placeholder cache component.
 *
 * CDummyCache does not do/cache anything. It is used as the default 'cache' application component.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.caching
 * @since 1.0
 */
class CDummyCache extends CApplicationComponent implements ICache, ArrayAccess
{
	/**
	 * Retrieves a value from cache with a specified key.
	 * @param string a key identifying the cached value
	 * @return mixed the value stored in cache, false if the value is not in the cache, expired or the dependency has changed.
	 */
	public function get($id)
	{
		return false;
	}

	/**
	 * Stores a value identified by a key into cache.
	 * If the cache already contains such a key, the existing value and
	 * expiration time will be replaced with the new ones.
	 *
	 * @param string the key identifying the value to be cached
	 * @param mixed the value to be cached
	 * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
	 * @param ICacheDependency dependency of the cached item. If the dependency changes, the item is labeled invalid.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	public function set($id,$value,$expire=0,$dependency=null)
	{
		return true;
	}

	/**
	 * Stores a value identified by a key into cache if the cache does not contain this key.
	 * Nothing will be done if the cache already contains the key.
	 * @param string the key identifying the value to be cached
	 * @param mixed the value to be cached
	 * @param integer the number of seconds in which the cached value will expire. 0 means never expire.
	 * @param ICacheDependency dependency of the cached item. If the dependency changes, the item is labeled invalid.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	public function add($id,$value,$expire=0,$dependency=null)
	{
		return true;
	}

	/**
	 * Deletes a value with the specified key from cache
	 * @param string the key of the value to be deleted
	 * @return boolean if no error happens during deletion
	 */
	public function delete($id)
	{
		return true;
	}

	/**
	 * Deletes all values from cache.
	 * Be careful of performing this operation if the cache is shared by multiple applications.
	 * Child classes may implement this method to realize the flush operation.
	 * @throws CException if this method is not overridden by child classes
	 */
	public function flush()
	{
	}

	/**
	 * Returns whether there is a cache entry with a specified key.
	 * This method is required by the interface ArrayAccess.
	 * @param string a key identifying the cached value
	 * @return boolean
	 */
	public function offsetExists($id)
	{
		return false;
	}

	/**
	 * Retrieves the value from cache with a specified key.
	 * This method is required by the interface ArrayAccess.
	 * @param string a key identifying the cached value
	 * @return mixed the value stored in cache, false if the value is not in the cache or expired.
	 */
	public function offsetGet($id)
	{
		return false;
	}

	/**
	 * Stores the value identified by a key into cache.
	 * If the cache already contains such a key, the existing value will be
	 * replaced with the new ones. To add expiration and dependencies, use the set() method.
	 * This method is required by the interface ArrayAccess.
	 * @param string the key identifying the value to be cached
	 * @param mixed the value to be cached
	 */
	public function offsetSet($id, $value)
	{
	}

	/**
	 * Deletes the value with the specified key from cache
	 * This method is required by the interface ArrayAccess.
	 * @param string the key of the value to be deleted
	 * @return boolean if no error happens during deletion
	 */
	public function offsetUnset($id)
	{
	}
}
