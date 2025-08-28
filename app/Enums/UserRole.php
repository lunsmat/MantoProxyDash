<?php

namespace App\Enums;

enum UserRole
{
    case Admin;
    case User;

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => 'admin',
            self::User => 'user',
        };
    }

    public function getLabelName(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::User => 'Usuário',
        };
    }

    public static function fromLabel(string $label): ?self
    {
        return match ($label) {
            'admin' => self::Admin,
            'user' => self::User,
            default => null,
        };
    }

    public static function getLabels(): array
    {
        return [
            self::Admin->getLabel(),
            self::User->getLabel(),
        ];
    }
}
