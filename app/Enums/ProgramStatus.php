<?php

namespace App\Enums;

enum ProgramStatus: string
{
    case Wacana = 'wacana';
    case AkanDatang = 'akan_datang';
    case Berjalan = 'berjalan';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Wacana => 'Wacana',
            self::AkanDatang => 'Akan Datang',
            self::Berjalan => 'Berjalan',
            self::Selesai => 'Selesai',
        };
    }

    public function canOpenRegistration(): bool
    {
        return $this === self::AkanDatang || $this === self::Berjalan;
    }
}
