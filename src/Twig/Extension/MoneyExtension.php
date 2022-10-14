<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use Brick\Money\Money;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Evert Harmeling <evert@yoursportpro.nl>
 */
final class MoneyExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('format_money', $this->formatMoney(...)),
        ];
    }

    public function formatMoney(Money $money): string
    {
        return $money->formatTo('nl');
    }
}
