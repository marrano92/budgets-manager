<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
    <link href="https://unpkg.com/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
    <!--Replace with your tailwind.css once created-->
    <link href="https://afeld.github.io/emoji-css/emoji.css" rel="stylesheet"> <!--Totally optional :) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.min.js"
            integrity="sha256-xKeoJ50pzbUGkpQxDYHD7o7hxe0LaOGeguUidbq6vis=" crossorigin="anonymous"></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
@guest
@else
    <nav id="header" class="bg-white fixed w-full z-10 top-0 shadow">
        <div class="w-full container mx-auto flex flex-wrap items-center mt-0 pt-3 pb-3 lg:pb-0">
            <div class="w-full pl-2 md:pl-0 sm:pl-4">
                <a class="text-gray-900 text-base xl:text-xl no-underline hover:no-underline font-bold" href="#">
                    <img class="w-5 m-1 rounded-full float-left" src="{{asset('img/motork-profile.png')}}" alt="logo">Motork Budgets
                </a>
            </div>
            <div class="w-1/2 flex-grow lg:flex lg:items-center lg:w-auto hidden lg:block mt-2 lg:mt-0 bg-white z-20"
                 id="nav-content">
                <ul class="list-reset lg:flex flex-1 items-center px-4 md:px-0">
                    <li class="mr-6 my-2 md:my-0">
                        <a href="#"
                           class="block py-1 md:py-3 pl-1 pr-3 align-middle @if(Route::currentRouteName() === 'home') text-red-mk border-red-mk @else text-gray-500 border-white  @endif hover:text-red-dark-mk hover:border-red-dark-mk no-underline border-b-2">
                            <i class="fas fa-home fa-fw mr-3 @if(Route::currentRouteName() === 'home') text-red-mk @endif"></i><span
                                class="pb-1 md:pb-0 text-sm">{{__('Home')}}</span>
                        </a>
                    </li>
                    <li class="mr-6 my-2 md:my-0">
                        <a href="#"
                           class="block py-1 md:py-3 pl-1 pr-3  @if(Route::currentRouteName() === 'expense') text-red-mk border-red-mk @else text-gray-500 border-white @endif hover:text-red-dark-mk hover:border-red-dark-mk align-middle no-underline border-b-2">
                            <i class="fa fa-wallet fa-fw mr-3 @if(Route::currentRouteName() === 'expense') text-red-mk @endif"></i><span
                                class="pb-1 md:pb-0 text-sm">{{__('Expense')}}</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="w-1/2 pr-0">
                <div class="flex relative inline-block md:float-none lg:float-right xl:float-right">
                    <div class="relative text-sm w-56">
                        <button id="userButton" class="flex items-center focus:outline-none mr-3">
                            <img class="w-8 h-8 rounded-full mr-4" src="http://i.pravatar.cc/300"
                                 alt="Avatar of User">
                            <span class="hidden md:inline-block">Hi, {{ Auth::user()->name }} </span>
                            <svg class="pl-2 h-2" version="1.1" xmlns="http://www.w3.org/2000/svg"
                                 viewBox="0 0 129 129"
                                 xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 129 129">
                                <g>
                                    <path
                                        d="m121.3,34.6c-1.6-1.6-4.2-1.6-5.8,0l-51,51.1-51.1-51.1c-1.6-1.6-4.2-1.6-5.8,0-1.6,1.6-1.6,4.2 0,5.8l53.9,53.9c0.8,0.8 1.8,1.2 2.9,1.2 1,0 2.1-0.4 2.9-1.2l53.9-53.9c1.7-1.6 1.7-4.2 0.1-5.8z"/>
                                </g>
                            </svg>
                        </button>
                        <div id="userMenu"
                             class="bg-white rounded shadow-md mt-2 absolute mt-12 top-0 right-0 min-w-full overflow-auto z-30 lg:mr-8 invisible">
                            <ul class="list-reset">
                                <li>
                                    <a href="#"
                                       class="px-4 py-2 block text-gray-900 hover:bg-gray-400 no-underline hover:no-underline">
                                        {{ __('My account') }}
                                    </a>
                                </li>
                                <li>
                                    <hr class="border-t mx-2 border-gray-400">
                                </li>
                                <li>
                                    <a class="px-4 py-2 block text-gray-900 hover:bg-gray-400 no-underline hover:no-underline"
                                       href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>


                    <div class="block lg:hidden pr-4">
                        <button id="nav-toggle"
                                class="flex items-center px-3 py-2 border rounded text-gray-500 border-gray-600 hover:text-gray-900 hover:border-teal-500 appearance-none focus:outline-none">
                            <svg class="fill-current h-3 w-3" viewBox="0 0 20 20"
                                 xmlns="http://www.w3.org/2000/svg"><title>
                                    Menu</title>
                                <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </nav>
