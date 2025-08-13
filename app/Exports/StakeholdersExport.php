<?php

namespace App\Exports;

use App\Models\Stakeholder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class StakeholdersExport implements FromCollection, WithHeadings, WithMapping, WithStrictNullComparison, ShouldAutoSize, WithEvents
{
    protected $search;
    protected $type;
    protected $isTemplate;

    public function __construct($search = null, $type = null, $isTemplate = false)
    {
        $this->search = $search;
        $this->type = $type;
        $this->isTemplate = $isTemplate;
    }

    public function collection()
    {
        // If this is a template request, return an empty collection
        if ($this->isTemplate) {
            return collect([]);
        }
        
        try {
            $query = Stakeholder::query();
            
            // Apply search filters if provided
            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                      ->orWhere('organization', 'like', "%{$this->search}%")
                      ->orWhere('dcg_contact_person', 'like', "%{$this->search}%")
                      ->orWhere('method_of_engagement', 'like', "%{$this->search}%");
                });
            }
            
            // Apply type filter if provided
            if ($this->type && in_array($this->type, ['internal', 'external'])) {
                $query->where('type', $this->type);
            }
            
            return $query->get();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in stakeholders export collection: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return an empty collection in case of error
            return collect([]);
        }
    }

    public function headings(): array
    {
        return [
            'ID',
            'CONTACT NAME',
            'EMAIL',
            'PHONE NUMBER',
            'ORGANIZATION',
            'DCG CONTACT',
            'METHOD OF ENGAGEMENT',
            'POSITION',
            'ADDRESS',
            'TYPE',
            'NOTES',
            'Created At'
        ];
    }

    public function map($stakeholder): array
    {
        // If this is a template, return an example row
        if ($this->isTemplate) {
            return [
                '',
                'John Doe',
                'john.doe@example.com',
                '1234567890',
                'Example Organization',
                'Jane Smith',
                'Email, Phone',
                'Manager',
                '123 Example Street',
                'Internal',
                'Additional notes here',
                ''
            ];
        }
        
        try {
            // Helper function to clean text fields
            $cleanText = function($text) {
                if (empty($text)) return '';
                
                // Convert to string if not already
                $text = (string) $text;
                
                // Remove HTML tags
                $text = strip_tags($text);
                
                // Replace all line breaks and control characters with a space
                $text = preg_replace('/[\r\n\t\f\v\0-\x1F\x7F]/', ' ', $text);
                
                // Remove any non-printable or non-ASCII characters
                $text = preg_replace('/[^\x20-\x7E]/', '', $text);
                
                // Limit string length to avoid Excel issues (32,767 is Excel's limit)
                if (strlen($text) > 32000) {
                    $text = substr($text, 0, 32000);
                }
                
                // Trim excessive whitespace and convert multiple spaces to single
                $text = trim($text);
                $text = preg_replace('/\s+/', ' ', $text);
                
                return $text;
            };

            return [
                (string) $stakeholder->id,
                $cleanText($stakeholder->name),
                $cleanText($stakeholder->email),
                $cleanText($stakeholder->phone ?? ''),
                $cleanText($stakeholder->organization),
                $cleanText($stakeholder->dcg_contact_person ?? ''),
                $cleanText($stakeholder->method_of_engagement ?? ''),
                $cleanText($stakeholder->position ?? ''),
                $cleanText($stakeholder->address ?? ''),
                ucfirst($stakeholder->type),
                $cleanText($stakeholder->notes ?? ''),
                $stakeholder->created_at ? $stakeholder->created_at->format('Y-m-d h:i A') : ''
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in stakeholders export mapping: ' . $e->getMessage(), [
                'stakeholder_id' => $stakeholder->id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return safe values in case of error
            return [
                (string) ($stakeholder->id ?? 'N/A'),
                'Error in data',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                'N/A',
                date('Y-m-d h:i A')
            ];
        }
    }
    
    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Auto-adjust column widths
                foreach ($event->sheet->getColumnIterator() as $column) {
                    $column = $column->getColumnIndex();
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
                
                // Apply text wrapping to columns with potentially long content
                $columns = ['I', 'K']; // Columns for address, notes
                foreach ($columns as $column) {
                    $event->sheet->getStyle($column)->getAlignment()->setWrapText(true);
                }
                
                // Set header row as bold
                $event->sheet->getStyle('A1:L1')->getFont()->setBold(true);
                
                // If this is a template, add some formatting to help the user
                if ($this->isTemplate) {
                    // Add a comment to the type cell explaining allowed values
                    $event->sheet->getComment('J2')->getText()->createTextRun('Enter either "Internal" or "External"');
                    
                    // Add some color to the required fields in the header
                    $requiredColumns = ['B1', 'C1', 'E1', 'J1']; // Name, Email, Organization, Type
                    $event->sheet->getStyle($requiredColumns)->getFont()->getColor()->setRGB('FF0000'); // Red color
                    
                    // Add sample row formatting
                    $event->sheet->getStyle('A2:L2')->getFont()->setItalic(true);
                    $event->sheet->getStyle('A2:L2')->getFont()->setColor()->setRGB('808080'); // Gray color
                }
            },
        ];
    }
}
