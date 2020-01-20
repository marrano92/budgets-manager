@extends('layouts.app')

@section('content')
    <div class="flex items-center flex-wrap mx-auto my-32 pl-20 pr-10">
        <!--Main Col-->
        <div id="profile"
             class="w-full lg:w-3/5 rounded-lg lg:rounded-l-lg lg:rounded-r-none shadow-2xl bg-white opacity-75 mx-6 lg:mx-0">
            <div class="p-4 md:p-12 text-center lg:text-left">
                <!-- Image for mobile view-->
                <div class="block lg:hidden rounded-full shadow-xl mx-auto -mt-16 h-48 w-48 bg-cover bg-center"
                     style="background-image: url('https://source.unsplash.com/MP0IUfwrn0A')"></div>
                <h1 class="text-3xl font-bold pt-8 lg:pt-0">{{Auth::user()->name}}</h1>
                <div class="mx-auto lg:mx-0 w-4/5 pt-3 border-b-2 border-teal-500 opacity-25"></div>
                <p class="pt-4 text-base font-bold flex items-center justify-center lg:justify-start">
                    <svg class="h-4 fill-current text-teal-700 pr-4" xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24"><path d="M0 3v18h24v-18h-24zm6.623 7.929l-4.623 5.712v-9.458l4.623 3.746zm-4.141-5.929h19.035l-9.517 7.713-9.518-7.713zm5.694 7.188l3.824 3.099 3.83-3.104 5.612 6.817h-18.779l5.513-6.812zm9.208-1.264l4.616-3.741v9.348l-4.616-5.607z"/></svg>
                    {{Auth::user()->email}}
                </p>
                <p class="pt-2 text-gray-600 text-xs lg:text-sm flex items-center justify-center lg:justify-start">
                    <svg class="h-4 fill-current text-teal-700 pr-4" xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 20 20">
                        <path
                            d="M10 20a10 10 0 1 1 0-20 10 10 0 0 1 0 20zm7.75-8a8.01 8.01 0 0 0 0-4h-3.82a28.81 28.81 0 0 1 0 4h3.82zm-.82 2h-3.22a14.44 14.44 0 0 1-.95 3.51A8.03 8.03 0 0 0 16.93 14zm-8.85-2h3.84a24.61 24.61 0 0 0 0-4H8.08a24.61 24.61 0 0 0 0 4zm.25 2c.41 2.4 1.13 4 1.67 4s1.26-1.6 1.67-4H8.33zm-6.08-2h3.82a28.81 28.81 0 0 1 0-4H2.25a8.01 8.01 0 0 0 0 4zm.82 2a8.03 8.03 0 0 0 4.17 3.51c-.42-.96-.74-2.16-.95-3.51H3.07zm13.86-8a8.03 8.03 0 0 0-4.17-3.51c.42.96.74 2.16.95 3.51h3.22zm-8.6 0h3.34c-.41-2.4-1.13-4-1.67-4S8.74 3.6 8.33 6zM3.07 6h3.22c.2-1.35.53-2.55.95-3.51A8.03 8.03 0 0 0 3.07 6z"/>
                    </svg>
                </p>
                <p class="pt-8 text-sm">Totally optional short description about yourself, what you do and so on.</p>
                <div class="pt-12 pb-8">
                    <button class="bg-teal-700 hover:bg-teal-900 text-white font-bold py-2 px-4 rounded-full">
                        {{__('Modify User')}}
                    </button>
                </div>
                <!-- Use https://simpleicons.org/ to find the svg for your preferred product -->
            </div>
        </div>
        <!--Img Col-->
        <div class="w-full lg:w-2/5 lg:z-10">
            <!-- Big profile image for side bar (desktop) -->
            <img src="{{asset('img/default-profile.jpeg')}}"
                 class="rounded-none lg:rounded-full hidden lg:block w-2/3 -ml-32">
            <!-- Image from: http://unsplash.com/photos/MP0IUfwrn0A -->
        </div>
    </div>
@endsection
