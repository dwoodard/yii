<?php
/**
 * CDbCommandBuilder class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CDbCommandBuilder provides basic methods to create query commands for tables.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.db.schema
 * @since 1.0
 */
class CDbCommandBuilder extends CComponent
{
	private $_schema;
	private $_connection;

	/**
	 * @param CDbSchema the schema for this command builder
	 */
	public function __construct($schema)
	{
		$this->_schema=$schema;
		$this->_connection=$schema->getDbConnection();
	}

	/**
	 * @return CDbConnection database connection.
	 */
	public function getDbConnection()
	{
		return $this->_connection;
	}

	/**
	 * @return CDbSchema the schema for this command builder.
	 */
	public function getSchema()
	{
		return $this->_schema;
	}

	/**
	 * Returns the last insertion ID for the specified table.
	 * @param CDbTableSchema the table metadata
	 * @return mixed last insertion id. Null is returned if no sequence name.
	 */
	public function getLastInsertID($table)
	{
		if($table->sequenceName!==null)
			return $this->_connection->getLastInsertID($table->sequenceName);
		else
			return null;
	}

	/**
	 * Creates a SELECT command for a single table.
	 * @param CDbTableSchema the table metadata
	 * @param CDbCriteria the query criteria
	 * @return CDbCommand query command.
	 */
	public function createFindCommand($table,$criteria)
	{
		$select=is_array($criteria->select) ? implode(', ',$criteria->select) : $criteria->select;
		$sql="SELECT {$select} FROM {$table->rawName}";
		$sql=$this->applyJoin($sql,$criteria->join);
		$sql=$this->applyCondition($sql,$criteria->condition);
		$sql=$this->applyGroup($sql,$criteria->group);
		$sql=$this->applyHaving($sql,$criteria->having);
		$sql=$this->applyOrder($sql,$criteria->order);
		$sql=$this->applyLimit($sql,$criteria->limit,$criteria->offset);
		$command=$this->_connection->createCommand($sql);
		$this->bindValues($command,$criteria->params);
		return $command;
	}

	/**
	 * Creates a COUNT(*) command for a single table.
	 * @param CDbTableSchema the table metadata
	 * @param CDbCriteria the query criteria
	 * @return CDbCommand query command.
	 */
	public function createCountCommand($table,$criteria)
	{
		$criteria->select='COUNT(*)';
		return $this->createFindCommand($table,$criteria);
	}

	/**
	 * Creates a DELETE command.
	 * @param CDbTableSchema the table metadata
	 * @param CDbCriteria the query criteria
	 * @return CDbCommand delete command.
	 */
	public function createDeleteCommand($table,$criteria)
	{
		$sql="DELETE FROM {$table->rawName}";
		$sql=$this->applyJoin($sql,$criteria->join);
		$sql=$this->applyCondition($sql,$criteria->condition);
		$sql=$this->applyGroup($sql,$criteria->group);
		$sql=$this->applyHaving($sql,$criteria->having);
		$sql=$this->applyOrder($sql,$criteria->order);
		$sql=$this->applyLimit($sql,$criteria->limit,$criteria->offset);
		$command=$this->_connection->createCommand($sql);
		$this->bindValues($command,$criteria->params);
		return $command;
	}

	/**
	 * Creates an INSERT command.
	 * @param CDbTableSchema the table metadata
	 * @param array data to be inserted (column name=>column value). If a key is not a valid column name, the corresponding value will be ignored.
	 * @return CDbCommand insert command
	 */
	public function createInsertCommand($table,$data)
	{
		$fields=array();
		$values=array();
		$placeholders=array();
		foreach($data as $name=>$value)
		{
			if(($column=$table->getColumn($name))!==null && ($value!==null || $column->allowNull))
			{
				$fields[]=$column->rawName;
				$placeholders[]=':'.$name;
				$values[':'.$name]=$column->typecast($value);
			}
		}
		$sql="INSERT INTO {$table->rawName} (".implode(', ',$fields).') VALUES ('.implode(', ',$placeholders).')';
		$command=$this->_connection->createCommand($sql);

		foreach($values as $name=>$value)
			$command->bindValue($name,$value);

		return $command;
	}

