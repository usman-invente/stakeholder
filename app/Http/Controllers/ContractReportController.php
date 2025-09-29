<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ContractReportController extends Controller
{
    /**
     * Display the monthly contract reports dashboard
     */
    public function index()
    {
        $currentDate = Carbon::now();
        
        // Contracts expiring in next 3 months
        $expiringNext3Months = $this->getContractsExpiringInPeriod($currentDate, $currentDate->copy()->addMonths(3));
       
        // Contracts overdue for renewal
        $overdueContracts = $this->getOverdueContracts();
        
        // Recent contract activity
        $recentActivity = $this->getRecentContractActivity();
        
        // Monthly breakdown
        $monthlyBreakdown = $this->getMonthlyExpiryBreakdown();
        // Department statistics
        $departmentStats = $this->getDepartmentStatistics();
        
        // Supplier statistics
        $supplierStats = $this->getSupplierStatistics();
        
        // Contract value statistics
        $valueStats = $this->getContractValueStatistics();
        
        return view('contracts.reports.index', compact(
            'expiringNext3Months',
            'overdueContracts',
            'recentActivity',
            'monthlyBreakdown',
            'departmentStats',
            'supplierStats',
            'valueStats',
            'currentDate'
        ));
    }

    /**
     * Get contracts expiring within a specific period
     */
    private function getContractsExpiringInPeriod($startDate, $endDate)
    {
        return Contract::with('department')
            ->whereBetween('expiry_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('status', '!=', 'expired')
            ->orderBy('expiry_date', 'asc')
            ->get()
            ->groupBy(function($contract) {
                return $contract->expiry_date->format('Y-m');
            });
    }

    /**
     * Get contracts that are overdue for renewal
     */
    private function getOverdueContracts()
    {
        return Contract::with('department')
            ->whereDate('expiry_date', '<', Carbon::now()->toDateString())
            ->where('status', 'expired')
            ->orderBy('expiry_date', 'desc')
            ->get();
    }

    /**
     * Get recent contract activity (uploaded in last week/month)
     */
    private function getRecentContractActivity()
    {
        $lastWeek = Carbon::now()->subWeek();
        $lastMonth = Carbon::now()->subMonth();
        
        return [
            'total_contracts' => Contract::count(),
            'last_week' => Contract::where('created_at', '>=', $lastWeek)->count(),
            'last_month' => Contract::where('created_at', '>=', $lastMonth)->count(),
            'recent_contracts' => Contract::with('department')
                ->where('created_at', '>=', $lastWeek)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * Get monthly expiry breakdown for next 3 months
     */
    private function getMonthlyExpiryBreakdown()
    {
        $months = [];
        
        // Get current month and the next 3 months to cover full 3-month period
        $currentDate = Carbon::now();
        $endDate = $currentDate->copy()->addMonths(3);
        
        $current = $currentDate->copy()->startOfMonth();
        
        while ($current->lessThanOrEqualTo($endDate)) {
            $startOfMonth = $current->copy()->startOfMonth();
            $endOfMonth = $current->copy()->endOfMonth();
            
            $count = Contract::whereBetween('expiry_date', [$startOfMonth, $endOfMonth])
                ->where('status', '!=', 'expired')
                ->count();
            
            $months[] = [
                'name' => $current->format('F Y'),
                'count' => $count,
                'month_year' => $current->format('Y-m'),
            ];
            
            $current->addMonth();
            
            // Safety break - limit to 4 months maximum
            if (count($months) >= 4) break;
        }
        
        return $months;
    }

    /**
     * Get statistics by department
     */
    private function getDepartmentStatistics()
    {
        return Department::withCount(['contracts' => function($query) {
                $query->where('status', '!=', 'expired');
            }])
            ->having('contracts_count', '>', 0)
            ->orderBy('contracts_count', 'desc')
            ->get();
    }

    /**
     * Get supplier statistics
     */
    private function getSupplierStatistics()
    {
        return Contract::select('supplier_name', DB::raw('count(*) as contract_count'), DB::raw('sum(contract_value) as total_value'))
            ->where('status', '!=', 'expired')
            ->groupBy('supplier_name')
            ->orderBy('contract_count', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get contract value statistics
     */
    private function getContractValueStatistics()
    {
        return [
            'total_value' => Contract::where('status', '!=', 'expired')->sum('contract_value'),
            'average_value' => Contract::where('status', '!=', 'expired')->avg('contract_value'),
            'highest_value' => Contract::where('status', '!=', 'expired')->max('contract_value'),
            'lowest_value' => Contract::where('status', '!=', 'expired')->where('contract_value', '>', 0)->min('contract_value'),
            'contracts_with_value' => Contract::where('status', '!=', 'expired')->whereNotNull('contract_value')->where('contract_value', '>', 0)->count(),
        ];
    }

    /**
     * Export reports data
     */
    public function export(Request $request)
    {
        $reportType = $request->get('type', 'all');
        $format = $request->get('format', 'excel');
        
        $filename = 'contract_report_' . $reportType . '_' . now()->format('Y_m_d_His');
        
        if ($format === 'excel') {
            return Excel::download(new \App\Exports\ContractReportsExport($reportType), $filename . '.xlsx');
        }
        
        // Future: Add PDF export functionality
        return response()->json(['message' => 'PDF export functionality coming soon']);
    }

    /**
     * Get detailed report for a specific month
     */
    public function monthlyDetail($year, $month)
    {
        $startDate = Carbon::createFromFormat('Y-m', "$year-$month")->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        $contracts = Contract::with('department')
            ->whereBetween('expiry_date', [$startDate, $endDate])
            ->where('status', '!=', 'expired')
            ->orderBy('expiry_date', 'asc')
            ->get();
        
        return view('contracts.reports.monthly-detail', compact('contracts', 'startDate', 'endDate'));
    }
}
