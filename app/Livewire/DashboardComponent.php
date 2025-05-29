<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
//use App\Models\Order;

class DashboardComponent extends Component
{
    use WithPagination;

    public function render()
    {
        return view('dashboard');
    }

    #[\Livewire\Attributes\Computed]
    public function paginator()
    {
        return new LengthAwarePaginator(items: range(1, 50), total: 100, perPage: 10, currentPage: 1);
    }

    #[\Livewire\Attributes\Computed]
    public function stats()
    {
        return [
            [
                'title' => 'Total revenue',
                'value' => '$38,393.12',
                'trend' => '16.2%',
                'trendUp' => true
            ],
            [
                'title' => 'Total transactions',
                'value' => '428',
                'trend' => '12.4%',
                'trendUp' => false
            ],
            [
                'title' => 'Total customers',
                'value' => '376',
                'trend' => '12.6%',
                'trendUp' => true
            ],
            [
                'title' => 'Average order value',
                'value' => '$87.12',
                'trend' => '13.7%',
                'trendUp' => true
            ]
        ];
    }

    // #[\Livewire\Attributes\Computed]
//    public function rows()
//    {
//        return Order::all();
//    }
}
