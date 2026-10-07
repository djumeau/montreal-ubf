<!-- Need prayer? Sends to the contact page, with "Prayer Support" chosen as the subject -->
<aside class="flex flex-col sm:flex-row items-center gap-4 p-5 bg-slate-800/60 border border-white rounded-lg">
    <span class="shrink-0 grid place-items-center size-14 rounded-full bg-sky-900 outline-1 outline-white"
        aria-hidden="true">
        <i class="fa-solid fa-hands-praying text-2xl"></i>
    </span>

    <div class="flex-1 text-center sm:text-left">
        <h3 class="text-lg font-bold">{{ __('home/prayer.need_title') }}</h3>
        <p class="text-sm text-slate-300">{{ __('home/prayer.need_text') }}</p>
    </div>

    <a href="{{ route(__('nav.contact.name'), ['inquiry' => \App\Enums\InquiryType::PRAYER->value]) }}"
        class="shrink-0 inline-flex items-center gap-2 p-2 leading-tight border border-transparent bg-sky-900 hover:bg-sky-950 text-white text-sm font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer">
        {{ __('home/prayer.need_button') }}<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
    </a>
</aside>
