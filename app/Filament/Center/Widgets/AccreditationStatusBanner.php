<?php
declare(strict_types=1);

namespace App\Filament\Center\Widgets;

use App\Models\CertifiedCenter;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

final class AccreditationStatusBanner extends Widget
{
    protected string $view = 'filament.center.widgets.accreditation-banner';
    protected static bool $isLazy = false;

    protected static ?int $sort = -10;

    public ?string $blockReason = null;
    public bool $isBlocked = false;

    public function mount(): void
    {
        /** @var CertifiedCenter|null $center */
        $center = auth('certified_center')->user();

        if ($center && !$center->canPerformActions()) {
            $this->isBlocked = true;
            $this->blockReason = $center->accreditationBlockReason();

            Notification::make()
                ->title(__('accreditation.notification.title'))
                ->body($this->blockReason)
                ->warning()
                ->persistent()
                ->send();
        }
    }
}
