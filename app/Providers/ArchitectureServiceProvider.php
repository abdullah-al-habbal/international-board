<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

final class ArchitectureServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Model::creating(function ($model) {
            if (! str_ends_with(class_basename($model), 'Model')) {
                throw new \LogicException("Model names must end with 'Model'.");
            }
        });

        Model::retrieved(function ($model) {
            foreach (['table', 'primaryKey'] as $prop) {
                if (! property_exists($model, $prop)) {
                    throw new \LogicException(get_class($model)." must define \$$prop");
                }
            }
        });
    }
}
