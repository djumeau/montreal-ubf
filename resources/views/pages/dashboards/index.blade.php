@props(['user' => null])

<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('dashboard/index.title') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pt-18 pb-8'>{{ __('dashboard/index.welcome', ['name' => $user->name]) }}
    </h1>

</x-layout>
