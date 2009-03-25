<?php
/**
 * CHtml class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright Copyright &copy; 2008-2009 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */


/**
 * CHtml is a static class that provides a collection of helper methods for creating HTML views.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @version $Id$
 * @package system.web.helpers
 * @since 1.0
 */
class CHtml
{
	const ID_PREFIX='yt';
	/**
	 * @var string the CSS class for displaying error summaries (see {@link errorSummary}).
	 */
	public static $errorSummaryCss='errorSummary';
	/**
	 * @var string the CSS class for displaying error messages (see {@link error}).
	 */
	public static $errorMessageCss='errorMessage';
	/**
	 * @var string the CSS class for highlighting error inputs. Form inputs will be appended
	 * with this CSS class if they have input errors.
	 */
	public static $errorCss='error';
	/**
	 * @var string the CSS class for required labels. Defaults to 'required'.
	 * @see label
	 */
	public static $requiredCss='required';
	/**
	 * @var string the HTML code to be prepended to the required label.
	 * @see label
	 */
	public static $beforeRequiredLabel='';
	/**
	 * @var string the HTML code to be appended to the required label.
	 * @see label
	 */
	public static $afterRequiredLabel=' <span class="required">*</span>';
	/**
	 * @var string the scenario used to determine whether a model attribute is required.
	 * @see activeLabelEx
	 */
	public static $scenario='';
	/**
	 * @var integer the counter for generating automatic input field names.
	 * @since 1.0.4
	 */
	public static $count=0;

	/**
	 * Encodes special characters into HTML entities.
	 * The {@link CApplication::charset application charset} will be used for encoding.
	 * @param string data to be encoded
	 * @return string the encoded data
	 * @see http://www.php.net/manual/en/function.htmlspecialchars.php
	 */
	public static function encode($text)
	{
		return htmlspecialchars($text,ENT_QUOTES,Yii::app()->charset);
	}

	/**
	 * Encodes special characters in an array of strings into HTML entities.
	 * Both the array keys and values will be encoded if needed.
	 * If a value is an array, this method will also encode it recursively.
	 * The {@link CApplication::charset application charset} will be used for encoding.
	 * @param array data to be encoded
	 * @return array the encoded data
	 * @see http://www.php.net/manual/en/function.htmlspecialchars.php
	 * @since 1.0.4
	 */
	public static function encodeArray($data)
	{
		$d=array();
		foreach($data as $key=>$value)
		{
			if(is_string($key))
				$key=htmlspecialchars($key,ENT_QUOTES,Yii::app()->charset);
			if(is_string($value))
				$value=htmlspecialchars($value,ENT_QUOTES,Yii::app()->charset);
			else if(is_array($value))
				$value=self::encodeArray($value);
			$d[$key]=$value;
		}
		return $d;
	}

	/**
	 * Generates an HTML element.
	 * @param string the tag name
	 * @param array the element attributes. The values will be HTML-encoded using {@link encode()}.
	 * @param mixed the content to be enclosed between open and close element tags. It will not be HTML-encoded.
	 * If false, it means there is no body content.
	 * @param boolean whether to generate the close tag.
	 * @return string the generated HTML element tag
	 */
	public static function tag($tag,$htmlOptions=array(),$content=false,$closeTag=true)
	{
		$html='<' . $tag;
		foreach($htmlOptions as $name=>$value)
			$html .= ' ' . $name . '="' . self::encode($value) . '"';
		if($content===false)
			return $closeTag ? $html.' />' : $html.'>';
		else
			return $closeTag ? $html.'>'.$content.'</'.$tag.'>' : $html.'>'.$content;
	}

	/**
	 * Generates an open HTML element.
	 * @param string the tag name
	 * @param array the element attributes. The values will be HTML-encoded using {@link encode()}.
	 * @return string the generated HTML element tag
	 */
	public static function openTag($tag,$htmlOptions=array())
	{
		$html='<' . $tag;
		foreach($htmlOptions as $name=>$value)
			$html .= ' ' . $name . '="' . self::encode($value) . '"';
		return $html . '>';
	}

	/**
	 * Generates a close HTML element.
	 * @param string the tag name
	 * @return string the generated HTML element tag
	 */
	public static function closeTag($tag)
	{
		return '</'.$tag.'>';
	}

	/**
	 * Encloses the given string within a CDATA tag.
	 * @param string the string to be enclosed
	 * @return string the CDATA tag with the enclosed content.
	 */
	public static function cdata($text)
	{
		return '<![CDATA[' . $text . ']]>';
	}

	/**
	 * Generates a meta tag that can be inserted in the head section of HTML page.
	 * @param string content attribute of the meta tag
	 * @param string name attribute of the meta tag. If null, the attribute will not be generated
	 * @param string http-equiv attribute of the meta tag. If null, the attribute will not be generated
	 * @param array other options in name-value pairs (e.g. 'scheme', 'lang')
	 * @return string the generated meta tag
	 * @since 1.0.1
	 */
	public static function metaTag($content,$name=null,$httpEquiv=null,$options=array())
	{
		$options['content']=$content;
		if($name!==null)
			$options['name']=$name;
		if($httpEquiv!==null)
			$options['http-equiv']=$httpEquiv;
		return self::tag('meta',$options);
	}

	/**
	 * Generates a link tag that can be inserted in the head section of HTML page.
	 * Do not confuse this method with {@link link()}. The latter generates a hyperlink.
	 * @param string rel attribute of the link tag. If null, the attribute will not be generated.
	 * @param string type attribute of the link tag. If null, the attribute will not be generated.
	 * @param string href attribute of the link tag. If null, the attribute will not be generated.
	 * @param string media attribute of the link tag. If null, the attribute will not be generated.
	 * @param array other options in name-value pairs
	 * @return string the generated link tag
	 * @since 1.0.1
	 */
	public static function linkTag($relation=null,$type=null,$href=null,$media=null,$options=array())
	{
		if($relation!==null)
			$options['rel']=$relation;
		if($type!==null)
			$options['type']=$type;
		if($href!==null)
			$options['href']=$href;
		if($media!==null)
			$options['media']=$media;
		return self::tag('link',$options);
	}

	/**
	 * Encloses the given CSS content with a CSS tag.
	 * @param string the CSS content
	 * @param string the media that this CSS should apply to.
	 * @return string the CSS properly enclosed
	 */
	public static function css($text,$media='')
	{
		if($media!=='')
			$media=' media="'.$media.'"';
		return "<style type=\"text/css\"{$media}>\n/*<![CDATA[*/\n{$text}\n/*]]>*/\n</style>";
	}

	/**
	 * Links to the specified CSS file.
	 * @param string the CSS URL
	 * @param string the media that this CSS should apply to.
	 * @return string the CSS link.
	 */
	public static function cssFile($url,$media='')
	{
		if($media!=='')
			$media=' media="'.$media.'"';
		return '<link rel="stylesheet" type="text/css" href="'.self::encode($url).'"'.$media.' />';
	}

