@extends('layouts.app')

@section('content')
    <div class="container w-full mx-auto pt-20">
        <div class="w-full px-4 md:px-2 md:mt-8 mb-16 text-gray-800 leading-normal">
            <div class="container">
                <table
                    class="w-full flex flex-row flex-no-wrap sm:bg-white rounded-lg overflow-hidden sm:shadow-lg my-5">
                    @if( $expenses->count() > 0)
                        <thead class="text-white">
                        @foreach($expenses as $expense)
                            <tr class="bg-teal-400 flex flex-col flex-no wrap sm:table-row rounded-l-lg sm:rounded-none mb-2 sm:mb-0">
                                <th class="p-3 text-left">{{__('Name')}}</th>
                                <th class="p-3 text-left">{{__('Email')}}</th>
                                <th class="p-3 text-left" width="110px"></th>
                                <th class="p-3 text-left" width="110px"></th>
                            </tr>
                        @endforeach
                        </thead>
                        <tbody class="flex-1 sm:flex-none">
                        @foreach($expenses as $expense)
                            <tr class="flex flex-col flex-no wrap sm:table-row mb-2 sm:mb-0">
                                <td class="border-grey-light border hover:bg-gray-100 p-3">John Covv</td>
                                <td class="border-grey-light border hover:bg-gray-100 p-3 truncate">
                                    contato@johncovv.tech
                                </td>
                                <td class="border-grey-light border hover:bg-gray-100 text-red-400 hover:text-red-600 hover:font-medium">
                                    <a class="px-3" href="#delete">Delete</a>
                                </td>
                                <td class="border-grey-light border hover:bg-gray-100 text-red-400 hover:text-red-600 hover:font-medium">
                                    <a class="px-3" href="/expense/{{$expense->id}}">Open</a>
                                </td>
                            </tr>
                        @endforeach
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
        </div>
    </div>
    <style>
        html,
        body {
            height: 100%;
        }

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
