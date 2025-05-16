<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php"); 
CModule::IncludeModule("form");

$show_image = htmlspecialcharsbx($_REQUEST['IMAGE']);
$after_name = '[content]';
$after_name = '[n#IND#]';

$show_image = array();
foreach($_REQUEST['IMAGE'] as $key=>$image)
{
    $show_image[$_REQUEST['INPUT_NAME'].'[content]['.$key.']'] = $image;
}

echo \Bitrix\Main\UI\FileInput::createInstance(array(
				"name" => htmlspecialcharsbx($_REQUEST['INPUT_NAME']).$after_name, //имя должно быть уникально
				"description" => true, //разрешить устанавливать description
				"upload" => true, //запрещает загрузку
				"medialib" => true, //разрешить выбрать из медиабиблиотеки
				"fileDialog" => true,
				"cloud" => true,
				"delete" => true, //можно удалять элемент
				"maxCount" => htmlspecialcharsbx($_REQUEST['COUNT']), //кол-во эл-в
				"allowUpload" => "A", //может принимать значения A,F,I (A,F - файлы, I - картинка)
				//"allowUploadExt" => ".png", //устанавливает допустимое расширение загружаемого файла
				"allowSort" => "Y" //можно сортировать эл-ты
			))->show($show_image);

?>
<input type="hidden" name="<?=htmlspecialcharsbx($_REQUEST['INPUT_NAME'])?>[type]" value="<?=htmlspecialcharsbx($_REQUEST['TYPE'])?>">
