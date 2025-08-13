<?php

namespace App\Http\Controllers;

use App\Models\Stakeholder;
use App\Exports\StakeholdersExport;
use App\Imports\StakeholdersImportNew;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class StakeholderController extends Controller
{
    public function index(Request $request)
    {
        // Build query with search capabilities
        $query = Stakeholder::query();
        
        // Apply search filters if provided
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('organization', 'like', "%{$search}%")
                  ->orWhere('dcg_contact_person', 'like', "%{$search}%")
                  ->orWhere('method_of_engagement', 'like', "%{$search}%");
            });
        }
        
        // Apply type filter if provided
        if ($request->filled('type') && in_array($request->type, ['internal', 'external'])) {
            $query->where('type', $request->type);
        }
        
        // Get paginated results
        $stakeholders = $query->paginate(10)->withQueryString();
        
        return view('stakeholders.index', compact('stakeholders'));
    }

    public function create()
    {
        $users = \App\Models\User::where('role', 'user')->get();
        return view('stakeholders.create', compact('users'));
    }

    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'The stakeholder name is required.',
            'name.max' => 'The stakeholder name cannot exceed 255 characters.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.max' => 'The phone number cannot exceed 20 characters.',
            'organization.required' => 'The organization name is required.',
            'organization.max' => 'The organization name cannot exceed 255 characters.',
            'dcg_contact_person.max' => 'The DCG contact person cannot exceed 255 characters.',
            'method_of_engagement.max' => 'The method of engagement cannot exceed 255 characters.',
            'position.max' => 'The position cannot exceed 255 characters.',
            'type.required' => 'Please select a stakeholder type.',
            'type.in' => 'Please select either Internal or External type.'
        ];

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:stakeholders',
            'phone' => 'nullable|string|max:20',
            'organization' => 'required|string|max:255',
            'dcg_contact_person' => 'nullable|string|max:255',
            'method_of_engagement' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'type' => 'required|in:internal,external',
            'notes' => 'nullable|string',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id'
        ], $messages);

        $stakeholder = Stakeholder::create($validated);
        
        // Attach users if selected
        if ($request->has('users')) {
            $stakeholder->users()->attach($request->users);
        }

        return redirect()->route('stakeholders.index')
            ->with('success', 'Stakeholder created successfully.');
    }

    public function show(Stakeholder $stakeholder)
    {
        return view('stakeholders.show', compact('stakeholder'));
    }

    public function edit(Stakeholder $stakeholder)
    {
        $users = \App\Models\User::where('role', 'user')->get();
        $assignedUsers = $stakeholder->users->pluck('id')->toArray();
        return view('stakeholders.edit', compact('stakeholder', 'users', 'assignedUsers'));
    }

    public function update(Request $request, Stakeholder $stakeholder)
    {
        $messages = [
            'name.required' => 'The stakeholder name is required.',
            'name.max' => 'The stakeholder name cannot exceed 255 characters.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'phone.max' => 'The phone number cannot exceed 20 characters.',
            'organization.required' => 'The organization name is required.',
            'organization.max' => 'The organization name cannot exceed 255 characters.',
            'dcg_contact_person.max' => 'The DCG contact person cannot exceed 255 characters.',
            'method_of_engagement.max' => 'The method of engagement cannot exceed 255 characters.',
            'position.max' => 'The position cannot exceed 255 characters.',
            'type.required' => 'Please select a stakeholder type.',
            'type.in' => 'Please select either Internal or External type.'
        ];

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:stakeholders,email,' . $stakeholder->id,
            'phone' => 'nullable|string|max:20',
            'organization' => 'required|string|max:255',
            'dcg_contact_person' => 'nullable|string|max:255',
            'method_of_engagement' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'type' => 'required|in:internal,external',
            'notes' => 'nullable|string',
            'users' => 'nullable|array',
            'users.*' => 'exists:users,id'
        ], $messages);

        $stakeholder->update($validated);
        
        // Sync users
        if ($request->has('users')) {
            $stakeholder->users()->sync($request->users);
        } else {
            $stakeholder->users()->detach();
        }

        return redirect()->route('stakeholders.index')
            ->with('success', 'Stakeholder updated successfully.');
    }

    public function destroy(Stakeholder $stakeholder)
    {
        $stakeholder->delete();

        return redirect()->route('stakeholders.index')
            ->with('success', 'Stakeholder deleted successfully.');
    }
    
    /**
     * Export stakeholders to Excel
     */
    public function export(Request $request)
    {
        try {
            // Check if this is a template request
            $isTemplate = $request->has('limit') && $request->limit === '0';
            
            $export = new StakeholdersExport(
                $isTemplate ? null : $request->search,
                $isTemplate ? null : $request->type,
                $isTemplate
            );
            
            $filename = $isTemplate ? 'stakeholders-template' : 'stakeholders-' . now()->format('Y-m-d');
            
            return Excel::download($export, $filename . '.xlsx');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Stakeholders Export failed: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for importing stakeholders
     */
    public function importForm()
    {
        return view('stakeholders.import');
    }

    /**
     * Import stakeholders from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'Please select a file to upload.',
            'file.file' => 'The upload must be a valid file.',
            'file.mimes' => 'The file must be an Excel file (xlsx, xls) or CSV file.',
            'file.max' => 'The file size cannot exceed 2MB.',
        ]);

        try {
            $import = new StakeholdersImportNew();
            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            
            return redirect()->route('stakeholders.index')
                ->with('success', "{$successCount} stakeholders imported successfully.");
                
        } catch (ValidationException $e) {
            $failures = $e->failures();
            
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = "Row {$failure->row()}: {$failure->errors()[0]}";
            }
            
            Log::error('Stakeholder import validation failed', [
                'errors' => $errors
            ]);
            
            return redirect()->back()
                ->with('import_errors', $errors)
                ->with('error', 'The import failed due to validation errors. Please check the data and try again.');
                
        } catch (\Exception $e) {
            Log::error('Stakeholder import failed: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
