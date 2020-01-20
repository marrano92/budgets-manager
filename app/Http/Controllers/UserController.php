<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class UserController extends Controller {


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware( 'auth' );
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index() {
        return view( 'user.index' )->with( 'obj', (object) [ 'user' => \Auth::user() ] );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function store( Request $request ) {
        //
    }

    /**
     * Display the specified resource.
     *
     *
     * @return Factory|View
     */
    public function show() {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit( $id ) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return Response
     */
    public function update( Request $request, $id ) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy( $id ) {
        //
    }
}