	/**
	 * Encloses the given JavaScript within a script tag.
	 * @param string the JavaScript to be enclosed
	 * @return string the enclosed JavaScript
	 */
	public static function script($text)
	{
		return "<script type=\"text/javascript\">\n/*<![CDATA[*/\n{$text}\n/*]]>*/\n</script>";
	}

	/**
	 * Includes a JavaScript file.
	 * @param string URL for the JavaScript file
	 * @return string the JavaScript file tag
	 */
	public static function scriptFile($url)
	{
		return '<script type="text/javascript" src="'.self::encode($url).'"></script>';
	}

	/**
	 * Generates an opening form tag.
	 * This is a shortcut to {@link beginForm}.
	 * @param mixed the form action URL (see {@link normalizeUrl} for details about this parameter.)
	 * @param string form method (e.g. post, get)
	 * @param array additional HTML attributes.
	 * @return string the generated form tag.
	 */
	public static function form($action='',$method='post',$htmlOptions=array())
	{
		return self::beginForm($action,$method,$htmlOptions);
	}

	/**
	 * Generates an opening form tag.
	 * Note, only the open tag is generated. A close tag should be placed manually
	 * at the end of the form.
	 * @param mixed the form action URL (see {@link normalizeUrl} for details about this parameter.)
	 * @param string form method (e.g. post, get)
	 * @param array additional HTML attributes.
	 * @return string the generated form tag.
	 * @since 1.0.4
	 * @see endForm
	 */
	public static function beginForm($action='',$method='post',$htmlOptions=array())
	{
		$htmlOptions['action']=self::normalizeUrl($action);
		$htmlOptions['method']=$method;
		$form=self::tag('form',$htmlOptions,false,false);
		$request=Yii::app()->request;
		if($request->enableCsrfValidation)
		{
			$token=self::hiddenField($request->csrfTokenName,$request->getCsrfToken(),array('id'=>false));
			$form.="\n".$token;
		}
		return $form;
	}

	/**
	 * Generates a closing form tag.
	 * @return string the generated tag
	 * @since 1.0.4
	 * @see beginForm
	 */
	public static function endForm()
	{
		return '</form>';
	}

	/**
	 * Generates a stateful form tag.
	 * A stateful form tag is similar to {@link form} except that it renders an additional
	 * hidden field for storing persistent page states. You should use this method to generate
	 * a form tag if you want to access persistent page states when the form is submitted.
	 * @param mixed the form action URL (see {@link normalizeUrl} for details about this parameter.)
	 * @param string form method (e.g. post, get)
	 * @param array additional HTML attributes.
	 * @return string the generated form tag.
	 */
	public static function statefulForm($action='',$method='post',$htmlOptions=array())
	{
		return self::form($action,$method,$htmlOptions)."\n".self::pageStateField('');
	}

	/**
	 * Generates a hidden field for storing persistent page states.
	 * This method is internally used by {@link statefulForm}.
	 * @param string the persistent page states in serialized format
	 * @return string the generated hidden field
	 */
	public static function pageStateField($value)
	{
		return '<input type="hidden" name="'.CController::STATE_INPUT_NAME.'" value="'.$value.'" />';
	}

	/**
	 * Generates a hyperlink tag.
	 * @param string link body. It will NOT be HTML-encoded. Therefore you can pass in HTML code such as an image tag.
	 * @param mixed a URL or an action route that can be used to create a URL.
	 * See {@link normalizeUrl} for more details about how to specify this parameter.
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated hyperlink
	 * @see normalizeUrl
	 * @see clientChange
	 */
	public static function link($text,$url='#',$htmlOptions=array())
	{
		if($url!=='')
			$htmlOptions['href']=self::normalizeUrl($url);
		self::clientChange('click',$htmlOptions);
		return self::tag('a',$htmlOptions,$text);
	}

	/**
	 * Generates a mailto link.
	 * @param string link body. It will NOT be HTML-encoded. Therefore you can pass in HTML code such as an image tag.
	 * @param string email address. If this is empty, the first parameter (link body) will be treated as the email address.
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated mailto link
	 * @see clientChange
	 * @since 1.0.1
	 */
	public static function mailto($text,$email='',$htmlOptions=array())
	{
		if($email==='')
			$email=$text;
		return self::link($text,'mailto:'.$email,$htmlOptions);
	}

	/**
	 * Generates an image tag.
	 * @param string the image URL
	 * @param string the alternative text display
	 * @param array additional HTML attributes.
	 * @return string the generated image tag
	 */
	public static function image($src,$alt='',$htmlOptions=array())
	{
		$htmlOptions['src']=$src;
		$htmlOptions['alt']=$alt;
		return self::tag('img',$htmlOptions);
	}

	/**
	 * Generates a button.
	 * @param string the button label
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated button tag
	 * @see clientChange
	 */
	public static function button($label='button',$htmlOptions=array())
	{
		if(!isset($htmlOptions['name']))
			$htmlOptions['name']=self::ID_PREFIX.self::$count++;
		if(!isset($htmlOptions['type']))
			$htmlOptions['type']='button';
		if(!isset($htmlOptions['value']))
			$htmlOptions['value']=$label;
		self::clientChange('click',$htmlOptions);
		return self::tag('input',$htmlOptions);
	}

	/**
	 * Generates a submit button.
	 * @param string the button label
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated button tag
	 * @see clientChange
	 */
	public static function submitButton($label='submit',$htmlOptions=array())
	{
		$htmlOptions['type']='submit';
		return self::button($label,$htmlOptions);
	}

	/**
	 * Generates a reset button.
	 * @param string the button label
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated button tag
	 * @see clientChange
	 */
	public static function resetButton($label='reset',$htmlOptions=array())
	{
		$htmlOptions['type']='reset';
		return self::button($label,$htmlOptions);
	}

	/**
	 * Generates an image submit button.
	 * @param string the button label
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated button tag
	 * @see clientChange
	 */
	public static function imageButton($src,$htmlOptions=array())
	{
		$htmlOptions['src']=$src;
		$htmlOptions['type']='image';
		return self::button('submit',$htmlOptions);
	}

	/**
	 * Generates a link submit button.
	 * @param string the button label
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated button tag
	 * @see clientChange
	 */
	public static function linkButton($label='submit',$htmlOptions=array())
	{
		if(!isset($htmlOptions['submit']))
			$htmlOptions['submit']=isset($htmlOptions['href']) ? $htmlOptions['href'] : '';
		return self::link($label,'#',$htmlOptions);
	}

