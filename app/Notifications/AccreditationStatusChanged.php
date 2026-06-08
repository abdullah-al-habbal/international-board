<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\CenterAccreditationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class AccreditationStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    private const CHANNELS = ['mail', 'database'];

    private const CENTER_ROUTE = '/center';

    public function __construct(
        private readonly CenterAccreditationRequest $accreditationRequest
    ) {}

    public function via(mixed $notifiable): array
    {
        return self::CHANNELS;
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->getMailSubject())
            ->greeting($this->getGreeting($notifiable))
            ->line($this->getStatusMessage())
            ->when($this->hasAdminNotes(), fn (MailMessage $message) => $this->addAdminNotesLine($message))
            ->action($this->getActionText(), $this->getActionUrl())
            ->line($this->getThankYouMessage());
    }

    public function toArray(mixed $notifiable): array
    {
        return [
            'accreditation_request_id' => $this->accreditationRequest->id,
            'status' => $this->getStatusValue(),
            'admin_notes' => $this->accreditationRequest->admin_notes,
            'reviewed_at' => $this->accreditationRequest->reviewed_at?->toISOString(),
        ];
    }

    private function getMailSubject(): string
    {
        return __('notifications.accreditation_status_changed.subject');
    }

    private function getGreeting(mixed $notifiable): string
    {
        return __('notifications.greeting', ['name' => $notifiable->name]);
    }

    private function getStatusMessage(): string
    {
        return __('notifications.accreditation_status_changed.message', [
            'status' => $this->getLocalizedStatus(),
            'request_id' => $this->accreditationRequest->id,
        ]);
    }

    private function hasAdminNotes(): bool
    {
        return ! empty($this->accreditationRequest->admin_notes);
    }

    private function addAdminNotesLine(MailMessage $message): MailMessage
    {
        return $message->line(
            __('notifications.accreditation_status_changed.admin_notes').': '.$this->accreditationRequest->admin_notes
        );
    }

    private function getActionText(): string
    {
        return __('notifications.view_request');
    }

    private function getActionUrl(): string
    {
        return url(self::CENTER_ROUTE);
    }

    private function getThankYouMessage(): string
    {
        return __('notifications.thank_you');
    }

    private function getStatusValue(): string
    {
        return $this->accreditationRequest->status->value;
    }

    private function getLocalizedStatus(): string
    {
        return __('enums.accreditation_status.'.$this->getStatusValue());
    }
}
