<?php
/**
 * Created by PhpStorm.
 * User: lifuren
 * Date: 2019/7/21
 * Time: 00:59
 */

namespace Demo\Imports;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

trait ExportTrait
{
    public $isFirst = false;

    public function __construct()
    {
        $this->ret[] = self::$columns + ['处理结果'];
        HeadingRowFormatter::default(HeadingRowFormatter::FORMATTER_NONE);
    }

    public function validateHeading($row)
    {
        if (!$this->isFirst) {
            $this->isFirst = true;
            Validator::make(array_values($row->toArray()), $this->rule(), $this->message())->validate();
            return true;
        }
        return false;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    private function message(): array
    {
        $rules = [];
        foreach (self::$columns as $key=>$column) {
            $rules[$key.'.in'] = '第'.($key+1).'列为:'.$column;
        }
        return $rules;
    }

    private function rule(): array
    {
        $rules = [];
        foreach (self::$columns as $key=>$column) {
            $rules[$key] = Rule::in($column);
        }
        return $rules;
    }

    public function startRow(): int
    {
        return 1;
    }
}