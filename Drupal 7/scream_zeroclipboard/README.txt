Для качественного использования:
1. Создаём в форме элемент на который будут нажимать дабы скопровать в буфер:
<div class="copy_btn_wrap"><div id="copy_btn">скопировать в буфер</div></div>
2. Стилим через позишн абсолют чтобы поверх элемента было
.copy_btn_wrap {
    background-color: #FFFF00;
    bottom: 45px;
    padding: 3px;
    position: absolute;
    right: 54px;
	border: 1px solid #808080;
}
3. Прописываем в настройках модуля, где #scream-result-wrapper textarea - поле с которого будет копироваться в буфер
div#copy_btn|#scream-result-wrapper textarea

Альтернативный вариант для пункта 3 использование напрямую функции в коде
<script>Drupal.scream_zeroClipboard.process(element_selector, element_copy_from);</script>
