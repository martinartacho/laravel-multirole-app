<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }} 
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @auth
                @if(auth()->user()->hasRole('admin'))
                    <x-dashboard.admin />
                @elseif(auth()->user()->hasRole('gestor'))
                    <x-dashboard.gestor />
                @elseif(auth()->user()->hasRole('editor'))
                    <x-dashboard.editor /> 
                @elseif(auth()->user()->hasRole('editor'))
                    <x-dashboard.user />
                @else
                    <x-dashboard.invited />
                @endif
            @endauth
        </div>
    </div>
</x-app-layout>