	/**
	 * Generates a label tag.
	 * @param string label text. Note, you should HTML-encode the text if needed.
	 * @param string the ID of the HTML element that this label is associated with
	 * @param array additional HTML attributes.
	 * Starting from version 1.0.2, the following HTML option is recognized:
	 * <pre>
	 * <li>required: if this is set and is true, the label will be styled
	 * with CSS class 'required' (customizable with CHtml::$requiredCss),
	 * and be decorated with {@link CHtml::beforeRequiredLabel} and
	 * {@link CHtml::afterRequiredLabel}.</li>
	 * </pre>
	 * @return string the generated label tag
	 */
	public static function label($label,$for,$htmlOptions=array())
	{
		$htmlOptions['for']=$for;
		if(isset($htmlOptions['required']))
		{
			if($htmlOptions['required'])
			{
				if(isset($htmlOptions['class']))
					$htmlOptions['class'].=' '.self::$requiredCss;
				else
					$htmlOptions['class']=self::$requiredCss;
				$label=self::$beforeRequiredLabel.$label.self::$afterRequiredLabel;
			}
			unset($htmlOptions['required']);
		}
		return self::tag('label',$htmlOptions,$label);
	}

	/**
	 * Generates a text field input.
	 * @param string the input name
	 * @param string the input value
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated input field
	 * @see clientChange
	 * @see inputField
	 */
	public static function textField($name,$value='',$htmlOptions=array())
	{
		self::clientChange('change',$htmlOptions);
		return self::inputField('text',$name,$value,$htmlOptions);
	}

	/**
	 * Generates a hidden input.
	 * @param string the input name
	 * @param string the input value
	 * @param array additional HTML attributes.
	 * @return string the generated input field
	 * @see inputField
	 */
	public static function hiddenField($name,$value='',$htmlOptions=array())
	{
		return self::inputField('hidden',$name,$value,$htmlOptions);
	}

	/**
	 * Generates a password field input.
	 * @param string the input name
	 * @param string the input value
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated input field
	 * @see clientChange
	 * @see inputField
	 */
	public static function passwordField($name,$value='',$htmlOptions=array())
	{
		self::clientChange('change',$htmlOptions);
		return self::inputField('password',$name,$value,$htmlOptions);
	}

	/**
	 * Generates a file input.
	 * Note, you have to set the enclosing form's 'enctype' attribute to be 'multipart/form-data'.
	 * After the form is submitted, the uploaded file information can be obtained via $_FILES[$name] (see
	 * PHP documentation).
	 * @param string the input name
	 * @param string the input value
	 * @param array additional HTML attributes.
	 * @return string the generated input field
	 * @see inputField
	 */
	public static function fileField($name,$value='',$htmlOptions=array())
	{
		return self::inputField('file',$name,$value,$htmlOptions);
	}

	/**
	 * Generates a text area input.
	 * @param string the input name
	 * @param string the input value
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated text area
	 * @see clientChange
	 * @see inputField
	 */
	public static function textArea($name,$value='',$htmlOptions=array())
	{
		$htmlOptions['name']=$name;
		if(!isset($htmlOptions['id']))
			$htmlOptions['id']=self::getIdByName($name);
		self::clientChange('change',$htmlOptions);
		return self::tag('textarea',$htmlOptions,self::encode($value));
	}

	/**
	 * Generates a radio button.
	 * @param string the input name
	 * @param boolean whether the check box is checked
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated radio button
	 * @see clientChange
	 * @see inputField
	 */
	public static function radioButton($name,$checked=false,$htmlOptions=array())
	{
		if($checked)
			$htmlOptions['checked']='checked';
		else
			unset($htmlOptions['checked']);
		$value=isset($htmlOptions['value']) ? $htmlOptions['value'] : 1;
		self::clientChange('click',$htmlOptions);
		return self::inputField('radio',$name,$value,$htmlOptions);
	}

	/**
	 * Generates a check box.
	 * @param string the input name
	 * @param boolean whether the check box is checked
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated check box
	 * @see clientChange
	 * @see inputField
	 */
	public static function checkBox($name,$checked=false,$htmlOptions=array())
	{
		if($checked)
			$htmlOptions['checked']='checked';
		else
			unset($htmlOptions['checked']);
		$value=isset($htmlOptions['value']) ? $htmlOptions['value'] : 1;
		self::clientChange('click',$htmlOptions);
		return self::inputField('checkbox',$name,$value,$htmlOptions);
	}

	/**
	 * Generates a drop down list.
	 * @param string the input name
	 * @param string the selected value
	 * @param array data for generating the list options (value=>display).
	 * You may use {@link listData} to generate this data.
	 * Please refer to {@link listOptions} on how this data is used to generate the list options.
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated drop down list
	 * @see clientChange
	 * @see inputField
	 * @see listData
	 */
	public static function dropDownList($name,$select,$data,$htmlOptions=array())
	{
		$htmlOptions['name']=$name;
		if(!isset($htmlOptions['id']))
			$htmlOptions['id']=self::getIdByName($name);
		self::clientChange('change',$htmlOptions);
		$options="\n".self::listOptions($select,$data,$htmlOptions);
		return self::tag('select',$htmlOptions,$options);
	}

	/**
	 * Generates a list box.
	 * @param string the input name
	 * @param string the selected value
	 * @param array data for generating the list options (value=>display)
	 * You may use {@link listData} to generate this data.
	 * Please refer to {@link listOptions} on how this data is used to generate the list options.
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated list box
	 * @see clientChange
	 * @see inputField
	 * @see listData
	 */
	public static function listBox($name,$select,$data,$htmlOptions=array())
	{
		if(!isset($htmlOptions['size']))
			$htmlOptions['size']=4;
		if(isset($htmlOptions['multiple']))
		{
			if(substr($name,-2)!=='[]')
				$name.='[]';
		}
		return self::dropDownList($name,$select,$data,$htmlOptions);
	}

