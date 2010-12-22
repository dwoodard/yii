<!DOCTYPE html PUBLIC
	"-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title><?php echo CHtml::encode($data['type'])?></title>

<style type="text/css">
/*<![CDATA[*/
html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,font,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td{border:0;outline:0;font-size:100%;vertical-align:baseline;background:transparent;margin:0;padding:0;}
body{line-height:1;}
ol,ul{list-style:none;}
blockquote,q{quotes:none;}
blockquote:before,blockquote:after,q:before,q:after{content:none;}
:focus{outline:0;}
ins{text-decoration:none;}
del{text-decoration:line-through;}
table{border-collapse:collapse;border-spacing:0;}

body {
	font-family: "Verdana";
	color: #000;
	background: #fff;
	font-size: 9pt;
}

h1 {
	font: normal 18pt "Verdana";
	color: #f00;
	margin-bottom: .5em;
}

h2 {
	font: normal 14pt "Verdana";
	color: #800000;
	margin-bottom: .5em;
}

h3 {
	font: bold 11pt "Verdana";
}

pre {
	font-family: "Lucida Console";
	font-size: 10pt;
}

.container {
	margin: 1em 4em;
}

.version {
	color: gray;
	font-size: 8pt;
	border-top: 1px solid #aaa;
	padding-top: 1em;
	margin-bottom: 1em;
}

.message {
	color: #000;
	padding: 1em;
	font-size: 11pt;

	background: #f3f3f3;
	-webkit-border-radius: 15px;
	-moz-border-radius: 15px;
	border-radius: 15px;

	margin-bottom: 1em;
	line-height: 160%;
}

.source {
	margin-bottom: 1em;
}

.source pre {
	font-family: "Lucida Console";
	font-weight: normal;
	background-color: #ffe;
}

.source .file {
	margin-bottom: 1em;
	font-weight: bold;
}

.source table {
	width: 100%;
}

.source th {
	font-weight: normal;
	padding-right: 10px;
	width: 50px;
	text-align: right;
}

.source .error th {
	font-weight: bold;
}

.error {
	background-color: #fce3e3;
}

.trace {
	margin-bottom: 1em;
}

.trace td {
	font: normal 9pt "Verdana";
	padding: .3em;
	vertical-align: top;
}

.trace .number {
	text-align: right;
}

.trace .method {
	margin-bottom: .6em;
}

.trace .core {
	color: #444;
}

.trace .app {
	border: 1px dashed #cc0000;
}

.trace .app .number {
	font-weight: bold;
}

.file {
	color: #666;
}
/*]]>*/
</style>
</head>

<body>
<div class="container">
<h1><?php echo $data['type']?></h1>

<p class="message">
<?php echo nl2br(CHtml::encode($data['message']))?>
</p>

<div class="source">
<p class="file"><?php echo CHtml::encode($data['file'])."({$data['line']})"?></p>
<?php echo $this->renderSource($data); ?>
</div>

<div class="trace">
	<h2>Stack Trace</h2>
	<?php echo $this->renderTrace($data); ?>
</div>

<div class="version">
<?php echo date('Y-m-d H:i:s',$data['time']) .' '. $data['version']; ?>
</div>

</div>
</body>
</html>