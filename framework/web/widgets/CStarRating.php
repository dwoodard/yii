<?php
/**
 * CStarRating class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CStarRating displays a star rating control that can collect user rating input.
 *
 * CStarRating is based on {@link http://plugins.jquery.com/project/MultipleFriendlyStarRating jQuery Star Rating Plugin}.
 * It displays a list of stars indicating the rating values. Users can toggle these stars
 * to indicate their rating input. On the server side, when the rating input is submitted,
 * the value can be retrieved in the same way as working with a normal HTML input.
 * For example, using
 * <pre>
 * $this->widget('CStarRating',array('name'=>'rating'));
 * </pre>
 * we can retrieve the rating value via <code>$_POST['rating']</code>.
 *
 * CStarRating allows customization of its appearance. It also supports empty rating as well as read-only rating.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.web.widgets
 * @since 1.0
 */
class CStarRating extends CInputWidget
{
	/**
	 * @var integer the number of stars. Defaults to 5.
	 */
	public $starCount=5;
	/**
	 * @var mixed the minimum rating allowed. This can be either an integer or a double value. Defaults to 1.
	 */
	public $minRating=1;
	/**
	 * @var mixed the maximum rating allowed. This can be either an integer or a double value. Defaults to 1.
	 */
	public $maxRating=10;
	/**
	 * @var mixed the step size of rating. This is the minimum difference between two rating values. Defaults to 1.
	 */
	public $ratingStepSize=1;
	/**
	 * @var mixed the CSS file used for the widget. Defaults to null, meaning
	 * using the default CSS file included together with the widget.
	 * If false, no CSS file will be used. Otherwise, the specified CSS file
	 * will be included when using this widget.
	 */
	public $cssFile;
	/**
	 * @var array the titles associated with the rating options. The keys are ratings and the values are the corresponding titles.
	 * Defaults to null, meaning using the rating value as the title.
	 */
	public $titles;
	/**
	 * @var string the hint text for the reset button. Defaults to null, meaning using the system-defined text (which is 'Cancel Rating').
	 */
	public $resetText;
	/**
	 * @var string the value taken when the rating is cleared. Defaults to null, meaning using the system-defined value (which is '').
	 */
	public $resetValue;
	/**
	 * @var boolean whether the rating value can be empty (not set). Defaults to true.
	 * When this is true, a reset button will be displayed in front of stars.
	 */
	public $allowEmpty;
	/**
	 * @var integer the width of star image. Defaults to null, meaning using the system-defined value (which is 16).
	 */
	public $starWidth;
	/**
	 * @var boolean whether the rating value is read-only or not. Defaults to false.
	 * When this is true, the rating cannot be changed.
	 */
	public $readOnly;
	/**
	 * @var string Callback when the stars are focused.
	 */
	public $focus;
	/**
	 * @var string Callback when the stars are not focused.
	 */
	public $blur;
	/**
	 * @var string Callback when the stars are clicked.
	 */
	public $click;


	/**
	 * Executes the widget.
	 * This method registers all needed client scripts and renders
	 * the text field.
	 */
	public function run()
	{
		list($name,$id)=$this->resolveNameID();
		$this->htmlOptions['id']=$id;

		$this->registerClientScript($id);

		echo CHtml::openTag('span',$this->htmlOptions)."\n";
		$this->renderStars($id,$name);
		echo "</span>";
	}

	/**
	 * Registers the necessary javascript and css scripts.
	 * @param string the ID of the container
	 */
	public function registerClientScript($id)
	{
		$jsOptions=$this->getClientOptions();
		$jsOptions=empty($jsOptions) ? '' : CJavaScript::encode($jsOptions);
		$js="jQuery('#{$id} > input').rating({$jsOptions});";
		$cs=Yii::app()->getClientScript();
		$cs->registerCoreScript('rating');
		$cs->registerScript('Yii.CStarRating#'.$id,$js);
		if($this->cssFile===null)
			$cs->registerCssFile($cs->getCoreScriptUrl().'/rating/jquery.rating.css');
		else if($this->cssFile!==false)
			$cs->registerCssFile($this->cssFile);
	}

	/**
	 * Renders the stars.
	 * @param string the ID of the container
	 * @param string the name of the input
	 */
	protected function renderStars($id,$name)
	{
		$inputCount=(int)(($this->maxRating-$this->minRating)/$this->ratingStepSize+1);
		$starSplit=(int)($inputCount/$this->starCount);
		$selection=$this->hasModel() ? $this->model->{$this->attribute} : $this->value;
		$options=$starSplit>1 ? array('class'=>"{split:{$starSplit}}") : array();
		for($value=$this->minRating, $i=0;$i<$inputCount; ++$i, $value+=$this->ratingStepSize)
		{
			$options['id']=$id.'_'.$i;
			$options['value']=$value;
			if(isset($this->titles[$value]))
				$options['title']=$this->titles[$value];
			else
				unset($options['title']);
			echo CHtml::radioButton($name,!strcmp($value,$selection),$options) . "\n";
		}
	}

	/**
	 * @return array the javascript options for the star rating
	 */
	protected function getClientOptions()
	{
		$options=array();
		if($this->resetText!==null)
			$options['cancel']=$this->resetText;
		if($this->resetValue!==null)
			$options['cancelValue']=$this->resetValue;
		if($this->allowEmpty===false)
			$options['required']=true;
		if($this->starWidth!==null)
			$options['starWidth']=$this->starWidth;
		if($this->readOnly===true)
			$options['readOnly']=true;
		if($this->focus!==null)
		{
			if(strncmp($this->focus,'js:',3))
				$options['focus']='js:'.$this->focus;
			else
				$options['focus']=$this->focus;
		}
		if($this->blur!==null)
		{
			if(strncmp($this->blur,'js:',3))
				$options['blur']='js:'.$this->blur;
			else
				$options['blur']=$this->blur;
		}
		if($this->click!==null)
		{
			if(strncmp($this->click,'js:',3))
				$options['click']='js:'.$this->click;
			else
				$options['click']=$this->click;
		}
		return $options;
	}
}