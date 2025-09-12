<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\AccreditationRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccreditationStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly AccreditationRequest $accreditationRequest
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('accreditation.' . $this->accreditationRequest->certified_center_id),
        ];
    }
}
