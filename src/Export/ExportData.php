<?php

declare(strict_types=1);


namespace Assurrussa\GridView\Export;

use Assurrussa\GridView\Helpers\A;
use Assurrussa\GridView\Helpers\F;


/**
 * Class ExportData
 *
 * @package App\Common\Export
 */
class ExportData
{
    /**
     * Создаем текст для экспорта данных и выдаем в браузер для скачивания
     *
     *
     *      пример array $fields = [
     *                      'ID'            => 'id',
     *                      'Время звонка'  => 'setup_at',
     *                      0               => 'brand.name',
     *                      'Гости'         => function() {return 'name';},
     *                  ];
     *
     * @param \Illuminate\Database\Eloquent\Builder $query  модель с критериями для получения данных
     * @param bool|array                            $fields список полей для экспорта,
     *                                                      значение в массиве м.б. или название поля модели или путь до
     *                                                      поля релейшена или функция для создания значения. Если
     *                                                      значение задано с ключом, то ключ используется для первой
     *                                                      строки описания полей. Если значение функция, то оно
     *                                                      обязательно д.б. с ключом.
     * @param string                                $filename
     * @param int                                   $cacheSecond
     * @param string                                $format
     * @param string                                $contentType
     *
     * @return array
     */
    public function fetch(
        \Illuminate\Database\Eloquent\Builder $query,
        array $fields = null,
        string $filename = null,
        int $cacheSecond = 60,
        string $format = 'csv',
        string $contentType = 'text/csv'
    ): array {
        $keyCache = class_basename($query->getModel());
        $pathCache = storage_path('app') . DIRECTORY_SEPARATOR . 'fetch';

        $dataString = '';
        $fieldsString = '';
        $fields = $fields ? [$fields] : [$query->getModel()->getFillable()];
        $fields = \Illuminate\Support\Arr::first($fields);
        array_walk($fields, function ($value, $key) use (&$fieldsString) {
            $fieldsString .= '"' . ($this->isInt($key) ? $value : $key) . '";';
        });
        $dataString .= $fieldsString . PHP_EOL;

        $fileCache = $pathCache . DIRECTORY_SEPARATOR . md5($keyCache . $dataString);
        $timeCacheExpire = $cacheSecond;
        if (file_exists($fileCache) && time() - filemtime($fileCache) < $timeCacheExpire) {
            $dataString = file_get_contents($fileCache);
        } else {
            $query->chunk(1000, function ($data) use ($fields, &$dataString) {
                foreach ($data as $item) {
                    $dataExport = [];
                    if ($data) {
                        foreach ($fields as $field) {
                            // достаем значение по пути $field или выполняем функцию $field для $data
                            $dataExport[] = A::value($item, $field);
                        }
                    } else {
                        continue;
                    }
                    $dataString .= A::join($dataExport, ';', '"', '"') . "\n";
                }
            });
            if (!empty($dataString)) {
                if (!file_exists($pathCache)) {
                    F::createDirectory($pathCache);
                }
                file_put_contents($fileCache, $dataString);
            }
        }

        if (!$filename) {
            $filename = 'export' . $keyCache . '.' . $format;
        }

        $strLen = strlen($dataString);

        unset($dataString);

        return [
            'path'                => $fileCache,
            'filename'            => $filename,
            'Content-Type'        => $contentType,
            'Accept-Ranges'       => 'bytes',
            'Content-Length'      => $strLen,
            'Content-disposition' => 'attachment; filename="' . $filename . '"',
        ];
    }

    /**
     * Проверяет содержит ли переменная целое значение.
     * Возвращает true если типа integer или содержит только допустимые символы без учета начальных и конечных пробелов
     * Не обрабатывает неопределенные значения! Для безопасной проверки элемента массива или свойства объекта используйте вместе с value().
     *
     * @param integer|string $val переменная
     *
     * @return boolean
     */
    protected function isInt($value): bool
    {
        // При преобразовании в int игнорируются все окружающие пробелы, но is_numeric допускает только пробелы в начале
        return is_bool($value) ? false : filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
}