	/**
	 * Generates a check box list.
	 * A check box list allows multiple selection, like {@link listBox}.
	 * As a result, the corresponding POST value is an array.
	 * @param string name of the check box list. You can use this name to retrieve
	 * the selected value(s) once the form is submitted.
	 * @param mixed selection of the check boxes. This can be either a string
	 * for single selection or an array for multiple selections.
	 * @param array value-label pairs used to generate the check box list.
	 * @param array addtional HTML options. The options will be applied to
	 * each checkbox input. The following special options are recognized:
	 * <ul>
	 * <li>template: string, specifies how each checkbox is rendered. Defaults
	 * to "{input} {label}", where "{input}" will be replaced by the generated
	 * check box input tag while "{label}" be replaced by the corresponding check box label.</li>
	 * <li>separator: string, specifies the string that separates the generated check boxes.</li>
	 * <li>checkAll: string, specifies the label for the "check all" checkbox.
	 * If this option is specified, a 'check all' checkbox will be displayed. Clicking on
	 * this checkbox will cause all checkboxes checked or unchecked. This option has been
	 * available since version 1.0.4.</li>
	 * <li>checkAllLast: boolean, specifies whether the 'check all' checkbox should be
	 * displayed at the end of the checkbox list. If this option is not set (default)
	 * or is false, the 'check all' checkbox will be displayed at the beginning of
	 * the checkbox list. This option has been available since version 1.0.4.</li>
	 * </ul>
	 * @return string the generated check box list
	 */
	public static function checkBoxList($name,$select,$data,$htmlOptions=array())
	{
		$template=isset($htmlOptions['template'])?$htmlOptions['template']:'{input} {label}';
		$separator=isset($htmlOptions['separator'])?$htmlOptions['separator']:"<br/>\n";
		unset($htmlOptions['template'],$htmlOptions['separator']);

		if(substr($name,-2)!=='[]')
			$name.='[]';

		if(isset($htmlOptions['checkAll']))
		{
			$checkAllLabel=$htmlOptions['checkAll'];
			$checkAllLast=isset($htmlOptions['checkAllLast']) && $htmlOptions['checkAllLast'];
		}
		unset($htmlOptions['checkAll'],$htmlOptions['checkAllLast']);

		$items=array();
		$baseID=self::getIdByName($name);
		$id=0;
		$checkAll=true;
		foreach($data as $value=>$label)
		{
			$checked=!is_array($select) && !strcmp($value,$select) || is_array($select) && in_array($value,$select);
			$checkAll=$checkAll && $checked;
			$htmlOptions['value']=$value;
			$htmlOptions['id']=$baseID.'_'.$id++;
			$option=self::checkBox($name,$checked,$htmlOptions);
			$label=self::label($label,$htmlOptions['id']);
			$items[]=strtr($template,array('{input}'=>$option,'{label}'=>$label));
		}

		if(isset($checkAllLabel))
		{
			$htmlOptions['value']=1;
			$htmlOptions['id']=$id=$baseID.'_all';
			$option=self::checkBox($id,$checkAll,$htmlOptions);
			$label=self::label($checkAllLabel,$id);
			$item=strtr($template,array('{input}'=>$option,'{label}'=>$label));
			if($checkAllLast)
				$items[]=$item;
			else
				array_unshift($items,$item);
			$name=strtr($name,array('['=>'\\[',']'=>'\\]'));
			$js=<<<EOD
jQuery('#$id').click(function() {
	var checked=this.checked;
	jQuery("input[name='$name']").each(function() {
		this.checked=checked;
	});
});

jQuery("input[name='$name']").click(function() {
	jQuery('#$id').attr('checked', jQuery("input[name='$name']").length==jQuery("input[name='$name'][checked=true]").length);
});
EOD;
			$cs=Yii::app()->getClientScript();
			$cs->registerCoreScript('jquery');
			$cs->registerScript($id,$js);
		}

		return implode($separator,$items);
	}

	/**
	 * Generates a radio button list.
	 * A radio button list is like a {@link checkBoxList check box list}, except that
	 * it only allows single selection.
	 * @param string name of the radio button list. You can use this name to retrieve
	 * the selected value(s) once the form is submitted.
	 * @param mixed selection of the radio buttons. This can be either a string
	 * for single selection or an array for multiple selections.
	 * @param array value-label pairs used to generate the radio button list.
	 * @param array addtional HTML options. The options will be applied to
	 * each checkbox input. The following special options are recognized:
	 * <ul>
	 * <li>template: string, specifies how each checkbox is rendered. Defaults
	 * to "{input} {label}", where "{input}" will be replaced by the generated
	 * radio button input tag while "{label}" be replaced by the corresponding radio button label.</li>
	 * <li>separator: string, specifies the string that separates the generated radio buttons.</li>
	 * </ul>
	 * @return string the generated radio button list
	 */
	public static function radioButtonList($name,$select,$data,$htmlOptions=array())
	{
		$template=isset($htmlOptions['template'])?$htmlOptions['template']:'{input} {label}';
		$separator=isset($htmlOptions['separator'])?$htmlOptions['separator']:"<br/>\n";
		unset($htmlOptions['template'],$htmlOptions['separator']);

		$items=array();
		$baseID=self::getIdByName($name);
		$id=0;
		foreach($data as $value=>$label)
		{
			$checked=!strcmp($value,$select);
			$htmlOptions['value']=$value;
			$htmlOptions['id']=$baseID.'_'.$id++;
			$option=self::radioButton($name,$checked,$htmlOptions);
			$label=self::label($label,$htmlOptions['id']);
			$items[]=strtr($template,array('{input}'=>$option,'{label}'=>$label));
		}
		return implode($separator,$items);
	}

	/**
	 * Generates a link that can initiate AJAX requests.
	 * @param string the link body (it will NOT be HTML-encoded.)
	 * @param string the URL for the AJAX request. If empty, it is assumed to be the current URL. See {@link normalizeUrl} for more details.
	 * @param array AJAX options (see {@link ajax})
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated link
	 * @see normalizeUrl
	 * @see ajax
	 */
	public static function ajaxLink($text,$url,$ajaxOptions=array(),$htmlOptions=array())
	{
		if(!isset($htmlOptions['href']))
			$htmlOptions['href']='#';
		$ajaxOptions['url']=$url;
		$htmlOptions['ajax']=$ajaxOptions;
		self::clientChange('click',$htmlOptions);
		return self::tag('a',$htmlOptions,$text);
	}

	/**
	 * Generates a push button that can initiate AJAX requests.
	 * @param string the button label
	 * @param string the URL for the AJAX request. If empty, it is assumed to be the current URL. See {@link normalizeUrl} for more details.
	 * @param array AJAX options (see {@link ajax})
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated button
	 */
	public static function ajaxButton($label,$url,$ajaxOptions=array(),$htmlOptions=array())
	{
		$ajaxOptions['url']=$url;
		$htmlOptions['ajax']=$ajaxOptions;
		return self::button($label,$htmlOptions);
	}

	/**
	 * Generates a push button that can submit the current form in POST method.
	 * @param string the button label
	 * @param string the URL for the AJAX request. If empty, it is assumed to be the current URL. See {@link normalizeUrl} for more details.
	 * @param array AJAX options (see {@link ajax})
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated button
	 */
	public static function ajaxSubmitButton($label,$url,$ajaxOptions=array(),$htmlOptions=array())
	{
		$ajaxOptions['type']='POST';
		return self::ajaxButton($label,$url,$ajaxOptions,$htmlOptions);
	}

