<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash         = 'cash';
    case Bkash        = 'bkash';
    case Nagad        = 'nagad';
    case Rocket       = 'rocket';
    case BankTransfer = 'bank_transfer';
    case Cheque       = 'cheque';
    case Credit       = 'credit';

    public function label(): string
    {
        return match ($this) {
            self::Cash         => 'Cash',
            self::Bkash        => 'bKash',
            self::Nagad        => 'Nagad',
            self::Rocket       => 'Rocket',
            self::BankTransfer => 'Bank Transfer',
            self::Cheque       => 'Cheque',
            self::Credit       => 'Credit Balance',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Cash         => '💵',
            self::Bkash        => '📱',
            self::Nagad        => '📱',
            self::Rocket       => '📱',
            self::BankTransfer => '🏦',
            self::Cheque       => '📄',
            self::Credit       => '💳',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Cash         => 'bg-green-100 text-green-700',
            self::Bkash        => 'bg-pink-100 text-pink-700',
            self::Nagad        => 'bg-orange-100 text-orange-700',
            self::Rocket       => 'bg-purple-100 text-purple-700',
            self::BankTransfer => 'bg-blue-100 text-blue-700',
            self::Cheque       => 'bg-slate-100 text-slate-700',
            self::Credit       => 'bg-indigo-100 text-indigo-700',
        };
    }
}
