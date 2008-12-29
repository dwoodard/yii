<?php
/**
 * CSqlMap class file.
 *
 * @author Wei Zhuo <weizhuo@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2009 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CSqlMap is the fascade class to the SqlMap database mapping
 * solution.
 *
 * @author Wei Zhuo <weizhuo@gmail.com>
 * @version $Id$
 * @package system.db.sqlmap
 * @since 1.1
 */
class CSqlMap extends CApplicationComponent
{
    /**
     * @var CDbConnection the database connection
     * for the data mapper. By default, this is the 'db'
     * application component.
     * @see getDbConnection
     */
    public $db;

    /**
     * @var CSqlMapConfig sqlmap configuration containing
     * file path information and mapping data.
     * @see CSqlMapConfig
     */
    public $config;

    /**
     * Constructor.
     * @param CDbConnection database connection for this new data mapper.
     * If not specified, the application component named 'db' will be used.
     * @param CSqlMapConfig mapping configurations
     * @see setDbConnection
     * @see CSqlMapConfig
     */
    public function __construct($dbConnection=null, $config=null)
    {
        $this->db=$dbConnection;
        $this->config=$config!==null?$config:new CSqlMapConfig();
    }

    /**
     * @param string base directory of mapping configuration files.
     * @see CSqlMapConfig::basePath
     */
    public function setBasePath($path)
    {
        $this->config->basePath=$path;
    }

    /**
     * @return string base directory of mapping configuration files.
     * @see CSqlMapConfig::basePath
     */
    public function getBasePath()
    {
        return $this->config->basePath;
    }

    /**
     * Returns the database connection used by the data mapper.
     * Obtains default application database connection when $db is null.
     * @return CDbConnection current database connection
     */
    public function getDbConnection()
    {
        if($this->db!==null)
            return $this->db;
        else
        {
            $this->db=Yii::app()->getDb();
            if($this->db instanceof CDbConnection)
            {
                $this->db->setActive(true);
                return $this->db;
            }
            else
            {
                throw new CDbException(Yii::t('yii',
                    'SqlMap requires a "db" CDbConnection application component.'));
            }
        }
    }

    /**
     * @param string|CDbConnection when $config is a string the application component
     * with key name given by $config is used. $db is set to $config when $config
     * is an instance of CDbConnection.
     * @see CDbConnection
     */
    public function setDbConnection($config)
    {
        if(is_string($config))
            $this->db=Yii::app()->getComponent($config);
        else if($config instanceof CDbConnection)
            $this->db=$config;
    }

    /**
     * @param string mapping key, see {@link CSqlMapConfig::getMappingById} for details. 
     * @return array mapping data corresponding to the mapping key.
     * @see CSqlMapConfig::getMappingById
     * @see CSqlMapConfig::resolveMappingKey
     */
    public function getMappingById($str)
    {
        return $this->config->getMappingById($str);
    }

    /**
     * Executes a Sql SELECT statement that returns data
     * to populate a single object instance.
     *
     * The argument $params is generally used to supply the input
     * data for the WHERE clause parameter(s) of the SELECT statement.
     * 
     * @param mixed query id or criteria.
     * If a string, it is treated as query id;
     * If an array, it is treated as the initial values for constructing a {@link CSqlMapCriteria} object;
     * Otherwise, it should be an instance of {@link CSqlMapCriteria}.
     * @param array parameters to be bound to the query SQL statement.
     * This is only used when the first parameter is a string (query id).
     * In other cases, please use {@link CSqlMapCriteria::params} to set parameters.
     * @return mixed the query result. Null if no record is found.
     */
    public function find($id, $params=array())
    {
        $mapping=$this->getMappingById($id);
        $stm=$this->getDbConnection()->createCommand($mapping['sql']);
        $result=array();
        foreach($stm->query() as $row)
            $result[] = $row;
        return $row;
    }

     /**
     * Executes a Sql SELECT statement that returns data
     * to populate a number of result objects.
     *
     * The argument $params is generally used to supply the input
     * data for the WHERE clause parameter(s) of the SELECT statement.
      *
     * @param mixed query id or criteria.
     * If a string, it is treated as query id;
     * If an array, it is treated as the initial values for constructing a {@link CSqlMapCriteria} object;
     * Otherwise, it should be an instance of {@link CSqlMapCriteria}.
     * @param array parameters to be bound to the query SQL statement.
     * This is only used when the first parameter is a string (query id).
     * In other cases, please use {@link CSqlMapCriteria::params} to set parameters.
     * @return array the query result.
     */
    public function findAll($id, $params=array())
    {
    }

