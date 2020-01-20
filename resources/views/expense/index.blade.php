@extends('layouts.app')

<?php
$expenses = $obj->expenses;
$tot = $obj->total;
?>

@section('content')
    <div class="container">
        <div class="w-1/2">
            {{--            <div class="inline-block relative w-64">--}}
            {{--                <select--}}
            {{--                    class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">--}}
            {{--                    <option>Really long option that will likely overlap the chevron</option>--}}
            {{--                    <option>Option 2</option>--}}
            {{--                    <option>Option 3</option>--}}
            {{--                </select>--}}
            {{--                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">--}}
            {{--                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">--}}
            {{--                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>--}}
            {{--                    </svg>--}}
            {{--                </div>--}}
            {{--            </div>--}}
        </div>
        <table
            class="w-full flex flex-row flex-no-wrap sm:bg-white rounded-lg overflow-hidden sm:shadow-lg my-5">
            @if( $expenses->count() > 0)
                <thead class="text-white">
                @foreach($expenses as $expense)
                    <tr class="bg-red-mk flex flex-col flex-no wrap sm:table-row rounded-l-lg sm:rounded-none mb-2 sm:mb-0">
                        <th class="p-3 text-left">{{__('Title')}}</th>
                        <th class="p-3 text-left">{{__('Description')}}</th>
                        <th class="p-3 text-left">{{__('value')}}</th>
                        <th class="p-3 text-left">{{__('State')}}</th>
                        <th class="p-3 text-left" width="110px"></th>
                        <th class="p-3 text-left" width="110px"></th>
                    </tr>
                @endforeach
                <tr class="bg-red-mk flex flex-col flex-no wrap sm:table-row rounded-l-lg sm:rounded-none mb-2 sm:mb-0">
                    <th class="p-3 text-left"></th>
                    <th class="p-3 text-left"></th>
                    <th class="p-3 text-left">{{__('value')}}</th>
                </tr>
                </thead>
                <tbody class="flex-1 sm:flex-none">
                @foreach($expenses as $expense)
                    <tr class="flex flex-col flex-no wrap sm:table-row mb-2 sm:mb-0">
                        <td class="border-grey-light border hover:bg-gray-100 p-3"> {{$expense->title}} </td>
                        <td class="border-grey-light border hover:bg-gray-100 p-3 truncate"> {{$expense->description}} </td>
                        <td class="border-grey-light border hover:bg-gray-100 text-right p-3 truncate">
                            {{$expense->value}} €
                        </td>
                        <td class="border-grey-light border hover:bg-gray-100 text-center p-3 truncate">
                            <i class="fas fa-thumbs-up @if($expense->state =1) text-green-600 @else text-red-600 @endif"></i>
                            <span class="text-green-600 font-bold">
                                @if($expense->state == 1) Approved @else Not approved @endif
                            </span>
                        </td>
                        <td class="border-grey-light border hover:bg-gray-100 text-red-400 hover:text-red-600 hover:font-medium">
                            <a class="px-3" href="#delete">Delete</a>
                        </td>
                        <td class="border-grey-light border hover:bg-gray-100 text-red-400 hover:text-red-600 hover:font-medium">
                            <a class="px-3" href="/expense/{{$expense->id}}">Open</a>
                        </td>
                    </tr>
                @endforeach
                <tr class="flex flex-col flex-no wrap sm:table-row mb-2 sm:mb-0">
                    <td class="border-grey-light border hover:bg-gray-100 p-3"></td>
                    <td class="border-grey-light border hover:bg-gray-100 p-3 truncate"></td>
                    <td class="border-grey-light border hover:bg-gray-100 text-right p-3 truncate">Tot. {{$tot}} €</td>
                    <td class="border-grey-light border hover:bg-gray-100 p-3 truncate hidden sm:hidden md:table-cell"></td>
                    <td class="border-grey-light border hover:bg-gray-100 text-red-400 hidden sm:hidden md:table-cell hover:text-red-600 hover:font-medium">
                    </td>
                    <td class="border-grey-light border hover:bg-gray-100 text-red-400 hidden sm:hidden md:table-cell hover:text-red-600 hover:font-medium">
                    </td>
                </tr>
            @else
                <thead class="text-white">
                <tr class="bg-red-mk flex flex-col flex-no wrap sm:table-row rounded-l-lg sm:rounded-none mb-2 sm:mb-0">
                    <th class="p-3 text-left">Values</th>
                </tr>
                </thead>
                <tbody class="flex-1 sm:flex-none">
                <tr class="flex flex-col flex-no wrap sm:table-row mb-2 sm:mb-0">
                    <td class="border-grey-light border hover:bg-gray-100 p-3">No results found</td>
                </tr>
                @endif
                </tbody>
        </table>
    </div>
    <style>
        @media (min-width: 640px) {
            table {
                display: inline-table !important;
            }

            thead tr:not(:first-child) {
                display: none;
            }
        }

        td:not(:last-child) {
            border-bottom: 0;
        }

        th:not(:last-child) {
            border-bottom: 2px solid rgba(0, 0, 0, .1);
        }
    </style>
@endsection
