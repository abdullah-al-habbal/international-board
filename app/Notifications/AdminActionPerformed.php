<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\PanelId;
use App\Filament\Admin\Resources\CertifiedCenterFinancialRequests\CertifiedCenterFinancialRequestResource;
use App\Filament\Admin\Resources\TrainerFinancialRequests\TrainerFinancialRequestResource;
use App\Models\FinancialRequest;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

final class AdminActionPerformed extends Notification
{
    public function __construct(
        private readonly Model $model,
        private readonly string $action,
        private readonly string $actorType,
        private readonly string $actorName,
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['database'];
    }

    public function toArray(mixed $notifiable): array
    {
        return [
            'format' => 'filament',
            'title' => __('notifications.admin_action_performed.title', [
                'label' => $this->getLabel(),
            ]),
            'body' => __('notifications.admin_action_performed.body', [
                'actor_type' => __('notifications.admin_action_performed.actor.'.$this->actorType),
                'actor_name' => $this->actorName,
                'action' => __('notifications.admin_action_performed.'.$this->action),
                'label' => $this->getLabel(),
            ]),
            'model_id' => $this->model->getKey(),
            'model_class' => $this->model::class,
            'action' => $this->action,
            'actions' => [
                [
                    'name' => 'view',
                    'label' => __('notifications.admin_action_performed.view'),
                    'icon' => 'heroicon-o-eye',
                    'color' => 'primary',
                    'size' => 'sm',
                    'url' => $this->getViewUrl(),
                ],
            ],
        ];
    }

    private function getLabel(): string
    {
        $resource = $this->resolveResource();

        return $resource !== null
            ? $resource::getModelLabel()
            : Str::headline(class_basename($this->model));
    }

    private function getViewUrl(): string
    {
        $resource = $this->resolveResource();

        if ($resource === null) {
            return '/admin';
        }

        // resolveResource() only ever returns an Admin-panel resource, but this
        // notification is raised from a trainer/center guard, so the *active*
        // panel is theirs. Without naming the panel, Filament would resolve the
        // route against the acting panel — e.g. asking for
        // filament.trainer.resources.trainees.view, which does not exist — and
        // throw RouteNotFoundException while the notification is being sent.
        $panel = PanelId::Admin->value;

        if ($this->action === 'deleted') {
            return $resource::getUrl(null, [], true, $panel);
        }

        $pages = $resource::getPages();

        try {
            return match (true) {
                array_key_exists('view', $pages) => $resource::getUrl('view', ['record' => $this->model->getKey()], true, $panel),
                array_key_exists('edit', $pages) => $resource::getUrl('edit', ['record' => $this->model->getKey()], true, $panel),
                default => $resource::getUrl(null, [], true, $panel),
            };
        } catch (RouteNotFoundException) {
            // A resource whose page is not routable in the target panel must not
            // take down the action that raised the notification.
            return '/admin';
        }
    }

    private function resolveResource(): ?string
    {
        if ($this->model instanceof FinancialRequest) {
            return $this->actorType === 'trainer'
                ? TrainerFinancialRequestResource::class
                : CertifiedCenterFinancialRequestResource::class;
        }

        return self::resourcesByModel()[get_class($this->model)] ?? null;
    }

    /**
     * @return array<class-string, class-string>
     */
    private static function resourcesByModel(): array
    {
        static $map = null;

        if ($map !== null) {
            return $map;
        }

        $map = [];

        foreach (glob(app_path('Filament/Admin/Resources/*/*Resource.php')) ?: [] as $file) {
            $class = 'App\\Filament\\Admin\\Resources\\'.basename(dirname($file)).'\\'.pathinfo($file, PATHINFO_FILENAME);

            if (! is_subclass_of($class, \Filament\Resources\Resource::class)) {
                continue;
            }

            $model = $class::getModel();

            if ($model !== null) {
                $map[$model] = $class;
            }
        }

        return $map;
    }
}