	/**
	 * Generates the JavaScript that initiates an AJAX request.
	 * @param array AJAX options. The valid options are specified in the jQuery ajax documentation.
	 * The following special options are added for convenience:
	 * <ul>
	 * <li>update: string, specifies the selector whose HTML content should be replaced
	 *   by the AJAX request result.</li>
	 * <li>replace: string, specifies the selector whose target should be replaced
	 *   by the AJAX request result.</li>
	 * </ul>
	 * Note, if you specify the 'success' option, the above options will be ignored.
	 * @return string the generated JavaScript
	 * @see http://docs.jquery.com/Ajax/jQuery.ajax#options
	 */
	public static function ajax($options)
	{
		Yii::app()->getClientScript()->registerCoreScript('jquery');
		if(!isset($options['url']))
			$options['url']='js:location.href';
		else
			$options['url']=self::normalizeUrl($options['url']);
		if(!isset($options['cache']))
			$options['cache']=false;
		if(!isset($options['data']) && isset($options['type']))
			$options['data']='js:jQuery(this).parents("form").serialize()';
		foreach(array('beforeSend','complete','error','success') as $name)
		{
			if(isset($options[$name]) && strpos($options[$name],'js:')!==0)
				$options[$name]='js:'.$options[$name];
		}
		if(isset($options['update']))
		{
			if(!isset($options['success']))
				$options['success']='js:function(html){jQuery("'.$options['update'].'").html(html)}';
			unset($options['update']);
		}
		if(isset($options['replace']))
		{
			if(!isset($options['success']))
				$options['success']='js:function(html){jQuery("'.$options['replace'].'").replaceWith(html)}';
			unset($options['replace']);
		}
		return 'jQuery.ajax('.CJavaScript::encode($options).');';
	}

	/**
	 * Generates the URL for the published assets.
	 * @param string the path of the asset to be published
	 * @param boolean whether the published directory should be named as the hashed basename.
	 * If false, the name will be the hashed dirname of the path being published.
	 * Defaults to false. Set true if the path being published is shared among
	 * different extensions.
	 * @return string the asset URL
	 */
	public static function asset($path,$hashByName=false)
	{
		return Yii::app()->getAssetManager()->publish($path,$hashByName);
	}

	/**
	 * Generates a URL if the input specifies the route to a controller action.
	 * @param mixed the URL to be normalized. If a string, the URL is returned back;
	 * if an array, it is considered as a route to a controller action and will
	 * be used to generate a URL using {@link CController::createUrl}; if the URL is empty,
	 * the currently requested URL is returned.
	 * @param string the URL
	 */
	public static function normalizeUrl($url)
	{
		if(is_array($url))
			$url=isset($url[0]) ? Yii::app()->getController()->createUrl($url[0],array_splice($url,1)) : '';
		return $url==='' ? Yii::app()->getRequest()->getUrl() : $url;
	}

	/**
	 * Generates an input HTML tag.
	 * This method generates an input HTML tag based on the given input name and value.
	 * @param string the input type (e.g. 'text', 'radio')
	 * @param string the input name
	 * @param string the input value
	 * @param array additional HTML attributes for the HTML tag
	 * @return string the generated input tag
	 */
	protected static function inputField($type,$name,$value,$htmlOptions)
	{
		$htmlOptions['type']=$type;
		$htmlOptions['value']=$value;
		$htmlOptions['name']=$name;
		if(!isset($htmlOptions['id']))
			$htmlOptions['id']=self::getIdByName($name);
		else if($htmlOptions['id']===false)
			unset($htmlOptions['id']);
		return self::tag('input',$htmlOptions);
	}

	/**
	 * Generates a label tag for a model attribute.
	 * The label text is the attribute label and the label is associated with
	 * the input for the attribute. If the attribute has input error,
	 * the label's CSS class will be appended with {@link errorCss}.
	 * @param CModel the data model
	 * @param string the attribute
	 * @param array additional HTML attributes.
	 * @return string the generated label tag
	 */
	public static function activeLabel($model,$attribute,$htmlOptions=array())
	{
		$for=self::getIdByName(self::resolveName($model,$attribute));
		$label=$model->getAttributeLabel($attribute);
		if($model->hasErrors($attribute))
			self::addErrorCss($htmlOptions);
		return self::label($label,$for,$htmlOptions);
	}

	/**
	 * Generates a label tag for a model attribute.
	 * This is an enhanced version of {@link activeLabel}. It will render additional
	 * CSS class and mark when the attribute is required.
	 * In particular, it calls {@link CModel::isAttributeRequired} to determine
	 * if the attribute is required under the scenario {@link CHtml::scenario}.
	 * If so, it will add a CSS class {@link CHtml::requiredCss} to the label,
	 * and decorate the label with {@link CHtml::beforeRequiredLabel} and
	 * {@link CHtml::afterRequiredLabel}.
	 * @param CModel the data model
	 * @param string the attribute
	 * @param array additional HTML attributes.
	 * @return string the generated label tag
	 * @since 1.0.2
	 */
	public static function activeLabelEx($model,$attribute,$htmlOptions=array())
	{
		$realAttribute=$attribute;
		self::resolveName($model,$attribute); // strip off square brackets if any
		$htmlOptions['required']=$model->isAttributeRequired($attribute,self::$scenario);
		return self::activeLabel($model,$realAttribute,$htmlOptions);
	}

	/**
	 * Generates a text field input for a model attribute.
	 * If the attribute has input error, the input field's CSS class will
	 * be appended with {@link errorCss}.
	 * @param CModel the data model
	 * @param string the attribute
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated input field
	 * @see clientChange
	 * @see activeInputField
	 */
	public static function activeTextField($model,$attribute,$htmlOptions=array())
	{
		self::resolveNameID($model,$attribute,$htmlOptions);
		self::clientChange('change',$htmlOptions);
		return self::activeInputField('text',$model,$attribute,$htmlOptions);
	}

	/**
	 * Generates a hidden input for a model attribute.
	 * @param CModel the data model
	 * @param string the attribute
	 * @param array additional HTML attributes.
	 * @return string the generated input field
	 * @see activeInputField
	 */
	public static function activeHiddenField($model,$attribute,$htmlOptions=array())
	{
		self::resolveNameID($model,$attribute,$htmlOptions);
		return self::activeInputField('hidden',$model,$attribute,$htmlOptions);
	}

	/**
	 * Generates a password field input for a model attribute.
	 * If the attribute has input error, the input field's CSS class will
	 * be appended with {@link errorCss}.
	 * @param CModel the data model
	 * @param string the attribute
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated input field
	 * @see clientChange
	 * @see activeInputField
	 */
	public static function activePasswordField($model,$attribute,$htmlOptions=array())
	{
		self::resolveNameID($model,$attribute,$htmlOptions);
		self::clientChange('change',$htmlOptions);
		return self::activeInputField('password',$model,$attribute,$htmlOptions);
	}

