<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\StoreContactMessageRequest;
use App\Services\Contact\ContactMessageService;
use Illuminate\Http\RedirectResponse;

final class StoreContactMessageController extends Controller
{
    public function __construct(
        private readonly ContactMessageService $service
    ) {}

    public function __invoke(StoreContactMessageRequest $request): RedirectResponse
    {
        $this->service->store($request->validated());

        return redirect()->back()->with('success', __('web.contact.success'));
    }
}
