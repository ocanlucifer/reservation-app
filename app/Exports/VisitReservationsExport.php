<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VisitReservationsExport implements FromCollection, WithHeadings
{
    protected $visitSchedules;

    public function __construct($visitSchedules)
    {
        $this->visitSchedules = $visitSchedules;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->visitSchedules as $visitSchedule) {
            $data[] = [
                'Nama Gedung' => $visitSchedule->schedule->building->name,
                'Tanggal' => $visitSchedule->schedule->tanggal,
                'Jam Mulai' => \Carbon\Carbon::parse($visitSchedule->schedule->start_time)->format('H:i'),
                'Jam Selesai' => \Carbon\Carbon::parse($visitSchedule->schedule->end_time)->format('H:i'),
                'Status' => $visitSchedule->is_booked ? "sudah di pesan" : "belum di pesan",
                'Company' => $visitSchedule->visitor_company,
                'Jumlah Pengunjung' => $visitSchedule->visitor_person,
                'Tujuan Kunjungan' => $visitSchedule->visitor_purphose,
                'Alamat Pengunjung' => $visitSchedule->visitor_address,
                'Kontak Pengunjung' => $visitSchedule->visitor_contact,
                'Note Pengunjung' => $visitSchedule->visitor_note,
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
            'Company',
            'Jumlah Pengunjung',
            'Tujuan Kunjungan',
            'Alamat Pengunjung',
            'Kontak Pengunjung',
            'Note Pengunjung',
        ];
    }
}

