<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php"); 
CModule::IncludeModule("fileman");
$LHE = new CLightHTMLEditor;
$LHE->Show(array(
	'id' => $_REQUEST['ID'],
	'content' => $_REQUEST['CONTENT'],
	'inputName' => $_REQUEST['INPUT_NAME'],
	'inputId' => $_REQUEST['INPUT_ID'],
	'width' => '100%',
	'height' => '100%',
	'bUseFileDialogs' => false,
	'jsObjName' => $_REQUEST['JS_OBJ_NAME'],
	'toolbarConfig' => array(
		'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat',
		'CreateLink', 'DeleteLink', 'Image', 'Video',
		'BackColor', 'ForeColor',
		'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyFull',
		//'=|=',
		'InsertOrderedList', 'InsertUnorderedList', 'Outdent', 'Indent',
		'StyleList', 'HeaderList',
		'FontList', 'FontSizeList',
	),
	'videoSettings' => false,
	'bResizable' => false,
	'bAutoResize' => false
));
?>