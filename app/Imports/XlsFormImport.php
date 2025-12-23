<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;

class XlsFormImport implements WithMultipleSheets
{
    public array $sheets = [];

    public function sheets(): array
    {
        return [
            'survey' => new GenericSheetImport('survey', $this),
            'choices' => new GenericSheetImport('choices', $this),
            'settings' => new GenericSheetImport('settings', $this),
        ];
    }
}

class GenericSheetImport implements ToCollection
{
    private string $name;
    private XlsFormImport $parent;

    public function __construct(string $name, XlsFormImport $parent) {
        $this->name = $name;
        $this->parent = $parent;
    }

    public function collection(Collection $rows) {
        $this->parent->sheets[$this->name] = $rows->toArray();
    }
}
