<?php

namespace App\Exports;

use App\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

//WithCustomStartCell, 

class ClientExport implements FromCollection, WithHeadings, WithMapping
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
            '#',
            'Registration Date',
            'First Name',
            'Last Name',
            'Email',
            'Secondary Email',
            'DOB',
            'Contact',
            'Passport Number',
            'Process Address',
            'Nationality',
            'Work Status'
        ];
    }

    public function map($row): array
    {
        $fields = [
            $row->id,
            $row->registration_date,
            $row->first_name,
            $row->last_name,
            $row->email,
            $row->secondary_email,
            $row->dob,
            $row->contact,
            $row->passport_number,
            $row->process_address,
            $row->nationality,
            $row->work_status
        ];

        return $fields;
    }
}
