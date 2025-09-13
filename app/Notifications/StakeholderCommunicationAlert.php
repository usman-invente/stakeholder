<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class StakeholderCommunicationAlert extends Notification
{
    use Queueable;

    protected $stakeholders;
    protected $threshold;

    public function __construct(Collection $stakeholders, int $threshold)
    {
        $this->stakeholders = $stakeholders;
        $this->threshold = $threshold;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $count = $this->stakeholders->count();
        
        // Create the table HTML
        $tableRows = '';
        foreach ($this->stakeholders as $index => $stakeholder) {
            $tableRows .= '
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;">' . ($index + 1) . '</td>
                <td style="border: 1px solid #ddd; padding: 8px;">' . $stakeholder->name . '</td>
                <td style="border: 1px solid #ddd; padding: 8px;">' . $stakeholder->organization . '</td>
                <td style="border: 1px solid #ddd; padding: 8px;">' . $stakeholder->email . '</td>
                <td style="border: 1px solid #ddd; padding: 8px;">' . $stakeholder->phone . '</td>
            </tr>';
        }

        $tableHtml = '
        <table style="border-collapse: collapse; width: 100%; margin-bottom: 20px;">
            <thead>
                <tr>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2; text-align: left;">#</th>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2; text-align: left;">Name</th>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2; text-align: left;">Organization</th>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2; text-align: left;">Email</th>
                    <th style="border: 1px solid #ddd; padding: 8px; background-color: #f2f2f2; text-align: left;">Phone</th>
                </tr>
            </thead>
            <tbody>
                ' . $tableRows . '
            </tbody>
        </table>';
        
        // Use view to create the email with HTML
        return (new MailMessage)
            ->subject('Stakeholder Communication Alert')
            ->view('vendor.notifications.stakeholder-alert', [
                'tableHtml' => $tableHtml,
                'count' => $count,
                'threshold' => $this->threshold,
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "{$this->stakeholders->count()} stakeholders need attention",
            'threshold' => $this->threshold,
            'stakeholder_ids' => $this->stakeholders->pluck('id')->toArray(),
        ];
    }
}
