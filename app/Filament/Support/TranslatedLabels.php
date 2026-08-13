<?php

declare(strict_types=1);

namespace App\Filament\Support;

use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Infolists\Components\Entry;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\BaseFilter;

final class TranslatedLabels
{
    public static function register(): void
    {
        $translateLabel = static fn (mixed $component): string => __(
            'app.'.str_replace('.', '_', (string) $component->getName()),
        );

        Field::configureUsing(fn (Field $field): Field => $field->label(fn (Field $field): string => $translateLabel($field)));
        Entry::configureUsing(fn (Entry $entry): Entry => $entry->label(fn (Entry $entry): string => $translateLabel($entry)));
        Column::configureUsing(fn (Column $column): Column => $column->label(fn (Column $column): string => $translateLabel($column)));
        BaseFilter::configureUsing(fn (BaseFilter $filter): BaseFilter => $filter->label(fn (BaseFilter $filter): string => $translateLabel($filter)));
        Action::configureUsing(fn (Action $action): Action => $action->label(fn (Action $action): string => $translateLabel($action)));
    }
}
