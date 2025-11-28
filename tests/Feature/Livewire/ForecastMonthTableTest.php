<?php

use App\Livewire\ForecastMonthTable;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(ForecastMonthTable::class)
        ->assertStatus(200);
});
