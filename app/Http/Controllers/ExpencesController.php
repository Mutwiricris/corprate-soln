<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExpencesController extends Controller
{
    //
    public function index()
    {
        return view('Financials.Expences');
    }

    //create a new expense
    public function create()
    {
        return view('Financials.CreateExpense');
    }
    //store a new expense
    public function store(Request $request)
    {
        // Validate and store the expense data
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category' => 'required|string|max:255',

        ]);
        $data = $request->all();
              
        $data['name'] = $request->name;
        $data['amount'] = $request->amount;
        $data['date'] = $request->date;
        $data['category'] = $request->category;

        $data->save(); // This is a placeholder; replace with actual saving logic
        
        return redirect()->route('expences.index')->with('success', 'Expense created successfully.');
    }

}
