<?php

namespace App\Http\Controllers;

use App\Expense;
use Illuminate\Contracts\Support\Renderable;

class HomeController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware( 'auth' );
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index() {
        $query    = Expense::query();
        $expenses = $query->where( 'user_id', '=', \Auth::id() )->get();
        $total    = 0;

        foreach ( $expenses as $expense ) {
            $total = $total + $expense->value;
        }

        return view( 'home' )->with( 'obj', (object) [ 'total' => $total ]);
    }
}
