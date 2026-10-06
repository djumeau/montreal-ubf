@props([
    'inquiry', // Contact form message (Inquiry): its id is the checkbox's value, its subject the text beside it
])

<!-- Inquiry Subject (Manage Inquiries): checkbox to select the message, then its subject.
     .stop on its clicks and keys: the inquiry item around it is clickable too, and ticking must not open it -->
<label {{ $attributes->merge(['class' => 'flex items-center gap-2 cursor-pointer']) }} @click.stop @keydown.enter.stop @keydown.space.stop>
    <!-- form=: sent with the page's Delete Selected form (manage-inquiries), which is outside the item.
         Drawn by hand (appearance-none) so it keeps a white border: dark when empty, blue with a white check when ticked -->
    <span class="relative inline-grid place-items-center size-4 shrink-0">
        <input type="checkbox" name="inquiries[]" value="{{ $inquiry->id }}" form="delete_selected_form"
            class="peer appearance-none size-4 m-0 border border-white rounded-sm bg-slate-900 checked:bg-blue-600 cursor-pointer outline-white focus-visible:outline-2 focus-visible:outline-offset-2"
            aria-label="{{ __('dashboard/manage-inquiries/index.select_inquiry', ['name' => $inquiry->name]) }}">
        {{-- opacity, not hidden: Font Awesome's display: inline-block (outside Tailwind's layers) would win over "hidden" --}}
        <i class="fa-solid fa-check absolute text-[10px] text-white opacity-0 peer-checked:opacity-100 pointer-events-none" aria-hidden="true"></i>
    </span>
    <span class="font-bold text-slate-100">{{ $inquiry->inquiry->label() }}</span>
</label>