	/**
	 * Generates a text area input for a model attribute.
	 * If the attribute has input error, the input field's CSS class will
	 * be appended with {@link errorCss}.
	 * @param CModel the data model
	 * @param string the attribute
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated text area
	 * @see clientChange
	 */
	public static function activeTextArea($model,$attribute,$htmlOptions=array())
	{
		self::resolveNameID($model,$attribute,$htmlOptions);
		self::clientChange('change',$htmlOptions);
		if($model->hasErrors($attribute))
			self::addErrorCss($htmlOptions);
		return self::tag('textarea',$htmlOptions,self::encode($model->$attribute));
	}

	/**
	 * Generates a file input for a model attribute.
	 * Note, you have to set the enclosing form's 'enctype' attribute to be 'multipart/form-data'.
	 * After the form is submitted, the uploaded file information can be obtained via $_FILES (see
	 * PHP documentation).
	 * @param CModel the data model
	 * @param string the attribute
	 * @param array additional HTML attributes.
	 * @return string the generated input field
	 * @see activeInputField
	 */
	public static function activeFileField($model,$attribute,$htmlOptions=array())
	{
		self::resolveNameID($model,$attribute,$htmlOptions);
		// add a hidden field so that if a model only has a file field, we can
		// still use isset($_POST[$modelClass]) to detect if the input is submitted
		return self::hiddenField($htmlOptions['name'],'',array('id'=>self::ID_PREFIX.$htmlOptions['id']))
			. self::activeInputField('file',$model,$attribute,$htmlOptions);
	}

	/**
	 * Generates a radio button for a model attribute.
	 * If the attribute has input error, the input field's CSS class will
	 * be appended with {@link errorCss}.
	 * @param CModel the data model
	 * @param string the attribute
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated radio button
	 * @see clientChange
	 * @see activeInputField
	 */
	public static function activeRadioButton($model,$attribute,$htmlOptions=array())
	{
		self::resolveNameID($model,$attribute,$htmlOptions);
		if(!isset($htmlOptions['value']))
			$htmlOptions['value']=1;
		if($model->$attribute)
			$htmlOptions['checked']='checked';
		self::clientChange('click',$htmlOptions);
		// add a hidden field so that if the radio button is not selected, it still submits a value
		return self::hiddenField($htmlOptions['name'],$htmlOptions['value']?0:-1,array('id'=>self::ID_PREFIX.$htmlOptions['id']))
			. self::activeInputField('radio',$model,$attribute,$htmlOptions);
	}

	/**
	 * Generates a check box for a model attribute.
	 * The attribute is assumed to take either true or false value.
	 * If the attribute has input error, the input field's CSS class will
	 * be appended with {@link errorCss}.
	 * @param CModel the data model
	 * @param string the attribute
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * Since version 1.0.2, a special option named 'uncheckValue' is available that can be used to specify
	 * the value returned when the checkbox is not checked. By default, this value is '0'.
	 * @return string the generated check box
	 * @see clientChange
	 * @see activeInputField
	 */
	public static function activeCheckBox($model,$attribute,$htmlOptions=array())
	{
		self::resolveNameID($model,$attribute,$htmlOptions);
		if(!isset($htmlOptions['value']))
			$htmlOptions['value']=1;
		if($model->$attribute)
			$htmlOptions['checked']='checked';
		self::clientChange('click',$htmlOptions);

		if(isset($htmlOptions['uncheckValue']))
		{
			$uncheck=$htmlOptions['uncheckValue'];
			unset($htmlOptions['uncheckValue']);
		}
		else
			$uncheck='0';

		return self::hiddenField($htmlOptions['name'],$uncheck,array('id'=>self::ID_PREFIX.$htmlOptions['id']))
			. self::activeInputField('checkbox',$model,$attribute,$htmlOptions);
	}

	/**
	 * Generates a drop down list for a model attribute.
	 * If the attribute has input error, the input field's CSS class will
	 * be appended with {@link errorCss}.
	 * @param CModel the data model
	 * @param string the attribute
	 * @param array data for generating the list options (value=>display)
	 * You may use {@link listData} to generate this data.
	 * Please refer to {@link listOptions} on how this data is used to generate the list options.
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated drop down list
	 * @see clientChange
	 * @see listData
	 */
	public static function activeDropDownList($model,$attribute,$data,$htmlOptions=array())
	{
		self::resolveNameID($model,$attribute,$htmlOptions);
		$selection=$model->$attribute;
		$options="\n".self::listOptions($selection,$data,$htmlOptions);
		self::clientChange('change',$htmlOptions);
		if($model->hasErrors($attribute))
			self::addErrorCss($htmlOptions);
		if(isset($htmlOptions['multiple']))
		{
			if(substr($htmlOptions['name'],-2)!=='[]')
				$htmlOptions['name'].='[]';
		}
		return self::tag('select',$htmlOptions,$options);
	}

	/**
	 * Generates a list box for a model attribute.
	 * The model attribute value is used as the selection.
	 * If the attribute has input error, the input field's CSS class will
	 * be appended with {@link errorCss}.
	 * @param CModel the data model
	 * @param string the attribute
	 * @param array data for generating the list options (value=>display)
	 * You may use {@link listData} to generate this data.
	 * Please refer to {@link listOptions} on how this data is used to generate the list options.
	 * @param array additional HTML attributes. Besides normal HTML attributes, a few special
	 * attributes are also recognized (see {@link clientChange} for more details.)
	 * @return string the generated list box
	 * @see clientChange
	 * @see listData
	 */
	public static function activeListBox($model,$attribute,$data,$htmlOptions=array())
	{
		if(!isset($htmlOptions['size']))
			$htmlOptions['size']=4;
		return self::activeDropDownList($model,$attribute,$data,$htmlOptions);
	}

	/**
	 * Generates a check box list for a model attribute.
	 * The model attribute value is used as the selection.
	 * If the attribute has input error, the input field's CSS class will
	 * be appended with {@link errorCss}.
	 * Note that a check box list allows multiple selection, like {@link listBox}.
	 * As a result, the corresponding POST value is an array. In case no selection
	 * is made, the corresponding POST value is an empty string.
	 * @param CModel the data model
	 * @param string the attribute
	 * @param array value-label pairs used to generate the check box list.
	 * @param array addtional HTML options. The options will be applied to
	 * each checkbox input. The following special options are recognized:
	 * <ul>
	 * <li>template: string, specifies how each checkbox is rendered. Defaults
	 * to "{input} {label}", where "{input}" will be replaced by the generated
	 * check box input tag while "{label}" be replaced by the corresponding check box label.</li>
	 * <li>separator: string, specifies the string that separates the generated check boxes.</li>
	 * <li>checkAll: string, specifies the label for the "check all" checkbox.
	 * If this option is specified, a 'check all' checkbox will be displayed. Clicking on
	 * this checkbox will cause all checkboxes checked or unchecked. This option has been
	 * available since version 1.0.4.</li>
	 * <li>checkAllLast: boolean, specifies whether the 'check all' checkbox should be
	 * displayed at the end of the checkbox list. If this option is not set (default)
	 * or is false, the 'check all' checkbox will be displayed at the beginning of
	 * the checkbox list. This option has been available since version 1.0.4.</li>
	 * </ul>
	 * @return string the generated check box list
	 * @see checkBoxList
	 */
	public static function activeCheckBoxList($model,$attribute,$data,$htmlOptions=array())
	{
		self::resolveNameID($model,$attribute,$htmlOptions);
		$selection=$model->$attribute;
		if($model->hasErrors($attribute))
			self::addErrorCss($htmlOptions);
		$name=$htmlOptions['name'];
		unset($htmlOptions['name']);

		return self::hiddenField($name,'',array('id'=>self::ID_PREFIX.$htmlOptions['id']))
			. self::checkBoxList($name,$selection,$data,$htmlOptions);
	}

