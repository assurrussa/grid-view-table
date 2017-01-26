<?php
if(!function_exists('amiGrid')) {
    /**
     * @return \Assurrussa\GridView\GridView
     */
    function amiGrid()
    {
        return app(\Assurrussa\GridView\GridView::NAME);
    }
}
if(!function_exists('amiGridColumnCeil')) {
    /**
     * @return \Assurrussa\GridView\Support\ColumnCeil
     */
    function amiGridColumnCeil()
    {
        return \Assurrussa\GridView\GridView::columnCeil();
    }
}
if(!function_exists('isInt')) {
    /**
     * Проверяет содержит ли переменная целое значение.
     * Возвращает true если типа integer или содержит только допустимые символы без учета начальных и конечных пробелов
     * Не обрабатывает неопределенные значения! Для безопасной проверки элемента массива или свойства объекта используйте вместе с value().
     *
     * @param integer|string $val переменная
     * @return boolean
     */
    function isInt($value)
    {
        // При преобразовании в int игнорируются все окружающие пробелы, но is_numeric допускает только пробелы в начале
        return is_bool($value) ? false : filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
}