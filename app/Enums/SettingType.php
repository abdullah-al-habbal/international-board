<?php

declare(strict_types=1);

namespace App\Enums;

enum SettingType: string
{
    case Text = 'text';
    case Number = 'number';
    case Boolean = 'boolean';
    case Json = 'json';
    case Email = 'email';
    case Phone = 'phone';
    case Url = 'url';
    case Html = 'html';

    public function label(): string
    {
        return match ($this) {
            self::Text => 'Text',
            self::Number => 'Number',
            self::Boolean => 'Boolean',
            self::Json => 'JSON',
            self::Email => 'Email',
            self::Phone => 'Phone',
            self::Url => 'URL',
            self::Html => 'HTML',
        };
    }

    public function isText(): bool
    {
        return $this === self::Text;
    }

    public function isNumber(): bool
    {
        return $this === self::Number;
    }

    public function isBoolean(): bool
    {
        return $this === self::Boolean;
    }

    public function isJson(): bool
    {
        return $this === self::Json;
    }

    public function isEmail(): bool
    {
        return $this === self::Email;
    }

    public function isPhone(): bool
    {
        return $this === self::Phone;
    }

    public function isUrl(): bool
    {
        return $this === self::Url;
    }

    public function isHtml(): bool
    {
        return $this === self::Html;
    }
}
