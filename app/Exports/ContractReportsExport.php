<?php

namespace App\Exports;

use App\Models\Contract;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ContractReportsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    private $reportType;
    private $data;
    
    public function __construct($reportType = 'all', $data = null)
    {
        $this->reportType = $reportType;
        $this->data = $data;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        switch ($this->reportType) {
            case 'expiring_3_months':
                return Contract::with('department')
                    ->whereBetween('expiry_date', [Carbon::now(), Carbon::now()->addMonths(3)])
                    ->where('status', '!=', 'expired')
                    ->orderBy('expiry_date', 'asc')
                    ->get();
            case 'overdue':
                return Contract::with('department')
                    ->whereDate('expiry_date', '<', Carbon::now())
                    ->where('status', 'expired')
                    ->orderBy('expiry_date', 'desc')
                    ->get();
            case 'recent_activity':
                return Contract::with('department')
                    ->where('created_at', '>=', Carbon::now()->subMonth())
                    ->orderBy('created_at', 'desc')
                    ->get();
            default:
                return Contract::with('department')
                    ->where('status', '!=', 'expired')
                    ->orderBy('expiry_date', 'asc')
                    ->get();
        }
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Contract ID',
            'Title',
            'Supplier Name',
            'Department',
            'Start Date',
            'Expiry Date',
            'Days Until Expiry',
            'Contract Value',
            'Status',
            'Contract Owner',
            'Owner Email',
            'Renewal Terms',
            'Auto Notifications',
            'Created Date',
        ];
    }

    /**
     * @param mixed $contract
     * @return array
     */
    public function map($contract): array
    {
        return [
            $contract->contract_id,
            $contract->contract_title,
            $contract->supplier_name,
            $contract->department->name,
            $contract->start_date ? $contract->start_date->format('d/m/Y') : '',
            $contract->expiry_date ? $contract->expiry_date->format('d/m/Y') : '',
            $contract->days_until_expiry,
            $contract->contract_value ? '£' . number_format($contract->contract_value, 2) : '',
            ucfirst($contract->status),
            $contract->contract_owner,
            $contract->contract_owner_email,
            ucwords(str_replace('-', ' ', $contract->renewal_terms)),
            $contract->auto_renewal ? 'Yes' : 'No',
            $contract->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        $titles = [
            'expiring_3_months' => 'Contracts Expiring (Next 3 Months)',
            'overdue' => 'Overdue Contract Renewals',
            'recent_activity' => 'Recent Contract Activity',
            'all' => 'All Active Contracts'
        ];
        
        return $titles[$this->reportType] ?? 'Contract Report';
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as a header
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
