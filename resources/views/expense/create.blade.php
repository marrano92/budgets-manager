@extends('layouts.app')

@section('content')
            <div class="w-full px-3 mb-6 md:mb-0">
                <h1 class="block uppercase tracking-wide text-gray-700 text-2xl font-bold mb-2">{{__('Create a new expense')}}</h1>
                <div class="container">
                    {!! Form::open( [ 'action' => 'ExpenseController@store', 'method' => 'POST', 'class' => 'w-full max-w-lg' ] ) !!}
                    {{Form::token()}}
                    <div class="flex flex-wrap mb-6">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            {{Form::label( 'title', 'Title', [ 'class' => 'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2' ] )}}
                        </div>
                        {{Form::text( 'title', null, [ 'class' => 'appearance-none block w-full bg-gray-200 text-gray-700 border  rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white', 'placeholder' => 'Title' ] )}}
                    </div>
                    <div class="flex flex-wrap mb-6">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            {{Form::label( 'value', 'Value', ['class' => 'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2'] )}}
                        </div>
                        {{Form::number( 'value', null, [ 'class' => 'appearance-none block w-full bg-gray-200 text-gray-700 border  rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white', 'placeholder' => '0€' ] )}}
                    </div>
                    <div class="flex flex-wrap mb-6">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            {{Form::label( 'description', 'Description', ['class' => 'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2'] )}}
                        </div>
                        {{Form::text( 'description', null, [ 'class' => 'appearance-none block w-full bg-gray-200 text-gray-700 border  rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white', 'placeholder' => 'Write description for expense' ] )}}
                    </div>
                    <div class="flex flex-wrap mb-6">
                        <div class="w-full md:w-full px-3 mb-6 md:mb-0">
                            {{Form::label( 'type', 'Type', ['class' => 'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2'] )}}
                        </div>
                        <div class="relative w-full">
                            {{Form::select('type', ['1' => 'Event', '2' => 'Course'], null, [ 'class' => 'block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500' ])}}
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 20 20">
                                    <path
                                        d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap mb-6">
                        <div class="w-full md:w-full px-3 mb-6 md:mb-0">
                            {{Form::label( 'state', 'State', ['class' => 'block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2'] )}}
                        </div>
                        <div class="relative w-full">
                            {{Form::select('state', ['1' => 'Approved', '2' => 'Not Approved'], null, [ 'class' => 'block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500' ])}}
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 20 20">
                                    <path
                                        d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    {{Form::submit('Submit', ['class' => 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline'])}}
                    {!! Form::close() !!}
                </div>
            </div>
@endsection
