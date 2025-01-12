<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BuildingScheduleExport implements FromCollection, WithHeadings
{
    protected $buildingSchedules;

    public function __construct($buildingSchedules)
    {
        $this->buildingSchedules = $buildingSchedules;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->buildingSchedules as $buildingSchedule) {
            $data[] = [
                'Nama Gedung' => $buildingSchedule->building->name,
                'Tanggal' => $buildingSchedule->tanggal,
                'Jam Mulai' => \Carbon\Carbon::parse($buildingSchedule->start_time)->format('H:i'),
                'Jam Selesai' => \Carbon\Carbon::parse($buildingSchedule->end_time)->format('H:i'),
                'Status' => $buildingSchedule->is_booked ? "sudah di pesan" : "belum di pesan",
                'Schedule No.' => $buildingSchedule->transaction_number,
                'Pembuat Jadwal' => $buildingSchedule->creator->name ,
                'Tanggal Pembuatan Jadwal' => $buildingSchedule->created_at->format('d M Y H:i'),
                'Peng Update Jadwal' => $buildingSchedule->update_by ? $buildingSchedule->updater->name : 'Belum Diperbarui' ,
                'Tanggal Update Jadwal' => $buildingSchedule->update_by ? $buildingSchedule->updated_at->format('d M Y H:i') : 'Belum Diperbarui',
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Nama Gedung',
            'Tanggal',
            'Jam Mulai',
            'Jam Selesai',
            'Status',
            'Schedule No.',
            'Pembuat Jadwal',
            'Tanggal Pembuatan Jadwal',
            'Peng Update Jadwal',
            'Tanggal Update Jadwal',
        ];
    }
}

