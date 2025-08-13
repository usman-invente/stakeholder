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
        $mailMessage = (new MailMessage)
            ->subject('Stakeholder Communication Alert')
            ->line("There are {$count} stakeholders without communication in the last {$this->threshold} days.")
            ->line('The following stakeholders require attention:');

        // Create a table with stakeholder information
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

        $table = '
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

        $mailMessage->line($table);
        $mailMessage->action('View Report', route('stakeholder-communications.report'))
            ->line('Please review and take necessary action.');
            
        return $mailMessage;
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
