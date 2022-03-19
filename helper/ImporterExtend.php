<?php

namespace app\helper;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use Gevman\Yii2Excel\Importer;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Gevman\Yii2Excel\Exception\ImporterException;

class ImporterExtend extends Importer
{
    public function init()
    {
        $this->filePath = Yii::getAlias($this->filePath);

        try {
            $spreadsheet = IOFactory::load($this->filePath);
            $this->rows = $spreadsheet->getActiveSheet()->toArray();
            if ($this->skipFirstRow) {
                array_shift($this->rows);
            }
            $this->rows = array_map('array_filter', $this->rows);
            $this->rows = array_filter($this->rows);

            $this->process();
        } catch (Exception $e) {
            throw new ImporterException($e->getMessage(), $e->getCode(), $e);
        }
    }
    protected function process()
    {
        foreach ($this->rows as $index => $row) {
            if($index < 501){
                /** @var ActiveRecord $model */
                $model = (new $this->activeRecord);
                if ($this->scenario) {
                    $model->setScenario($this->scenario);
                }
                $attributes = [];
                foreach ($this->fields as $field) {
                    if (!($attribute = ArrayHelper::getValue($field, 'attribute'))) {
                        throw new ImporterException('attribute missing from one of your fields');
                    }
                    if (!($value = ArrayHelper::getValue($field, 'value'))) {
                        throw new ImporterException('value missing from one of your fields');
                    }
                    if (!is_callable($value) && !array_key_exists($value, $row)) {
                        throw new ImporterException("index `$value` not found in row");
                    }
                    if (is_callable($value)) {
                        $value = $value($row);
                    } else {
                        $value = $row[$value];
                    }

                    $attributes[$attribute] = $value;
                }
                $model->setAttributes($attributes);
                $this->models[$index] = $model;
            }

        }
    }
}