	/**
	 * Creates an UPDATE command.
	 * @param CDbTableSchema the table metadata
	 * @param array list of columns to be updated (name=>value)
	 * @param CDbCriteria the query criteria
	 * @return CDbCommand update command.
	 */
	public function createUpdateCommand($table,$data,$criteria)
	{
		$fields=array();
		$values=array();
		$bindByPosition=isset($criteria->params[0]);
		foreach($data as $name=>$value)
		{
			if(($column=$table->getColumn($name))!==null)
			{
				if($bindByPosition)
				{
					$fields[]=$column->rawName.'=?';
					$values[]=$column->typecast($value);
				}
				else
				{
					$fields[]=$column->rawName.'=:'.$name;
					$values[':'.$name]=$column->typecast($value);
				}
			}
		}
		if($fields===array())
			throw new CDbException(Yii::t('yii','No columns are being updated for table "{table}".',
				array('{table}'=>$table->name)));
		$sql="UPDATE {$table->rawName} SET ".implode(', ',$fields);
		$sql=$this->applyJoin($sql,$criteria->join);
		$sql=$this->applyCondition($sql,$criteria->condition);
		$sql=$this->applyOrder($sql,$criteria->order);
		$sql=$this->applyLimit($sql,$criteria->limit,$criteria->offset);

		$command=$this->_connection->createCommand($sql);
		$this->bindValues($command,array_merge($values,$criteria->params));

		return $command;
	}

	/**
	 * Creates an UPDATE command that increments/decrements certain columns.
	 * @param CDbTableSchema the table metadata
	 * @param CDbCriteria the query criteria
	 * @param array counters to be updated (counter increments/decrements indexed by column names.)
	 * @return CDbCommand the created command
	 * @throws CException if no counter is specified
	 */
	public function createUpdateCounterCommand($table,$counters,$criteria)
	{
		$fields=array();
		foreach($counters as $name=>$value)
		{
			if(($column=$table->getColumn($name))!==null)
			{
				$value=(int)$value;
				if($value<0)
					$fields[]="{$column->rawName}={$column->rawName}-".(-$value);
				else
					$fields[]="{$column->rawName}={$column->rawName}+".$value;
			}
		}
		if($fields!==array())
		{
			$sql="UPDATE {$table->rawName} SET ".implode(', ',$fields);
			$sql=$this->applyJoin($sql,$criteria->join);
			$sql=$this->applyCondition($sql,$criteria->condition);
			$sql=$this->applyOrder($sql,$criteria->order);
			$sql=$this->applyLimit($sql,$criteria->limit,$criteria->offset);
			$command=$this->_connection->createCommand($sql);
			$this->bindValues($command,$criteria->params);
			return $command;
		}
		else
			throw new CDbException(Yii::t('yii','No counter columns are being updated for table "{table}".',
				array('{table}'=>$table->name)));
	}

	/**
	 * Creates a command based on a given SQL statement.
	 * @param string the explicitly specified SQL statement
	 * @param array parameters that will be bound to the SQL statement
	 * @return CDbCommand the created command
	 */
	public function createSqlCommand($sql,$params=array())
	{
		$command=$this->_connection->createCommand($sql);
		$this->bindValues($command,$params);
		return $command;
	}

	/**
	 * Alters the SQL to apply JOIN clause.
	 * @param string the SQL statement to be altered
	 * @param string the JOIN clause (starting with join type, such as INNER JOIN)
	 * @return string the altered SQL statement
	 */
	public function applyJoin($sql,$join)
	{
		if($join!=='')
			return $sql.' '.$join;
		else
			return $sql;
	}

	/**
	 * Alters the SQL to apply WHERE clause.
	 * @param string the SQL statement without WHERE clause
	 * @param string the WHERE clause (without WHERE keyword)
	 * @return string the altered SQL statement
	 */
	public function applyCondition($sql,$condition)
	{
		if($condition!=='')
			return $sql.' WHERE '.$condition;
		else
			return $sql;
	}

	/**
	 * Alters the SQL to apply ORDER BY.
	 * @param string SQL statement without ORDER BY.
	 * @param string column ordering
	 * @return string modified SQL applied with ORDER BY.
	 */
	public function applyOrder($sql,$orderBy)
	{
		if($orderBy!=='')
			return $sql.' ORDER BY '.$orderBy;
		else
			return $sql;
	}

	/**
	 * Alters the SQL to apply LIMIT and OFFSET.
	 * Default implementation is applicable for PostgreSQL, MySQL and SQLite.
	 * @param string SQL query string without LIMIT and OFFSET.
	 * @param integer maximum number of rows, -1 to ignore limit.
	 * @param integer row offset, -1 to ignore offset.
	 * @return string SQL with LIMIT and OFFSET
	 */
	public function applyLimit($sql,$limit,$offset)
	{
		if($limit>=0)
			$sql.=' LIMIT '.(int)$limit;
		if($offset>0)
			$sql.=' OFFSET '.(int)$offset;
		return $sql;
	}

	/**
	 * Alters the SQL to apply GROUP BY.
	 * @param string SQL query string without GROUP BY.
	 * @param string GROUP BY
	 * @return string SQL with GROUP BY.
	 */
	public function applyGroup($sql,$group)
	{
		if($group!=='')
			return $sql.' GROUP BY '.$group;
		else
			return $sql;
	}