    /**
     * Executes a Sql INSERT statement.
     *
     * Insert is a bit different from other update methods, as it provides
     * facilities for returning the primary key of the newly inserted row
     * (rather than the effected rows),
     *
     * The argument $params is generally used to supply the input data for the
     * INSERT values.
     *
     * @param mixed query id or criteria.
     * If a string, it is treated as query id;
     * If an array, it is treated as the initial values for constructing a {@link CSqlMapCriteria} object;
     * Otherwise, it should be an instance of {@link CSqlMapCriteria}.
     * @param array parameters to be bound to the query SQL statement.
     * @return mixed The primary key of the newly inserted row.
     * This might be automatically generated by the RDBMS,
     * or selected from a sequence table or other source.
     */
    public function insert($id, $params=array())
    {        
    }

    /**
     * Executes a Sql UPDATE statement.
     *
     * Update can also be used for any other update statement type, such as
     * inserts and deletes.  Update returns the number of rows effected.
     *
     * The parameter object is generally used to supply the input data for the
     * UPDATE values as well as the WHERE clause parameter(s).
     *
     * @param mixed query id or criteria.
     * If a string, it is treated as query id;
     * If an array, it is treated as the initial values for constructing a {@link CSqlMapCriteria} object;
     * Otherwise, it should be an instance of {@link CSqlMapCriteria}.
     * @param array parameters to be bound to the query SQL statement.
     * @return int The number of rows effected.
     */
    public function update($id, $params=array())
    {
    }

    /**
     * Executes a Sql DELETE statement.  Delete returns the number of rows effected.
     *
     * The argument $params is generally used to supply the input
     * data for the WHERE clause parameter(s) of the DELETE statement.
     *
     * @param mixed query id or criteria.
     * If a string, it is treated as query id;
     * If an array, it is treated as the initial values for constructing a {@link CSqlMapCriteria} object;
     * Otherwise, it should be an instance of {@link CSqlMapCriteria}.
     * @param array parameters to be bound to the query SQL statement.
     * @return int The number of rows effected.
     */
    public function delete($id, $params=array())
    {
    }
}

/**
 * CSqlMapConfig providing caching for mapping configurations.
 *
 * @author Wei Zhuo <weizhuo@gmail.com>
 * @version $Id$
 * @package system.db.sqlmap
 * @since 1.1
 */
class CSqlMapConfig
{
    /**
     * Mapping configuration file extension.
     */
    const MAPPING_EXT='.php';

   /**
     * @var string base directory of the mapping configuration files.
     */
    public $basePath='.';

    /**
     * @var string default sqlmap mapping configuration file name.
     */
    public $defaultMappingFile='sqlmap';

    /**
     * @var array mapping configurations.
     */
    protected $_mappings=array();

    /**
     * @param string mapping file name without '.php' extension {@see MAPPING_EXT}.
     * @return string fullpath of the mapping file, returns default mapping file
     * if mapping file parameter is empty.
     */
    protected function getMappingFile($mapping=null)
    {
        if(empty($mapping))
            $mapping=$this->defaultMappingFile;
        return $this->basePath.DIRECTORY_SEPARATOR.$mapping.self::MAPPING_EXT;
    }

    /**
     * @param string mapping key of the form 'path.to.file.array_key'
     * where 'path.to.file' is a file named 'file' with '.php' extension and
     * is in the 'path.to' directory (replacing dots with directory separators)
     * relative to the {@see basePath} directory. If the file name is omitted
     * the default mapping configuration file name given by $defaultMappingFile
     * is used.
     * @return array array($file, $id) where $file is the mapping data file name
     * and $id as the array key.
     */
    public function resolveMappingKey($str)
    {
        $parts=explode('.', $str);
        $id=array_pop($parts);
        $file=$this->getMappingFile(implode(DIRECTORY_SEPARATOR, $parts));
        return array($file, $id);
    }

    /**
     * @param string mapping key, see {@link resolveMappingKey} for details.
     * @return array mapping data corresponding to the mapping key. Null if not exists.
     */
    public function getMappingById($str)
    {
        list($file, $id)=$this->resolveMappingKey($str);
        if(!isset($this->_mappings[$file]))
            $this->_mappings[$file]=include($file);
        if(isset($this->_mappings[$file][$id]))
            return $this->_mappings[$file][$id];
    }
}

class CSqlMapException extends CException { }