	/**
	 * Generates a radio button list for a model attribute.
	 * The model attribute value is used as the selection.
	 * If the attribute has input error, the input field's CSS class will
	 * be appended with {@link errorCss}.
	 * @param CModel the data model
	 * @param string the attribute
	 * @param array value-label pairs used to generate the radio button list.
	 * @param array addtional HTML options. The options will be applied to
	 * each checkbox input. The following special options are recognized:
	 * <ul>
	 * <li>template: string, specifies how each checkbox is rendered. Defaults
	 * to "{input} {label}", where "{input}" will be replaced by the generated
	 * radio button input tag while "{label}" be replaced by the corresponding radio button label.</li>
	 * <li>separator: string, specifies the string that separates the generated radio buttons.</li>
	 * </ul>
	 * @return string the generated radio button list
	 * @see radioButtonList
	 */
	public static function activeRadioButtonList($model,$attribute,$data,$htmlOptions=array())
	{
		self::resolveNameID($model,$attribute,$htmlOptions);
		$selection=$model->$attribute;
		if($model->hasErrors($attribute))
			self::addErrorCss($htmlOptions);
		$name=$htmlOptions['name'];
		unset($htmlOptions['name']);

		return self::hiddenField($name,'',array('id'=>self::ID_PREFIX.$htmlOptions['id']))
			. self::radioButtonList($name,$selection,$data,$htmlOptions);
	}

	/**
	 * Returns the element ID that is used by methods such as {@link activeTextField}.
	 * @param CModel the data model
	 * @param string the attribute
	 * @return string the element ID for the active field corresponding to the specified model and attribute.
	 */
	public static function getActiveId($model,$attribute)
	{
		return get_class($model).'_'.$attribute;
	}

	/**
	 * Displays a summary of validation errors for one or several models.
	 * @param mixed the models whose input errors are to be displayed. This can be either
	 * a single model or an array of models.
	 * @param string a piece of HTML code that appears in front of the errors
	 * @param string a piece of HTML code that appears at the end of the errors
	 * @return string the error summary. Empty if no errors are found.
	 * @see CModel::getErrors
	 * @see errorSummaryCss
	 */
	public static function errorSummary($model,$header=null,$footer=null)
	{
		$content='';
		if(!is_array($model))
			$model=array($model);
		foreach($model as $m)
		{
			foreach($m->getErrors() as $errors)
			{
				foreach($errors as $error)
				{
					if($error!='')
						$content.="<li>$error</li>\n";
				}
			}
		}
		if($content!=='')
		{
			if($header===null)
				$header='<p>'.Yii::t('yii','Please fix the following input errors:').'</p>';
			return self::tag('div',array('class'=>self::$errorSummaryCss),$header."\n<ul>\n$content</ul>".$footer);
		}
		else
			return '';
	}

	/**
	 * Displays the first validation error for a model attribute.
	 * @param CModel the data model
	 * @param string the attribute name
	 * @return string the error display. Empty if no errors are found.
	 * @see CModel::getErrors
	 * @see errorMessageCss
	 */
	public static function error($model,$attribute)
	{
		$error=$model->getError($attribute);
		if($error!='')
			return self::tag('div',array('class'=>self::$errorMessageCss),$error);
		else
			return '';
	}

	/**
	 * Generates the data suitable for {@link dropDownList} and {@link listBox}.
	 * Note, this method does not HTML-encode the generated data. You may call {@link encodeArray} to
	 * encode it if needed.
	 * @param array a list of model objects. Starting from version 1.0.3, this parameter
	 * can also be an array of associative arrays (e.g. results of {@link CDbCommand::queryAll}).
	 * @param string the attribute name for list option values
	 * @param string the attribute name for list option texts
	 * @param string the attribute name for list option group names. If empty, no group will be generated.
	 * @return array the list data that can be used in {@link dropDownList} and {@link listBox}
	 */
	public static function listData($models,$valueField,$textField,$groupField='')
	{
		$listData=array();
		if($groupField==='')
		{
			foreach($models as $model)
			{
				if(is_object($model))
					$listData[$model->$valueField]=$model->$textField;
				else
					$listData[$model[$valueField]]=$model[$textField];
			}
		}
		else
		{
			foreach($models as $model)
			{
				if(is_object($model))
					$listData[$model->$groupField][$model->$valueField]=$model->$textField;
				else
					$listData[$model[$groupField]][$model[$valueField]]=$model[$textField];
			}
		}
		return $listData;
	}

	/**
	 * Generates a valid HTML ID based the name.
	 * @return string the ID generated based on name.
	 */
	public static function getIdByName($name)
	{
		return str_replace(array('[]', '][', '[', ']'), array('', '_', '_', ''), $name);
	}

	/**
	 * Generates input field ID for a model attribute.
	 * @param CModel the data model
	 * @param string the attribute
	 * @return string the generated input field ID
	 * @since 1.0.1
	 */
	public static function activeId($model,$attribute)
	{
		return self::getIdByName(self::activeName($model,$attribute));
	}

	/**
	 * Generates input field name for a model attribute.
	 * @param CModel the data model
	 * @param string the attribute
	 * @return string the generated input field name
	 * @since 1.0.1
	 */
	public static function activeName($model,$attribute)
	{
		$a=$attribute; // because the attribute name may be changed by resolveName
		return self::resolveName($model,$a);
	}

	/**
	 * Generates an input HTML tag for a model attribute.
	 * This method generates an input HTML tag based on the given data model and attribute.
	 * If the attribute has input error, the input field's CSS class will
	 * be appended with {@link errorCss}.
	 * This enables highlighting the incorrect input.
	 * @param string the input type (e.g. 'text', 'radio')
	 * @param CModel the data model
	 * @param string the attribute
	 * @param array additional HTML attributes for the HTML tag
	 * @return string the generated input tag
	 */
	protected static function activeInputField($type,$model,$attribute,$htmlOptions)
	{
		$htmlOptions['type']=$type;
		if($type==='file')
			unset($htmlOptions['value']);
		else if(!isset($htmlOptions['value']))
			$htmlOptions['value']=$model->$attribute;
		if($model->hasErrors($attribute))
			self::addErrorCss($htmlOptions);
		return self::tag('input',$htmlOptions);
	}

