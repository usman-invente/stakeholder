<?php

namespace App\Exports;

use App\Models\StakeholderCommunication;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StakeholderCommunicationsExport implements FromCollection, WithHeadings, WithMapping
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
        $query = StakeholderCommunication::with(['stakeholder', 'users']);

        if ($this->startDate) {
            $query->whereDate('meeting_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('meeting_date', '<=', $this->endDate);
        }

        return $query->get();
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
        // Convert meeting time to Carbon instance for better handling
        $meetingTime = \Carbon\Carbon::createFromFormat('H:i:s', $communication->meeting_time);
        $formattedTime = $meetingTime->format('h:i A'); // 12-hour format with AM/PM

        return [
            $communication->id,
            $communication->stakeholder->name,
            $communication->meeting_date->format('Y-m-d'),
            $formattedTime,
            ucfirst($communication->meeting_type),
            $communication->location,
            $communication->attendees,
            $communication->discussion_points,
            $communication->action_items,
            $communication->follow_up_notes,
            $communication->follow_up_date ? $communication->follow_up_date->format('Y-m-d') : '',
            $communication->users->pluck('name')->implode(', '),
            $communication->created_at->format('Y-m-d h:i A')
        ];
    }
}
