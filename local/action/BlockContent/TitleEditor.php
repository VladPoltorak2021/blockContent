<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("form");
?>
Размер заголовка:
<select name="<?=htmlspecialcharsbx($_REQUEST['INPUT_NAME']).'[size]';?>" class="block-content-select">
    <?
    for ($i=1;$i<=6;$i++) {
        ?>
        <option value="h<?=$i?>"<? if($_REQUEST['SIZE'] == 'h'.$i) echo ' selected';?>>h<?=$i?></option>
        <?
    }
    ?>
</select>
<br>
<br>

Текст:
<textarea cols="40" rows="3" name="<?= htmlspecialcharsbx($_REQUEST['INPUT_NAME']) . '[content]'; ?>"><?= htmlspecialcharsbx($_REQUEST['CONTENT']) ?></textarea>

<br>
<br>
<span id="hint_<?=$_REQUEST['INPUT_NAME']?>"></span> ID блока: <input size="40" type="text" name="<?= htmlspecialcharsbx($_REQUEST['INPUT_NAME']) . '[text]'; ?>"
                value="<?= htmlspecialcharsbx($_REQUEST['TEXT']) ?>">

<br>
<input type="hidden" name="<?=htmlspecialcharsbx($_REQUEST['INPUT_NAME'])?>[type]" value="title">

<script>
    BX.hint_replace(BX('hint_<?=$_REQUEST['INPUT_NAME']?>'), 'Латинские символы без пробелов');
</script>