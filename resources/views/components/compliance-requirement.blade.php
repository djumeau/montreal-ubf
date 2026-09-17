{{-- cookie-banner.blade.php or directly inside components/layout.blade.php --}}
<div x-data="{
    showBanner: false,
    init() {
        // Check localStorage on load; show banner if choice is not made
        this.showBanner = !localStorage.getItem('privacy_consent_given');
    },
    setConsent(choice) {
        localStorage.setItem('privacy_consent_given', choice);
        this.showBanner = false;

        // Dispatch custom window event if other parts of your app need to know
        window.dispatchEvent(new CustomEvent('privacy-consent-updated', { detail: choice }));
    }
}" x-show="showBanner" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-10"
    class="fixed bottom-0 left-0 right-0 z-50 p-4 md:p-6 bg-slate-900/98 text-slate-100 border-t outline-1 outline-white shadow-2xl"
    style="display: none;" {{-- Prevents flashing on page load before Alpine loads --}}>

    <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-start md:items-center justify-between gap-4">

        {{-- Bilingual Content Wrapper based on Laravel Locale --}}
        <div class="flex-1 text-sm leading-relaxed">

            <p class="font-semibold text-base mb-1 text-white">{{ __('home/index.consent.title') }}</p>

            <p>{{ __('home/index.consent.content_1') }}</p>
            <p>{{ __('home/index.consent.content_2') }}
                <a href="{{ __('nav.confidentiality.url') }}"
                    class="underline text-sky-400 hover:text-sky-300 font-medium">{{ __('home/index.consent.content_3') }}</a>.

        </div>

        {{-- Law 25 Compliant Buttons: Equal Prominence & Tailwind v4 Utilities --}}
        <div class="flex items-center gap-3 w-full md:w-auto shrink-0 justify-end">
            <button @click="setConsent('reject')" type="button"
                class="w-1/2 md:w-auto p-2 text-xs font-semibold uppercase tracking-wider bg-sky-900/50 justify-right hover:bg-sky-950/50 text-white rounded cursor-pointer outline-1 outline-white focus:shadow-outline transition-colors duration-200">
                {{ __('home/index.consent.reject') }}
            </button>

            <button @click="setConsent('accept')" type="button"
                class="w-1/2 md:w-auto p-2 text-xs font-semibold uppercase tracking-wider bg-sky-900/50 justify-right hover:bg-sky-950/50 text-white rounded cursor-pointer outline-1 outline-white focus:shadow-outline transition-colors duration-200">
                {{ __('home/index.consent.accept') }}
            </button>
        </div>



    </div>

</div>
