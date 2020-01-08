@extends('layouts.app')

@section('content')
    <div class="h-screen bg-black-gray-mk">
        <div class="mx-auto h-full flex justify-center items-center">
            <div class="w-96 bg-gray-mk rounded shadow-lg py-6 px-8 text-center">
                <img class="w-16 rounded-full m-auto" src="{{asset('img/motork-profile.png')}}" alt="">
                <h1 class="text-black-gray-mktext-2xl pt-10">Welcome to MotorKBudgets</h1>
                <h2 class="text-grey-200 pt-1">Login to manage your annual budgets</h2>
                <div class="form-group row mb-0 pt-10">
                    <div class="col-md-8 offset-md-4">
                        <a href="{{url('/redirect')}}" class="btn btn-primary">
                            <button
                                class="bg-red-mk hover:bg-red-dark-mk text-white font-bold py-2 px-4 border border-red-mk rounded">
                                <img width="20" class="mr-2 float-left" alt="Google Logo"
                                     src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Google_%22G%22_Logo.svg/512px-Google_%22G%22_Logo.svg.png">
                                <span>{{ __('Login with Google') }}</span>
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
@endsection