@endguest
@yield('content')
@guest
@else
    <footer class="bg-white border-t border-gray-400 shadow">
        <div class="container max-w-md mx-auto flex py-8">

            <div class="w-full mx-auto flex flex-wrap">
                <div class="flex w-full md:w-1/2 ">
                    <div class="px-8">
                        <h3 class="font-bold font-bold text-gray-900">About</h3>
                        <p class="py-4 text-gray-600 text-sm">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas vel mi ut felis tempus
                            commodo
                            nec id erat. Suspendisse consectetur dapibus velit ut lacinia.
                        </p>
                    </div>
                </div>

                <div class="flex w-full md:w-1/2">
                    <div class="px-8">
                        <h3 class="font-bold font-bold text-gray-900">Social</h3>
                        <ul class="list-reset items-center text-sm pt-3">
                            <li>
                                <a class="inline-block text-gray-600 no-underline hover:text-gray-900 hover:text-underline py-1"
                                   href="#">Add social link</a>
                            </li>
                            <li>
                                <a class="inline-block text-gray-600 no-underline hover:text-gray-900 hover:text-underline py-1"
                                   href="#">Add social link</a>
                            </li>
                            <li>
                                <a class="inline-block text-gray-600 no-underline hover:text-gray-900 hover:text-underline py-1"
                                   href="#">Add social link</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>


        </div>
    </footer>
    <script>
        /*Toggle dropdown list*/
        /*https://gist.github.com/slavapas/593e8e50cf4cc16ac972afcbad4f70c8*/

        var userMenuDiv = document.getElementById("userMenu");
        var userMenu = document.getElementById("userButton");

        var navMenuDiv = document.getElementById("nav-content");
        var navMenu = document.getElementById("nav-toggle");

        document.onclick = check;

        function check(e) {
            var target = (e && e.target) || (event && event.srcElement);

            //User Menu
            if (!checkParent(target, userMenuDiv)) {
                // click NOT on the menu
                if (checkParent(target, userMenu)) {
                    // click on the link
                    if (userMenuDiv.classList.contains("invisible")) {
                        userMenuDiv.classList.remove("invisible");
                    } else {
                        userMenuDiv.classList.add("invisible");
                    }
                } else {
                    // click both outside link and outside menu, hide menu
                    userMenuDiv.classList.add("invisible");
                }
            }

            //Nav Menu
            if (!checkParent(target, navMenuDiv)) {
                // click NOT on the menu
                if (checkParent(target, navMenu)) {
                    // click on the link
                    if (navMenuDiv.classList.contains("hidden")) {
                        navMenuDiv.classList.remove("hidden");
                    } else {
                        navMenuDiv.classList.add("hidden");
                    }
                } else {
                    // click both outside link and outside menu, hide menu
                    navMenuDiv.classList.add("hidden");
                }
            }

        }

        function checkParent(t, elm) {
            while (t.parentNode) {
                if (t == elm) {
                    return true;
                }
                t = t.parentNode;
            }
            return false;
        }


    </script>
@endguest
</body>
</html>
