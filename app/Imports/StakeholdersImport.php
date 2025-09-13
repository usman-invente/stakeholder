<?php

namespace App\Imports;

us    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->successCount++;
        
        return new Stakeholder([
            'name' => $row['CONTACT NAME'] ?? null,
            'email' => $row['EMAIL'] ?? null,
            'phone' => $row['PHONE NUMBER'] ?? null,
            'organization' => $row['ORGANIZATION'] ?? 'Default Organization',
            'dcg_contact_person' => $row['DCG CONTACT'] ?? null,
            'method_of_engagement' => $row['METHOD OF ENGAGEMENT'] ?? null,
            'position' => $row['POSITION'] ?? null,
            'address' => $row['ADDRESS'] ?? null,
            'type' => strtolower($row['TYPE'] ?? 'external'),
            'notes' => $row['NOTES'] ?? null,
        ]);
    }older;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class StakeholdersImport implements 
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
        $this->successCount++;
        
        return new Stakeholder([
            'name' => $row['CONTACT NAME'],
            'email' => $row['EMAIL'],
            'phone' => $row['PHONE NUMBER'] ?? null,
            'dcg_contact_person' => $row['dcg_contact_person'] ?? null,
            'method_of_engagement' => $row['method_of_engagement'] ?? null,
            'position' => $row['POSITION '] ?? null,
            
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:stakeholders,email',
            'phone' => 'nullable|string|max:20',
            'organization' => 'nullable|string|max:255',
            'dcg_contact_person' => 'nullable|string|max:255',
            'method_of_engagement' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'type' => ['nullable', 'string', function ($attribute, $value, $fail) {
                $value = strtolower($value);
                if (!in_array($value, ['internal', 'external'])) {
                    $fail('The type must be either "internal" or "external".');
                }
            }],
            'notes' => 'nullable|string',
        ];
    }

    /**
     * @param \Throwable $e
     */
    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }

    /**
     * @param Failure[] $failures
     */
    public function onFailure(Failure ...$failures)
    {
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
     * @return int
     */
    public function batchSize(): int
    {
        return 100;
    }
    
    /**
     * @return int
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }
}
