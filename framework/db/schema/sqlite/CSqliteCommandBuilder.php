<?php
/**
 * CSqliteCommandBuilder class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CSqliteCommandBuilder provides basic methods to create query commands for SQLite tables.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.db.schema.sqlite
 * @since 1.0
 */
class CSqliteCommandBuilder extends CDbCommandBuilder
{
	/**
	 * Generates the expression for selecting rows with specified composite primary key values.
	 * This method is overridden because SQLite does not support the default
	 * IN expression with composite columns.
	 * @param CDbTableSchema the table schema
	 * @param array list of primary key values to be selected within
	 * @param string column prefix (ended with dot)
	 * @return string the expression for selection
	 */
	protected function createCompositePkCondition($table,$values,$prefix)
	{
		$keyNames=array();
		foreach(array_keys($values[0]) as $name)
			$keyNames[]=$prefix.$table->columns[$name]->rawName;
		$vs=array();
		foreach($values as $value)
			$vs[]=implode("||','||",$value);
		return implode("||','||",$keyNames).' IN ('.implode(', ',$vs).')';
	}
}
