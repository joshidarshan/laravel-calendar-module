<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EmployeeReportExport implements FromArray, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    /** Data */
    public function array(): array
    {
        return $this->rows;
    }

    /** Excel Header Row */
    public function headings(): array
    {
        return [
            'Employee',
            'Total',
            'Score',
            'Pending',
            'Progress',
            'Completed',
            'Overdue',
        ];
    }

    /** Row Mapping (FORMAT HERE) */
    public function map($row): array
    {
        return [
            $row['employee'],
            $row['total'],
            $row['score'] . '%',   // ⭐ PERCENT FORMAT
            $row['pending'],
            $row['progress'],
            $row['completed'],
            $row['overdue'],
        ];
    }
}
