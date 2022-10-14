<?php

declare(strict_types=1);

namespace App\Twig\Components;

use Brick\Money\Currency;
use Brick\Money\Money;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('home', 'home/_component.html.twig')]
final class HomeComponent
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?Money $display = null;

    public function getDisplay(): Money
    {
        $this->display = Money::ofMinor(2500, Currency::of('EUR'));

        return $this->display;
    }
}
