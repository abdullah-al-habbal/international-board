<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\User;
use App\Notifications\AdminActionPerformed;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

trait NotifiesAdminOnMutation
{
    protected static function bootNotifiesAdminOnMutation(): void
    {
        static::created(fn (Model $model) => static::notifyAdminOnMutation($model, 'created'));
        static::updated(fn (Model $model) => static::notifyAdminOnMutation($model, 'updated'));
        static::deleted(fn (Model $model) => static::notifyAdminOnMutation($model, 'deleted'));
    }

    private static function notifyAdminOnMutation(Model $model, string $action): void
    {
        foreach ([
            'certified_center' => 'center',
            'trainer' => 'trainer',
        ] as $guard => $actorType) {
            if (! Auth::guard($guard)->check()) {
                continue;
            }

            $actor = Auth::guard($guard)->user();

            Notification::send(
                User::admin()->get(),
                new AdminActionPerformed($model, $action, $actorType, (string) $actor->name)
            );

            return;
        }
    }
}
