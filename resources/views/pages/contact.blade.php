<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">{{ __('header.name') }} - {{ __('contact.title') }}</x-slot>

    <h1 class='text-right text-4xl font-bold pt-18 pb-8'>{{ __('contact.title') }}</h1>

    <img src="{{ asset('images/conferences/2026-07_franco_tour_mtl.jpg') }}"
        alt="{{ __('contact.conference_image_alt') }}" class="w-full h-auto rounded-sm mb-4">

    <div class="flex flex-col justify-between items-center border rounded-sm outline-1 outline-slate-100 ">

        <h2 class="p-4 pb-0 text-2xl font-medium text-slate-100">{{ __('contact.subtitle') }}</h2>

        <p class="p-2 mx-2 text-slate-100">{{ __('contact.instructions') }}</p>

        <!-- Display Notification - If applicable -->
        @if (session('status'))
            <div class="flex items-center justify-left ml-4 mb-4">
                <div class="w-100 p-2 border rounded-sm border-emerald-600 text-emerald-400 text-sm">
                    {{ __(session('status')) }}
                </div>
            </div>
        @endif

        <form class="w-full ml-4" action="{{ route('contact.store') }}" method="POST">
            @csrf

            <div class="absolute left-[-9999px]" aria-hidden="true">
                <label for="website">Website</label>
                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="flex flex-col mr-4 items-center justify-center gap-y-2 p-2 mb-8">

                <x-inputs.text class="max-w-100" label="{{ __('contact.name') }}" id="name" name="name"
                    value="{{ old('name') }}" />

                <x-inputs.text class="max-w-100" label="{{ __('contact.email') }}" id="email" name="email"
                    type="email" value="{{ old('email') }}" />

                <x-inputs.select class="max-w-100" label="{{ __('contact.inquiring_about') }}" id="inquiring_about"
                    name="inquiring_about" :options="$inquiryOptions" value="{{ old('inquiring_about') }}" />

                <x-inputs.text-area class="max-w-100" label="{{ __('contact.message') }}" id="message" name="message"
                    value="{{ old('message') }}" />

                <x-submit>
                    {{ __('contact.send') }}
                </x-submit>

            </div>

        </form>

    </div>
    <!-- END UI Segment - Contact Form -->

    </div>



</x-layout>
