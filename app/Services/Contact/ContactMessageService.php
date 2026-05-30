<?php

declare(strict_types=1);

namespace App\Services\Contact;

use App\Models\ContactMessage;

final class ContactMessageService
{
    public function store(array $data): ContactMessage
    {
        return ContactMessage::create($data);
    }
}
