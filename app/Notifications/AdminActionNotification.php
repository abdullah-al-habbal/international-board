<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;

final class AdminActionNotification extends Notification
{
    public function __construct(
        private readonly Model $model,
        private readonly string $action,
        private readonly string $panelId,
        private readonly string $resourceClass,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $resource = $this->resourceClass;

        return [
            'format' => 'filament',
            'title' => __('notifications.admin_action_notification.title', [
                'label' => $resource::getModelLabel(),
            ]),
            'body' => __('notifications.admin_action_notification.body', [
                'action' => __('notifications.admin_action_notification.actions.'.$this->action),
                'label' => $resource::getModelLabel(),
            ]),
            'model_id' => $this->model->getKey(),
            'model_class' => $this->model::class,
            'action' => $this->action,
            'actions' => [
                [
                    'name' => 'view',
                    'label' => __('notifications.admin_action_notification.view'),
                    'icon' => 'heroicon-o-eye',
                    'color' => 'primary',
                    'size' => 'sm',
                    'url' => $this->getViewUrl($resource),
                ],
            ],
        ];
    }

    private function getViewUrl(string $resource): string
    {
        if ($this->action === 'deleted') {
            return $resource::getUrl(null, [], true, $this->panelId);
        }

        $pages = $resource::getPages();

        return match (true) {
            array_key_exists('view', $pages) => $resource::getUrl('view', ['record' => $this->model->getKey()], true, $this->panelId),
            array_key_exists('edit', $pages) => $resource::getUrl('edit', ['record' => $this->model->getKey()], true, $this->panelId),
            default => $resource::getUrl(null, [], true, $this->panelId),
        };
    }
}
