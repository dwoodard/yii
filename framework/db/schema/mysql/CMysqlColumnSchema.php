<?php
/**
 * CMysqlColumnSchema class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2009 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CMysqlColumnSchema class describes the column meta data of a MySQL table.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.db.schema.mysql
 * @since 1.0
 */
class CMysqlColumnSchema extends CDbColumnSchema
{
	/**
	 * Extracts the PHP type from DB type.
	 * @param string DB type
	 */
	protected function extractType($dbType)
	{
		if(strncmp($dbType,'enum',4)===0)
			$this->type='string';
		else if(strpos($dbType,'bigint')!==false || strpos($dbType,'float')!==false || strpos($dbType,'double')!==false)
			$this->type='double';
		else if(strpos($dbType,'bool')!==false)
			$this->type='boolean';
		else if(strpos($dbType,'int')!==false || strpos($dbType,'bit')!==false)
			$this->type='integer';
		else
			$this->type='string';
	}

	protected function extractDefault($defaultValue)
	{
		if($this->dbType==='timestamp' && $defaultValue==='CURRENT_TIMESTAMP')
			$this->defaultValue=null;
		else
			parent::extractDefault($defaultValue);
	}
	
	/**
	 * Extracts size, precision and scale information from column's DB type.
	 * @param string the column's DB type
	 */
	protected function extractLimit($dbType)
	{
		if (strncmp($dbType, 'enum', 4)==0){
			if(strpos($dbType,'(') && preg_match('/\((.*)\)/',$dbType,$matches)){
				$values = explode(',', $matches[1]);
				$size = 0;
				foreach($values as $value){
					$size = max($size, strlen($value)-2);
				}
				$this->size = $this->precision = $size;
			}
		}else{
			parent::extractLimit($dbType);
		}
	}
}