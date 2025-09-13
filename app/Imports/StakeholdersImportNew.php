<?php

namespace App\Imports;

use App\Models\Stakeholder;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Uid\NilUlid;

class StakeholdersImportNew implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation, 
    SkipsOnError, 
    SkipsOnFailure,
    WithBatchInserts
{
    /**
     * @var array
     */
    protected $errors = [];
    
    /**
     * @var array
     */
    protected $failures = [];
    
    /**
     * @var int
     */
    protected $successCount = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Debug to check what's in the row
        Log::info('Excel Row:', $row);
        
        // Check if we have actual data in this row
        if (empty($row) || (!isset($row['contact_name']))) {
            return null;
        }
        
        $this->successCount++;
        
        // Try different possible column names (case-insensitive)
        $name =  $row['contact_name'] ?? 'N/A';
        $email = $row['email'] ?? null;
        $phone = $row['phone_number']  ?? 'N/A';
        $organization = $row['organization']  ?? 'N/A';
        $dcgContact = $row['dcg_contact_person']  ?? 'N/A';
        $method = $row['method_of_engagement']  ?? 'N/A';
        $position = $row['position']  ?? 'N/A';
        $type = $row['type']  ?? 'external';
        
        Log::info('Mapped data:', [
            'name' => $name,
            'email' => $email,
            'organization' => $organization,
            'type' => $type
        ]);
        
        return new Stakeholder([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'dcg_contact_person' => $dcgContact,
            'method_of_engagement' => $method,
            'position' => $position,
            'organization' => $organization,
            'type' => $type,
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
           
            'contact_name' => 'required|string|max:255',
            'email' => 'sometimes',
            'phone' => 'nullable|string|max:20',
            'organization' => 'nullable|string|max:255',
            'dcg_contact_person' => 'nullable|string|max:255',
            'method_of_engagement' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255', 
            'type' => 'nullable|string',
        
        ];
    }
    
    /**
     * Custom validation to check required fields across different column names
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            foreach ($validator->getData() as $rowIndex => $row) {

                Log::info($row);
                // Check if name is provided in any variant
                $hasName = isset($row['name']) || isset($row['NAME']) || 
                           isset($row['contact_name']) || isset($row['CONTACT NAME']);
                    
                
                // Add validation errors if required fields are missing
                if (!$hasName) {
                    $validator->errors()->add($rowIndex, 'The name field is required.');
                }
                
               

              
            }
        });
    }

    /**
     * @param \Throwable $e
     */
    public function onError(Throwable $e)
    {
        Log::error('Import error: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        $this->errors[] = $e->getMessage();
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            Log::warning('Import validation failure: ', [
                'row' => $failure->row(),
                'errors' => $failure->errors()
            ]);
        }
        $this->failures = array_merge($this->failures, $failures);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getFailures()
    {
        return $this->failures;
    }

    /**
     * Configure the collection chunk size.
     *
     * @return int
     */
    public function chunkSize(): int
    {
        return 50;
    }
    
    /**
     * Check if it's a heading row.
     * We also use this to log the headings.
     */
    public function headingRow(): int
    {
        // The WithHeadingRow trait uses 1-indexed rows, where 1 is the heading row
        return 1;
    }
    
    /**
     * Process one row at a time for better debugging
     */
    public function batchSize(): int
    {
        return 1;
    }
    
    /**
     * @return int
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }
}