	/**
	 * Alters the SQL to apply HAVING.
	 * @param string SQL query string without HAVING
	 * @param string HAVING
	 * @return string SQL with HAVING
	 * @since 1.0.1
	 */
	public function applyHaving($sql,$having)
	{
		if($having!=='')
			return $sql.' HAVING '.$having;
		else
			return $sql;
	}

	/**
	 * Binds parameter values for an SQL command.
	 * @param CDbCommand database command
	 * @param array values for binding (integer-indexed array for question mark placeholders, string-indexed array for named placeholders)
	 */
	public function bindValues($command, $values)
	{
		if(($n=count($values))===0)
			return;
		if(isset($values[0])) // question mark placeholders
		{
			for($i=0;$i<$n;++$i)
				$command->bindValue($i+1,$values[$i]);
		}
		else // named placeholders
		{
			foreach($values as $name=>$value)
			{
				if($name[0]!==':')
					$name=':'.$name;
				$command->bindValue($name,$value);
			}
		}
	}

	/**
	 * Creates a query criteria.
	 * @param CDbTableSchema the table metadata
	 * @param mixed query condition or criteria.
	 * If a string, it is treated as query condition (the WHERE clause);
	 * If an array, it is treated as the initial values for constructing a {@link CDbCriteria} object;
	 * Otherwise, it should be an instance of {@link CDbCriteria}.
	 * @param array parameters to be bound to an SQL statement.
	 * This is only used when the first parameter is a string (query condition).
	 * In other cases, please use {@link CDbCriteria::params} to set parameters.
	 * @return CDbCriteria the created query criteria
	 * @throws CException if the condition is not string, array and CDbCriteria
	 */
	public function createCriteria($condition='',$params=array())
	{
		if(is_array($condition))
			$criteria=new CDbCriteria($condition);
		else if($condition instanceof CDbCriteria)
			$criteria=clone $condition;
		else
		{
			$criteria=new CDbCriteria;
			$criteria->condition=$condition;
			$criteria->params=$params;
		}
		return $criteria;
	}

	/**
	 * Creates a query criteria with the specified primary key.
	 * @param CDbTableSchema the table metadata
	 * @param mixed primary key value(s). Use array for multiple primary keys. For composite key, each key value must be an array (column name=>column value).
	 * @param mixed query condition or criteria.
	 * If a string, it is treated as query condition;
	 * If an array, it is treated as the initial values for constructing a {@link CDbCriteria};
	 * Otherwise, it should be an instance of {@link CDbCriteria}.
	 * @param array parameters to be bound to an SQL statement.
	 * This is only used when the second parameter is a string (query condition).
	 * In other cases, please use {@link CDbCriteria::params} to set parameters.
	 * @return CDbCriteria the created query criteria
	 */
	public function createPkCriteria($table,$pk,$condition='',$params=array())
	{
		$criteria=$this->createCriteria($condition,$params);
		if(!is_array($pk)) // single key
			$pk=array($pk);
		if(is_array($table->primaryKey) && !isset($pk[0]) && $pk!==array()) // single composite key
			$pk=array($pk);
		$condition=$this->createPkCondition($table,$pk);
		if($criteria->condition!=='')
			$criteria->condition=$condition.' AND ('.$criteria->condition.')';
		else
			$criteria->condition=$condition;

		return $criteria;
	}

	/**
	 * Generates the expression for selecting rows of specified primary key values.
	 * @param CDbTableSchema the table schema
	 * @param array list of primary key values to be selected within
	 * @param string column prefix (ended with dot). If null, it will be the table name
	 * @return string the expression for selection
	 */
	public function createPkCondition($table,$values,$prefix=null)
	{
		if(($n=count($values))<1)
			return '0=1';
		if($prefix===null)
			$prefix=$table->rawName.'.';
		if(is_string($table->primaryKey))
		{
			// simple key: $values=array(pk1,pk2,...)
			$column=$table->columns[$table->primaryKey];
			foreach($values as &$value)
			{
				$value=$column->typecast($value);
				if(is_string($value))
					$value=$this->_connection->quoteValue($value);
			}
			if($n===1)
				return $prefix.$column->rawName.'='.$values[0];
			else
				return $prefix.$column->rawName.' IN ('.implode(', ',$values).')';
		}
		else if(is_array($table->primaryKey))
		{
			// composite key: $values=array(array('pk1'=>'v1','pk2'=>'v2'),array(...))
			foreach($table->primaryKey as $name)
			{
				$column=$table->columns[$name];
				for($i=0;$i<$n;++$i)
				{
					if(isset($values[$i][$name]))
					{
						$value=$column->typecast($values[$i][$name]);
						if(is_string($value))
							$values[$i][$name]=$this->_connection->quoteValue($value);
						else
							$values[$i][$name]=$value;
					}
					else
						throw new CDbException(Yii::t('yii','The value for the primary key "{key}" is not supplied when querying the table "{table}".',
							array('{table}'=>$table->name,'{key}'=>$name)));
				}
			}

			if(count($values)===1)
			{
				$entries=array();
				foreach($values[0] as $name=>$value)
					$entries[]=$prefix.$table->columns[$name]->rawName.'='.$value;
				return implode(' AND ',$entries);
			}
			else
				return $this->createCompositePkCondition($table,$values,$prefix);
		}
		else
			throw new CDbException(Yii::t('yii','Table "{table}" does not have a primary key defined.',
				array('{table}'=>$table->name)));
	}

