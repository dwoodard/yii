<?php
/**
 * CBaseIdentity class file
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CBaseIdentity is a base class implementing {@link IIdentity}.
 *
 * CBaseIdentity implements the scheme for representing identity
 * information that needs to be persisted. It also provides the way
 * to represent the authentication errors.
 *
 * Derived classes should implement {@link IIdentity::authenticate}
 * and {@link IIdentity::getId} that are required by the {@link IIdentity}
 * interface.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.web.auth
 * @since 1.0
 */
abstract class CBaseIdentity extends CComponent implements IIdentity
{
	const ERROR_NONE=0;
	const ERROR_USERNAME_INVALID=1;
	const ERROR_PASSWORD_INVALID=2;
	const ERROR_UNKNOWN=100;

	/**
	 * @var integer the authentication error code. If there is an error, the error code will be non-zero. Defaults to 0.
	 */
	public $errorCode=self::ERROR_NONE;
	/**
	 * @var string the authentication error message. Defaults to empty.
	 */
	public $errorMessage='';

	private $_state=array();

	/**
	 * Returns the identity states that should be persisted.
	 * This method is required by {@link IIdentity}.
	 * @return array the identity states that should be persisted.
	 */
	public function getPersistentStates()
	{
		return $this->_state;
	}

	/**
	 * Returns a value indicating whether the authentication is successful.
	 * This method is required by {@link IIdentity}.
	 * @return whether the authentication is successful.
	 */
	public function getIsValid()
	{
		return $this->errorCode==self::ERROR_NONE;
	}

	/**
	 * Gets the persisted state by the specified name.
	 * @param string the name of the state
	 * @param mixed the default value to be returned if the named state does not exist
	 * @return mixed the value of the named state
	 */
	public function getState($name,$defaultValue=null)
	{
		return isset($this->_state[$name])?$this->_state[$name]:$defaultValue;
	}

	/**
	 * Sets the named state with a given value.
	 * @param string the name of the state
	 * @param mixed the value of the named state
	 * @param mixed the default value for the named state.
	 * If the given value is the same as this value, the state will be removed
	 * from the storage.
	 */
	public function setState($name,$value,$defaultValue=null)
	{
		if($value===$defaultValue)
			unset($this->_state[$name]);
		else
			$this->_state[$name]=$value;
	}
}
