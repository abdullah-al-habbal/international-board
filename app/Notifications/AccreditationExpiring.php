<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\CertifiedCenter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class AccreditationExpiring extends Notification implements ShouldQueue
{
    use Queueable;

    private const CHANNELS = ['mail', 'database'];

    private const ROUTE = '/center/accreditation-requests/create';

    public function __construct(
        private readonly CertifiedCenter $center,
        private readonly int $daysRemaining
    ) {}

    public function via(mixed $notifiable): array
    {
        return self::CHANNELS;
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.accreditation_expiring.subject'))
            ->greeting(__('notifications.greeting', ['name' => $notifiable->name]))
            ->line($this->messageLine())
            ->action(__('notifications.renew_accreditation'), url(self::ROUTE))
            ->line(__('notifications.thank_you'));
    }

    public function toArray(mixed $notifiable): array
    {
        return [
            'certified_center_id' => $this->center->id,
            'days_remaining' => $this->daysRemaining,
            'expiry_date' => $this->center->accreditation_period_end?->toISOString(),
        ];
    }

    private function messageLine(): string
    {
        return __('notifications.accreditation_expiring.message', [
            'days' => $this->daysRemaining,
            'expiry_date' => $this->center->accreditation_period_end?->format('Y-m-d'),
        ]);
    }
}
