<!-- "Processing..." with a spinning circular arrow, shown while a form is being sent (until the page comes back with its status message).
     Goes inside an Alpine scope holding "processing"; set it with @submit="processing = true" on the form -->
<div x-show="processing" x-cloak role="status"
    {{ $attributes->merge(['class' => 'flex items-center justify-center gap-2 py-2 text-slate-100']) }}>
    <i class="fa-solid fa-arrows-rotate fa-spin" aria-hidden="true"></i>
    <span>{{ __('dashboard/index.processing') }}</span>
</div>
