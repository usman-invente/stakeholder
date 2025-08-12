<?php

namespace App\Exports;

use App\Models\StakeholderCommunication;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class StakeholderCommunicationsExport implements FromCollection, WithHeadings, WithMapping, WithStrictNullComparison, ShouldAutoSize, WithEvents
{
    protected $startDate;
    protected $endDate;

    public function setDateRange($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        return $this;
    }

    public function collection()
    {
        try {
            $query = StakeholderCommunication::with(['stakeholder', 'users']);

            if ($this->startDate) {
                $query->whereDate('meeting_date', '>=', $this->startDate);
            }

            if ($this->endDate) {
                $query->whereDate('meeting_date', '<=', $this->endDate);
            }

            return $query->get();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in export collection: ' . $e->getMessage(), [
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
            'Stakeholder',
            'Meeting Date',
            'Meeting Time',
            'Meeting Type',
            'Location',
            'Attendees',
            'Discussion Points',
            'Action Items',
            'Follow Up Notes',
            'Follow Up Date',
            'Assigned Users',
            'Created At'
        ];
    }

    public function map($communication): array
    {
        try {
            // Safely handle meeting time
            $formattedTime = 'N/A';
            try {
                if (!empty($communication->meeting_time)) {
                    // Try multiple time formats
                    if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $communication->meeting_time)) {
                        $meetingTime = \Carbon\Carbon::createFromFormat('H:i:s', $communication->meeting_time);
                        $formattedTime = $meetingTime->format('h:i A');
                    } elseif (preg_match('/^\d{2}:\d{2}$/', $communication->meeting_time)) {
                        $meetingTime = \Carbon\Carbon::createFromFormat('H:i', $communication->meeting_time);
                        $formattedTime = $meetingTime->format('h:i A');
                    } else {
                        $formattedTime = $communication->meeting_time;
                    }
                }
            } catch (\Exception $timeException) {
                // If time parsing fails, just use the original value
                $formattedTime = $communication->meeting_time ?? 'N/A';
                \Illuminate\Support\Facades\Log::warning('Time parsing error: ' . $timeException->getMessage(), [
                    'communication_id' => $communication->id ?? 'unknown',
                    'meeting_time' => $communication->meeting_time ?? 'null'
                ]);
            }

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

            // Handle stakeholder name safely
            $stakeholderName = '';
            if (isset($communication->stakeholder) && isset($communication->stakeholder->name)) {
                $stakeholderName = $communication->stakeholder->name;
            }

            // Handle meeting date safely
            $meetingDate = '';
            try {
                if (!empty($communication->meeting_date)) {
                    $meetingDate = $communication->meeting_date->format('Y-m-d');
                }
            } catch (\Exception $e) {
                $meetingDate = 'Invalid Date';
            }

            // Handle follow-up date safely
            $followUpDate = '';
            try {
                if (!empty($communication->follow_up_date)) {
                    $followUpDate = $communication->follow_up_date->format('Y-m-d');
                }
            } catch (\Exception $e) {
                $followUpDate = 'Invalid Date';
            }

            // Handle created_at date safely
            $createdAt = '';
            try {
                if (!empty($communication->created_at)) {
                    $createdAt = $communication->created_at->format('Y-m-d h:i A');
                }
            } catch (\Exception $e) {
                $createdAt = 'Invalid Date';
            }

            // Handle users safely
            $usersString = '';
            try {
                if (isset($communication->users) && $communication->users->count() > 0) {
                    $usersString = $communication->users->pluck('name')->implode(', ');
                }
            } catch (\Exception $e) {
                $usersString = 'Error retrieving users';
            }

            return [
                (string) ($communication->id ?? 'N/A'),
                $cleanText($stakeholderName),
                $meetingDate,
                $formattedTime,
                $cleanText(ucfirst($communication->meeting_type ?? '')),
                $cleanText($communication->location ?? ''),
                $cleanText($communication->attendees ?? ''),
                $cleanText($communication->discussion_points ?? ''),
                $cleanText($communication->action_items ?? ''),
                $cleanText($communication->follow_up_notes ?? ''),
                $followUpDate,
                $cleanText($usersString),
                $createdAt
            ];
        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('Error in export mapping: ' . $e->getMessage(), [
                'communication_id' => $communication->id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return safe values in case of error
            return [
                (string) ($communication->id ?? 'N/A'),
                'Error in data',
                date('Y-m-d'),
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
                $columns = ['G', 'H', 'I', 'J']; // Columns for attendees, discussion points, action items, follow-up notes
                foreach ($columns as $column) {
                    $event->sheet->getStyle($column)->getAlignment()->setWrapText(true);
                }
                
                // Set header row as bold
                $event->sheet->getStyle('A1:M1')->getFont()->setBold(true);
            },
        ];
    }
}
