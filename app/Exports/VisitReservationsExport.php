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
                'Instansi' => $visitSchedule->visitor_company,
                'Nama Pengunjung' => $visitSchedule->visitor_name,
                'Jumlah Pengunjung' => $visitSchedule->visitor_person,
                'Jumlah Kendaraan' => $visitSchedule->visitor_jumlah_kendaraan,
                'Tujuan Kunjungan' => $visitSchedule->visitor_purphose,
                'Kontak Pengunjung' => $visitSchedule->visitor_contact,
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
            'Instansi',
            'Nama Pengunjung',
            'Jumlah Pengunjung',
            'Jumlah Kendaraan',
            'Tujuan Kunjungan',
            'Kontak Pengunjung',
        ];
    }
}

