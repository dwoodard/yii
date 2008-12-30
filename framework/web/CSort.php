<?php
/**
 * CSort class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2009 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CSort represents information relevant to sorting.
 *
 * When data needs to be sorted according to one or several attributes,
 * we can use CSort to represent the sorting information and generate
 * appropriate hyperlinks that can lead to sort actions.
 *
 * CSort is designed to be used together with {@link CActiveRecord}.
 * When creating a CSort instance, you need to specify {@link modelClass}.
 * You can use CSort to generate hyperlinks by calling {@link link}.
 * You can also use CSort to modify a {@link CDbCriteria} instance so that
 * it can cause the query results to be sorted according to the specified
 * attributes.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.web
 * @since 1.0.1
 */
class CSort extends CComponent
{
	/**
	 * @var boolean whether the sorting can be applied to multiple attributes simultaneously.
	 * Defaults to true. If false, each time the data can only be sorted by one attribute.
	 */
	public $multiSort=true;
	/**
	 * @var string the class name of data models that need to be sorted.
	 * This should be a child class of {@link CActiveRecord}.
	 */
	public $modelClass;
	/**
	 * @var array list of attributes that are allowed to be sorted.
	 * For example, array('user_id','create_time') would specify that only 'user_id'
	 * and 'create_time' can be sorted.
	 * Defaults to null, meaning all attributes of the {@link modelClass} can be sorted.
	 * This property can also be used to specify attribute aliases that should appear
	 * in the 'sort' GET parameter in replace of the original attribute names.
	 * In this case, the aliases should be array values while the attribute names
	 * should be the corresponding array keys. Do not use '-' and '.' in the aliases.
	 */
	public $attributes;
	/**
	 * @var string the name of the GET parameter that specifies which attributes to be sorted
	 * in which direction. Defaults to 'sort'.
	 */
	public $sortVar='sort';
	/**
	 * @var string the default order that should be applied to the query criteria when
	 * the current request does not specify any sort.
	 */
	public $defaultOrder;
	/**
	 * @var string the route (controller ID and action ID) for generating the sorted contents.
	 * Defaults to empty string, meaning using the currently requested route.
	 */
	public $route='';
	/**
	 * @var array separators used in the generated URL. This must be an array consisting of
	 * two elements. The first element specifies the character separating different
	 * attributes, while the second element specifies the character separating attribute name
	 * and the corresponding sort direction. Defaults to array('.','-').
	 */
	public $separators=array('.','-');

	private $_directions;

	/**
	 * Constructor.
	 * @param string the class name of data models that need to be sorted.
	 * This should be a child class of {@link CActiveRecord}.
	 */
	public function __construct($modelClass)
	{
		$this->modelClass=$modelClass;
	}

	/**
	 * Modifies the query criteria by changing its ORDER BY property.
	 * @param CDbCriteria the query criteria
	 */
	public function applyOrder($criteria)
	{
		$directions=$this->getDirections();
		if(empty($directions))
			$order=$this->defaultOrder;
		else
		{
			$orders=array();
			foreach($directions as $attribute=>$descending)
				$orders[]=$descending?$attribute.' DESC':$attribute;
			$order=implode(', ',$orders);
		}

		if(!empty($order))
		{
			if(!empty($criteria->order))
				$criteria->order.=', ';
			$criteria->order.=$order;
		}
	}

	/**
	 * Generates a hyperlink that can be clicked to cause sorting.
	 * @param string the attribute name
	 * @return string the generated hyperlink
	 */
	public function link($attribute,$label=null,$htmlOptions=array())
	{
		$directions=$this->getDirections();
		if(isset($directions[$attribute]))
		{
			$descending=!$directions[$attribute];
			unset($directions[$attribute]);
		}
		else
			$descending=false;
		if($this->multiSort)
			$directions=array_merge(array($attribute=>$descending),$directions);
		else
			$directions=array($attribute=>$descending);

		if($label===null)
			$label=CActiveRecord::model($this->modelClass)->getAttributeLabel($attribute);
		$url=$this->createUrl(Yii::app()->getController(),$directions);

		return $this->createLink($attribute,$label,$url,$htmlOptions);
	}

	/**
	 * Returns the currently requested sort information.
	 * @return array sort directions indexed by attribute names.
	 * The sort direction is true if the corresponding attribute should be
	 * sorted in descending order.
	 */
	public function getDirections()
	{
		if($this->_directions===null)
		{
			$this->_directions=array();
			if(isset($_GET[$this->sortVar]))
			{
				$attributes=explode($this->separators[0],$_GET[$this->sortVar]);
				foreach($attributes as $attribute)
				{
					if(($pos=strpos($attribute,$this->separators[1]))!==false)
					{
						$descending=substr($attribute,$pos+1)==='desc';
						$attribute=substr($attribute,0,$pos);
					}
					else
						$descending=false;

					if(($attribute=$this->validateAttribute($attribute))!==false)
						$this->_directions[$attribute]=$descending;
				}
				if(!$this->multiSort)
				{
					foreach($this->_directions as $attribute=>$descending)
						return $this->_directions=array($attribute=>$descending);
				}
			}
		}
		return $this->_directions;
	}

	/**
	 * Creates a URL that can lead to generating sorted data.
	 * @param CController the controller that will be used to create the URL.
	 * @param array the sort directions indexed by attribute names.
	 * The sort direction is true if the corresponding attribute should be
	 * sorted in descending order.
	 * @return string the URL for sorting
	 */
	public function createUrl($controller,$directions)
	{
		$sorts=array();
		foreach($directions as $attribute=>$descending)
		{
			if(is_array($this->attributes) && isset($this->attributes[$attribute]))
				$attribute=$this->attributes[$attribute];
			$sorts[]=$descending ? $attribute.$this->separators[1].'desc' : $attribute;
		}
		$params=$_GET;
		$params[$this->sortVar]=implode($this->separators[0],$sorts);
		return $controller->createUrl($this->route,$params);
	}

	/**
	 * Validates an attribute that is requested to be sorted.
	 * The validation is based on {@link attributes} and {@link CActiveRecord::attributeNames}.
	 * False will be returned if the attribute is not allowed to be sorted.
	 * If the attribute is aliased via {@link attributes}, the original
	 * attribute name will be returned.
	 */
	protected function validateAttribute($attribute)
	{
		if(empty($this->attributes))
			$attributes=CActiveRecord::model($this->modelClass)->attributeNames();
		else
			$attributes=$this->attributes;
		foreach($attributes as $name=>$alias)
		{
			if($alias===$attribute)
				return is_string($name) ? $name : $alias;
		}
		return false;
	}

	/**
	 * Creates a hyperlink based on the given label and URL.
	 * You may override this method to customize the link generation.
	 * @param string the name of the attribute that this link is for
	 * @param string the label of the hyperlink
	 * @param string the URL
	 * @param array additional HTML options
	 * @return string the generated hyperlink
	 */
	protected function createLink($attribute,$label,$url,$htmlOptions)
	{
		return CHtml::link(CHtml::encode($label),$url,$htmlOptions);
	}
}