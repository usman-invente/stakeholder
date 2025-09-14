<?php

namespace App\Exports;

use App\Models\Visitor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VisitorsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $startDate;
    protected $endDate;
    
    /**
     * Optional constructor to filter by date range
     * 
     * @param string|null $startDate
     * @param string|null $endDate
     */
    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate ?: date('Y-m-d');
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Visitor::query()->orderBy('check_in_time', 'desc');
        
        if ($this->startDate) {
            $query->whereDate('check_in_time', '>=', $this->startDate);
        }
        
        if ($this->endDate) {
            $query->whereDate('check_in_time', '<=', $this->endDate);
        }
        
        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Full Name',
            'Email',
            'Card No.',
            'Contact Number',
            'Host Name',
            'Host Email',
            'Check-in Time',
            'Check-out Time',
            'Meeting ID'
        ];
    }

    /**
     * @param Visitor $visitor
     * @return array
     */
    public function map($visitor): array
    {
        return [
            $visitor->id,
            $visitor->full_name,
            $visitor->email ?? 'N/A',
            $visitor->card_no ?? 'N/A',
            $visitor->contact_number,
            $visitor->host_name,
            $visitor->host_email,
            $visitor->check_in_time ? $visitor->check_in_time->format('Y-m-d H:i:s') : 'N/A',
            $visitor->check_out_time ? $visitor->check_out_time->format('Y-m-d H:i:s') : 'Not checked out',
            $visitor->meeting_id ?? 'N/A'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
    
    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:J1')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFD9EAD3']
                    ]
                ]);
                
                $event->sheet->getStyle('A1:J1')->getFont()->setBold(true);
                
                // Auto-adjust column widths
                foreach ($event->sheet->getColumnIterator() as $column) {
                    $column = $column->getColumnIndex();
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
                
                // Add title with date range
                $title = 'Visitor Records';
                if ($this->startDate && $this->endDate) {
                    $startDate = date('M d, Y', strtotime($this->startDate));
                    $endDate = date('M d, Y', strtotime($this->endDate));
                    $title .= ' (' . $startDate . ' to ' . $endDate . ')';
                } elseif ($this->endDate) {
                    $endDate = date('M d, Y', strtotime($this->endDate));
                    $title .= ' (up to ' . $endDate . ')';
                }
                
                $event->sheet->setCellValue('A1', $title);
                $event->sheet->mergeCells('A1:J1');
                $event->sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $event->sheet->getStyle('A1')->getFont()->setSize(16);
                
                // Reinsert the headers in row 2
                $headers = $this->headings();
                foreach ($headers as $index => $header) {
                    $column = chr(65 + $index); // Convert 0 to A, 1 to B, etc.
                    $event->sheet->setCellValue($column.'2', $header);
                }
                
                $event->sheet->getStyle('A2:J2')->getFont()->setBold(true);
                $event->sheet->getStyle('A2:J2')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFCFE2F3']
                    ]
                ]);
            },
        ];
    }
}
