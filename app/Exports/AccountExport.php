<?php

namespace App\Exports;

use App\Account;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

//WithCustomStartCell, 

class AccountExport implements FromCollection, WithHeadings, WithMapping
{
    public $collection;

    public function collection()
    {
        // return Client::all();
        return (!empty($this->collection) && $this->collection instanceof Collection) ? $this->collection : [];
    }

    /*public function startCell(): string
    {
        return 'A2';
    }*/
    
    public function headings(): array
    {
        return [
            'Author Details',
            'Date',
            'Client Name',
            'Total Fees',
            'Purpose'
        ];
    }

    public function map($row): array
    {
        $fields = [
            $row->created_by,
            $row->date,
            $row->client_id,
            $row->received_amount,
            $row->purpose_article_id
        ];

        return $fields;
    }
}
