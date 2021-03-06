<?php

namespace App\Http\Controllers;

use App\Expense;
use App\Repository\ExpenseRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ExpenseController extends Controller
{

    /**
     * ExpenseController constructor.
     *
     * @param  ExpenseRepository  $expense
     */
    public function __construct(ExpenseRepository $expense)
    {
        $this->expense = $expense;
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index()
    {
        $expenses = $this->expense->find(\Auth::id());

        return view('expense.index')->with(['obj' => $expenses->get_obj()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('expense.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return Redirector
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'       => 'required',
            'value'       => 'required',
            'description' => 'required',
            'type'        => 'required',
            'state'       => 'required',
        ]);

        $expense              = new Expense;
        $expense->title       = $request->input('title');
        $expense->value       = $request->input('value');
        $expense->description = $request->input('description');
        $expense->type        = $request->input('type');
        $expense->state       = $request->input('state');
        $expense->user_id     = \Auth::id();
        $expense->save();

        return redirect('/expense')->with('success', 'Expense created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return Factory|View
     */
    public function show($id)
    {
        $expenses = Expense::all();
        $expense  = $expenses->find($id);

        return view('expense.show')->with('expense', $expense);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     *
     * @return void
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