	/**
	 * Generates the list options.
	 * @param mixed the selected value(s). This can be either a string for single selection or an array for multiple selections.
	 * @param array the option data (see {@link listData})
	 * @param array additional HTML attributes. The following two special attributes are recognized:
	 * <ul>
	 * <li>prompt: string, specifies the prompt text shown as the first list option. Its value is empty.</li>
	 * <li>empty: string, specifies the text corresponding to empty selection. Its value is empty.</li>
	 * <li>options: array, specifies additional attributes for each OPTION tag.
	 *     The array keys must be the option values, and the array values are the extra
	 *     OPTION tag attributes in the name-value pairs. For example,
	 * <pre>
	 *     array(
	 *         'value1'=>array('disabled'=>true, 'label'=>'value 1'),
	 *         'value2'=>array('label'=>'value 2'),
	 *     );
	 * </pre>
	 *     This option has been available since version 1.0.3.
	 * </li>
	 * </ul>
	 * @return string the generated list options
	 */
	public static function listOptions($selection,$listData,&$htmlOptions)
	{
		$content='';
		if(isset($htmlOptions['prompt']))
		{
			$content.='<option value="">'.self::encode($htmlOptions['prompt'])."</option>\n";
			unset($htmlOptions['prompt']);
		}
		if(isset($htmlOptions['empty']))
		{
			$content.='<option value="">'.self::encode($htmlOptions['empty'])."</option>\n";
			unset($htmlOptions['empty']);
		}

		if(isset($htmlOptions['options']))
		{
			$options=$htmlOptions['options'];
			unset($htmlOptions['options']);
		}
		else
			$options=array();

		foreach($listData as $key=>$value)
		{
			if(is_array($value))
			{
				$content.='<optgroup label="'.self::encode($key)."\">\n";
				$dummy=array();
				$content.=self::listOptions($selection,$value,$dummy);
				$content.='</optgroup>'."\n";
			}
			else
			{
				$attributes=array('value'=>(string)$key);
				if(!is_array($selection) && !strcmp($key,$selection) || is_array($selection) && in_array($key,$selection))
					$attributes['selected']='selected';
				if(isset($options[$key]))
					$attributes=array_merge($attributes,$options[$key]);
				$content.=CHtml::tag('option',$attributes,self::encode((string)$value))."\n";
			}
		}
		return $content;
	}

	/**
	 * Generates the JavaScript with the specified client changes.
	 * @param string event name (without 'on')
	 * @param array HTML attributes which may contain the following special attributes
	 * specifying the client change behaviors:
	 * <ul>
	 * <li>submit: string, specifies the URL that the button should submit to. If empty, the current requested URL will be used.</li>
	 * <li>params: array, name-value pairs that should be submitted together with the form. This is only used when 'submit' option is specified.</li>
	 * <li>return: boolean, the return value of the javascript. Defaults to false, meaning that the execution of
	 * javascript would not cause the default behavior of the event. This option has been available since version 1.0.2.</li>
	 * <li>confirm: string, specifies the message that should show in a pop-up confirmation dialog.</li>
	 * <li>ajax: array, specifies the AJAX options (see {@link ajax}).</li>
	 * </ul>
	 */
	protected static function clientChange($event,&$htmlOptions)
	{
		if(!isset($htmlOptions['submit']) && !isset($htmlOptions['confirm']) && !isset($htmlOptions['ajax']))
			return;

		if(isset($htmlOptions['return']) && $htmlOptions['return'])
			$return='return true';
		else
			$return='return false';

		if(isset($htmlOptions['on'.$event]))
		{
			$handler=trim($htmlOptions['on'.$event],';').';';
			unset($htmlOptions['on'.$event]);
		}
		else
			$handler='';

		if(isset($htmlOptions['id']))
			$id=$htmlOptions['id'];
		else
			$id=$htmlOptions['id']=isset($htmlOptions['name'])?$htmlOptions['name']:self::ID_PREFIX.self::$count++;

		$cs=Yii::app()->getClientScript();
		$cs->registerCoreScript('jquery');

		if(isset($htmlOptions['params']))
			$params=CJavaScript::encode($htmlOptions['params']);
		else
			$params='{}';

		if(isset($htmlOptions['submit']))
		{
			$cs->registerCoreScript('yii');
			if($htmlOptions['submit']!=='')
				$url=CJavaScript::quote(self::normalizeUrl($htmlOptions['submit']));
			else
				$url='';
			$handler.="jQuery.yii.submitForm(this,'$url',$params);{$return};";
		}

		if(isset($htmlOptions['ajax']))
			$handler.=self::ajax($htmlOptions['ajax'])."{$return};";

		if(isset($htmlOptions['confirm']))
		{
			$confirm='confirm(\''.CJavaScript::quote($htmlOptions['confirm']).'\')';
			if($handler!=='')
				$handler="if($confirm) {".$handler."} else return false;";
			else
				$handler="return $confirm;";
		}

		$cs->registerScript('Yii.CHtml.#'.$id,"jQuery('#$id').$event(function(){{$handler}});");
		unset($htmlOptions['params'],$htmlOptions['submit'],$htmlOptions['ajax'],$htmlOptions['confirm'],$htmlOptions['return']);
	}

	/**
	 * Generates input name and ID for a model attribute.
	 * This method will update the HTML options by setting appropriate 'name' and 'id' attributes.
	 * @param CModel the data model
	 * @param string the attribute
	 * @param array the HTML options
	 */
	protected static function resolveNameID($model,&$attribute,&$htmlOptions)
	{
		if(!isset($htmlOptions['name']))
			$htmlOptions['name']=self::resolveName($model,$attribute);
		if(!isset($htmlOptions['id']))
			$htmlOptions['id']=self::getIdByName($htmlOptions['name']);
	}

	/**
	 * Generates input name for a model attribute.
	 * @param CModel the data model
	 * @param string the attribute
	 * @return string the input name
	 * @since 1.0.2
	 */
	protected static function resolveName($model,&$attribute)
	{
		if(($pos=strpos($attribute,'['))!==false)
		{
			$sub=substr($attribute,$pos);
			$attribute=substr($attribute,0,$pos);
			return get_class($model).$sub.'['.$attribute.']';
		}
		else
			return get_class($model).'['.$attribute.']';
	}

	/**
	 * Appends {@link errorCss} to the 'class' attribute.
	 * @param array HTML options to be modified
	 */
	protected static function addErrorCss(&$htmlOptions)
	{
		if(isset($htmlOptions['class']))
			$htmlOptions['class'].=' '.self::$errorCss;
		else
			$htmlOptions['class']=self::$errorCss;
	}
}
