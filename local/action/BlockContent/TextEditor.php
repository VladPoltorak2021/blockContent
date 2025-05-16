<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php"); 
CModule::IncludeModule("fileman");

if (!function_exists('validBase64')) {
	function validBase64($string) {
		if (empty($string)) return true;
		$decoded = base64_decode($string, true);
		if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $string)) return false;
		if (!base64_decode($string, true)) return false;
		if (base64_encode($decoded) != $string) return false;
		return true;
	}
}


$content = $_REQUEST['CONTENT'];

?>
<div class="wrap-field-block-content">
Стиль вывода:
<select name="<?=htmlspecialcharsbx($_REQUEST['INPUT_NAME']).'[view]';?>" class="block-content-select js-change-view-text">
    <option value="">Не задан</option>
    <option value="note"<? if($_REQUEST['VIEW'] == 'note') echo ' selected';?>>Сноска</option>
    <option value="strong"<? if($_REQUEST['VIEW'] == 'strong') echo ' selected';?>>Жирный текст</option>
    <option value="clear"<? if($_REQUEST['VIEW'] == 'clear' || !isset($_REQUEST['VIEW'])) echo ' selected';?>>Текст без оберток</option>
</select>
Отступ снизу:
<select name="<?=htmlspecialcharsbx($_REQUEST['INPUT_NAME']).'[marginBottom]';?>" class="block-content-select">
    <option value="Y"<? if($_REQUEST['MARGIN_BOTTOM'] == 'Y') echo ' selected';?>>Есть</option>
    <option value="N"<? if($_REQUEST['MARGIN_BOTTOM'] == 'N') echo ' selected';?>>Нет</option>
</select>
<?

$LHE = new CHTMLEditor;
$LHE->Show(array(
	'id' => $_REQUEST['ID'],
	'content' => htmlspecialcharsback($content),
	'inputName' => $_REQUEST['INPUT_NAME'].'[content]',
	'inputId' => $_REQUEST['INPUT_ID'],
	'width' => '100%',
	'height' => '500',
	'bUseFileDialogs' => false,
	'jsObjName' => $_REQUEST['JS_OBJ_NAME'],
	'toolbarConfig' => array(
	),
	'videoSettings' => false,
	'bResizable' => false,
	'bAutoResize' => false
));
?>
<input type="hidden" name="<?=htmlspecialcharsbx($_REQUEST['INPUT_NAME'])?>[type]" value="text">
<script>
$(function(){
	$(".bxhtmled-taskbar-cnt[data-bx-type='taskbarmanager']").remove();
});
</script>
</div>