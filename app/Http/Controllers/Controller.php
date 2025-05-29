<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;

 class Controller
{
    //

    public function index()
    {
        $chart = new Chart;
        $chart->labels(['Jan', 'Feb', 'Mar']);
        $chart->dataset('Monthly Sales', 'line', [150, 200, 300])
            ->color('blue')
            ->backgroundcolor('lightblue');

        return view('Financials.Expences', compact('chart'));
    }

}
