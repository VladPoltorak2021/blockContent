<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("form");

$show_image = htmlspecialcharsbx($_REQUEST['IMAGE']);
$after_name = '[image]';

$numberOne = uniqid();
$numberTwo = uniqid();
?>

<br>
<div class="quote-items<?echo $numberOne?> ">
    <?php if (isset($_REQUEST['TEXT2']) && is_array($_REQUEST['TEXT2'])) {
        foreach ($_REQUEST['TEXT2'] as $key => $text2) { ?>
            <div class="grid-quote-item">
                <label>
                Текст ссылки: <input size="60" type="text" name="<?= htmlspecialcharsbx($_REQUEST['INPUT_NAME']) . '[text2][]'; ?>"
                                value="<?= htmlspecialcharsbx($text2) ?>">
                <!-- Здесь можно добавить другие поля для редактирования цитаты -->
                </label>
                <label>
               Ссылка: <input size="60" type="text" name="<?= htmlspecialcharsbx($_REQUEST['INPUT_NAME']) . '[header2][]'; ?>"
                                value="<?= htmlspecialcharsbx($_REQUEST['HEADER2'][$key]) ?>">
                <!-- Здесь можно добавить другие поля для редактирования цитаты -->
                </label>
            </div>
        <?php }
    } ?>
</div>
<button type="button" class="quote-add-button<?echo $numberTwo?>">Добавить еще ссылку</button>

<script>
    $(document).on('click', '.quote-add-button<?echo $numberTwo?>', function () {

        var quoteItems = $(this).prev('.quote-items<?echo $numberOne?>');
        console.log(quoteItems)
        var newQuoteItem = `
             <div class="grid-quote-item">
                <label>
                Текст ссылки: <input size="60" type="text" name="<?= htmlspecialcharsbx($_REQUEST['INPUT_NAME']) . '[text2][]'; ?>">
                <!-- Здесь можно добавить другие поля для редактирования цитаты -->
                </label>
                <label>
               Ссылка: <input size="60" type="text" name="<?= htmlspecialcharsbx($_REQUEST['INPUT_NAME']) . '[header2][]'; ?>">
                <!-- Здесь можно добавить другие поля для редактирования цитаты -->
                </label>
            </div>
        `;
        quoteItems.append(newQuoteItem);
    });
</script>

<input type="hidden" name="<?=htmlspecialcharsbx($_REQUEST['INPUT_NAME'])?>[type]" value="link">
<input type="hidden" name="INPUT_NAME" value="<?=htmlspecialcharsbx($_REQUEST['INPUT_NAME'])?>">
