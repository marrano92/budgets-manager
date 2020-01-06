@extends('layouts.app')

@section('content')
    <div class="container w-full mx-auto pt-20">
        <div class="w-full px-4 md:px-2 md:mt-8 mb-16 text-gray-800 leading-normal">
            <div class="container">
                {{$expense}}
            </div>
        </div>
    </div>
@endsection
