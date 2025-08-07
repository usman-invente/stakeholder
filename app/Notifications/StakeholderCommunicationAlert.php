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
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $count = $this->stakeholders->count();
        
        return (new MailMessage)
            ->subject('Stakeholder Communication Alert')
            ->line("There are {$count} stakeholders without communication in the last {$this->threshold} days.")
            ->action('View Report', route('stakeholder-communications.report'))
            ->line('Please review and take necessary action.');
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