	/**
	 * Generates the expression for selecting rows with specified composite primary key values.
	 * @param CDbTableSchema the table schema
	 * @param array list of primary key values to be selected within
	 * @param string column prefix (ended with dot)
	 * @return string the expression for selection
	 */
	protected function createCompositePkCondition($table,$values,$prefix)
	{
		if($prefix===null)
			$prefix=$table->rawName.'.';
		$keyNames=array();
		foreach(array_keys($values[0]) as $name)
			$keyNames[]=$prefix.$table->columns[$name]->rawName;
		$vs=array();
		foreach($values as $value)
			$vs[]='('.implode(', ',$value).')';
		return '('.implode(', ',$keyNames).') IN ('.implode(', ',$vs).')';
	}

	/**
	 * Creates a query criteria with the specified column values.
	 * @param CDbTableSchema the table metadata
	 * @param array column values that should be matched in the query (name=>value)
	 * @param mixed query condition or criteria.
	 * If a string, it is treated as query condition;
	 * If an array, it is treated as the initial values for constructing a {@link CDbCriteria};
	 * Otherwise, it should be an instance of {@link CDbCriteria}.
	 * @param array parameters to be bound to an SQL statement.
	 * This is only used when the second parameter is a string (query condition).
	 * In other cases, please use {@link CDbCriteria::params} to set parameters.
	 * @return CDbCriteria the created query criteria
	 */
	public function createColumnCriteria($table,$columns,$condition='',$params=array())
	{
		$criteria=$this->createCriteria($condition,$params);
		$bindByPosition=isset($criteria->params[0]);
		$conditions=array();
		$values=array();
		foreach($columns as $name=>$value)
		{
			if(($column=$table->getColumn($name))!==null)
			{
				if($value!==null)
				{
					if($bindByPosition)
					{
						$conditions[]=$table->rawName.'.'.$column->rawName.'=?';
						$values[]=$value;
					}
					else
					{
						$conditions[]=$table->rawName.'.'.$column->rawName.'=:'.$name;
						$values[':'.$name]=$value;
					}
				}
				else
					$conditions[]=$table->rawName.'.'.$column->rawName.' IS NULL';
			}
			else
				throw new CDbException(Yii::t('yii','Table "{table}" does not have a column named "{column}".',
					array('{table}'=>$table->name,'{column}'=>$name)));
		}
		$criteria->params=array_merge($values,$criteria->params);
		if(isset($conditions[0]))
		{
			if($criteria->condition!=='')
				$criteria->condition=implode(' AND ',$conditions).' AND ('.$criteria->condition.')';
			else
				$criteria->condition=implode(' AND ',$conditions);
		}
		return $criteria;
	}

	/**
	 * Generates the expression for searching the specified keywords within a list of columns.
	 * The search expression is generated using the 'LIKE' SQL syntax.
	 * Every word in the keywords must be present and appear in at least one of the columns.
	 * @param array list of column names for potential search condition.
	 * @param mixed search keywords. This can be either a string with space-separated keywords or an array of keywords.
	 * @param string column prefix (ended with dot). If null, it will be the table name
	 * @return string SQL search condition matching on a set of columns. An empty string is returned
	 * if either the column array or the keywords are empty.
	 */
	public function createSearchCondition($table,$columns,$keywords,$prefix=null)
	{
		if(!is_array($keywords))
			$keywords=preg_split('/\s+/u',$keywords,-1,PREG_SPLIT_NO_EMPTY);
		if(empty($keywords))
			return '';
		if($prefix===null)
			$prefix=$table->rawName.'.';
		$conditions=array();
		foreach($columns as $name)
		{
			if(($column=$table->getColumn($name))===null)
				throw new CDbException(Yii::t('yii','Table "{table}" does not have a column named "{column}".',
					array('{table}'=>$table->name,'{column}'=>$name)));
			$condition=array();
			foreach($keywords as $keyword)
				$condition[]=$prefix.$column->rawName.' LIKE '.$this->_connection->quoteValue('%'.$keyword.'%');
			$conditions[]=implode(' AND ',$condition);
		}
		return '('.implode(' OR ',$conditions).')';
	}
}
