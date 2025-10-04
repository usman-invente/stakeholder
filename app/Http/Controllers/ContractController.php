<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ContractController extends Controller
{
    /**
     * Display the contract dashboard.
     */
    public function dashboard()
    {
        $contracts = Contract::with('department')->latest()->take(10)->get();

        // Update contract statuses
        foreach ($contracts as $contract) {
            $contract->updateStatus();
        }

        // Storage calculation (in MB)
        $storageUsed = $this->calculateStorageUsed();
        $storageLimit = 1024; // 1GB limit, can be configured
        $storageRemaining = $storageLimit - $storageUsed;

        $stats = [
            'total_contracts' => Contract::count(),
            'active_contracts' => Contract::where('status', 'active')->count(),
            'expiring_contracts' => Contract::where('status', 'expiring')->count(),
            'expired_contracts' => Contract::where('status', 'expired')->count(),
            'storage_used' => $storageUsed,
            'storage_limit' => $storageLimit,
            'storage_remaining' => $storageRemaining,
        ];

        return view('contracts.dashboard', compact('contracts', 'stats'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contracts = Contract::with('department')->latest()->paginate(15);
        return view('contracts.index', compact('contracts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        return view('contracts.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contract_title' => 'required|string|max:255',
            'contract_id' => 'nullable|string|max:255|unique:contracts',
            'start_date' => 'required|date',
            'expiry_date' => 'required|date|after:start_date',
            'renewal_terms' => 'required|in:auto-renew,manual',
            'contract_value' => 'nullable|numeric|min:0',
            'department_id' => 'required|exists:departments,id',
            'contract_owner' => 'required|string|max:255',
            'contract_owner_email' => 'required|email|max:255',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:10240', // 10MB max
        ]);

        // Generate contract ID if not provided
        if (empty($validated['contract_id'])) {
            $validated['contract_id'] = $this->generateContractId();
        }

        if ($request->hasFile('document')) {
            $file = $request->file('document');

            // Generate unique filename to avoid conflicts
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $nameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);
            $uniqueFilename = $nameWithoutExtension . '_' . time() . '.' . $extension;

            try {
                // Ensure contracts directory exists in public folder
                $contractsDir = public_path('contracts');
                if (!file_exists($contractsDir)) {
                    mkdir($contractsDir, 0755, true);
                }

                // Move file directly to public/contracts folder
                $destinationPath = $contractsDir . DIRECTORY_SEPARATOR . $uniqueFilename;
                $file->move($contractsDir, $uniqueFilename);

                // Store relative path for database
                $validated['document_path'] = 'contracts/' . $uniqueFilename;
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to upload document: ' . $e->getMessage());
            }
        }

        $contract = Contract::create($validated);
        $contract->updateStatus();

        return redirect()->route('contracts.index')->with('success', 'Contract created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        $contract->load('department');
        return view('contracts.show', compact('contract'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contract $contract)
    {
        $departments = Department::where('is_active', true)->get();
        return view('contracts.edit', compact('contract', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'contract_title' => 'required|string|max:255',
            'contract_id' => 'required|string|max:255|unique:contracts,contract_id,' . $contract->id,
            'start_date' => 'required|date',
            'expiry_date' => 'required|date|after:start_date',
            'renewal_terms' => 'required|in:auto-renew,manual',
            'contract_value' => 'nullable|numeric|min:0',
            'department_id' => 'required|exists:departments,id',
            'contract_owner' => 'required|string|max:255',
            'contract_owner_email' => 'required|email|max:255',
            'auto_renewal' => 'nullable|boolean',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        // Track changes for notification
        $originalData = $contract->toArray();
        $changes = [];

        foreach ($validated as $field => $newValue) {
            if ($field === 'document') continue; // Skip file upload field

            $oldValue = $contract->{$field};

            // Handle date comparison properly
            $valuesAreDifferent = false;
            if (in_array($field, ['start_date', 'expiry_date'])) {
                // For dates, compare the actual date values, not the objects
                $oldDateString = $oldValue ? $oldValue->format('Y-m-d') : null;
                $newDateString = $newValue ? date('Y-m-d', strtotime($newValue)) : null;
                $valuesAreDifferent = $oldDateString !== $newDateString;
            } else {
                $valuesAreDifferent = $oldValue != $newValue;
            }

            if ($valuesAreDifferent) {
                // Format values for display
                $displayOldValue = $this->formatValueForDisplay($field, $oldValue);
                $displayNewValue = $this->formatValueForDisplay($field, $newValue);

                $changes[$field] = [
                    'old' => $displayOldValue,
                    'new' => $displayNewValue
                ];
            }
        }

        if ($request->hasFile('document')) {
            // Delete old document if exists
            if ($contract->document_path && file_exists(public_path($contract->document_path))) {
                unlink(public_path($contract->document_path));
            }

            $file = $request->file('document');
            // Generate unique filename to avoid conflicts
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $nameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);
            $uniqueFilename = $nameWithoutExtension . '_' . time() . '.' . $extension;

            try {
                // Ensure contracts directory exists in public folder
                $contractsDir = public_path('contracts');
                if (!file_exists($contractsDir)) {
                    mkdir($contractsDir, 0755, true);
                }

                // Move file directly to public/contracts folder
                $file->move($contractsDir, $uniqueFilename);

                // Store relative path for database
                $path = 'public/contracts/' . $uniqueFilename;
                $validated['document_path'] = $path;

                // Track document change
                $changes['document'] = [
                    'old' => $contract->document_path ? asset($contract->document_path) : 'No document',
                    'new' => asset($path)
                ];
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to upload document: ' . $e->getMessage());
            }
        }

        $contract->update($validated);
        $contract->updateStatus();

        // Send alteration notification if there were changes
        if (!empty($changes)) {
            $updatedBy = Auth::user() ? Auth::user()->name : 'System Administrator';
            $contract->sendAlterationNotification($changes, $updatedBy);
        }

        return redirect()->route('contracts.index')->with('success', 'Contract updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contract $contract)
    {
        if ($contract->document_path && file_exists(public_path($contract->document_path))) {
            unlink(public_path($contract->document_path));
        }

        $contract->delete();

        return redirect()->route('contracts.index')->with('success', 'Contract deleted successfully.');
    }

    /**
     * Generate a new contract ID via AJAX
     */
    public function generateId()
    {
        return response()->json([
            'contract_id' => $this->generateContractId()
        ]);
    }

    /**
     * Download contract document
     */
    public function downloadDocument(Contract $contract)
    {
        $filePath = $contract->document_path;


        if (!$contract->document_path || !file_exists($filePath)) {
            abort(404, 'Document not found.');
        }

        $filename = $contract->contract_id . '_' . basename($contract->document_path);

        return response()->download($filePath, $filename);
    }

    /**
     * Generate a unique contract ID
     */
    private function generateContractId()
    {
        $year = date('Y');
        $prefix = 'CTR-' . $year . '-';

        // Find the highest number for this year
        $lastContract = Contract::where('contract_id', 'like', $prefix . '%')
            ->orderBy('contract_id', 'desc')
            ->first();

        if ($lastContract) {
            // Extract number from last contract ID (e.g., CTR-2025-001 -> 001)
            $lastNumber = (int) substr($lastContract->contract_id, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format with leading zeros (001, 002, etc.)
        $formattedNumber = str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        return $prefix . $formattedNumber;
    }

    /**
     * Calculate total storage used by contract documents
     */
    private function calculateStorageUsed()
    {
        $totalSize = 0;
        $contracts = Contract::whereNotNull('document_path')->get();

        foreach ($contracts as $contract) {
            $filePath = public_path($contract->document_path);
            if (file_exists($filePath)) {
                $totalSize += filesize($filePath);
            }
        }

        return round($totalSize / 1024 / 1024, 2); // Convert to MB
    }

    /**
     * Format values for display in change notifications
     */
    private function formatValueForDisplay($field, $value)
    {
        if ($value === null) {
            return 'Not set';
        }

        // Format dates properly
        if (in_array($field, ['start_date', 'expiry_date'])) {
            if ($value instanceof \Carbon\Carbon) {
                return $value->format('d/m/Y');
            }
            try {
                return \Carbon\Carbon::parse($value)->format('d/m/Y');
            } catch (\Exception $e) {
                return $value;
            }
        }

        // Format contract value
        if ($field === 'contract_value' && is_numeric($value)) {
            return '£' . number_format($value, 2);
        }

        // Format boolean values
        if (in_array($field, ['auto_renewal'])) {
            return $value ? 'Yes' : 'No';
        }

        // Format department_id to department name
        if ($field === 'department_id') {
            $department = \App\Models\Department::find($value);
            return $department ? $department->name : "Department ID: {$value}";
        }

        // Format enum values
        if ($field === 'renewal_terms') {
            return ucwords(str_replace('-', ' ', $value));
        }

        if ($field === 'status') {
            return ucfirst($value);
        }

        return $value;
    }
}
