<!-- Progress of an upload sent with $store.upload.send() (see app.js); goes inside the form, which only shows it while it is the one being sent.
     The bar fills as the files go; at 100 % the server is saving them ("Processing..." until the result page comes) -->
<div {{ $attributes }}>
    <div x-show="$store.upload.form === $el.closest('form')" x-cloak class="mt-3 text-sm text-slate-100">
        <div role="progressbar" aria-valuemin="0" aria-valuemax="100" :aria-valuenow="$store.upload.progress"
            aria-label="{{ __('dashboard/index.upload') }}"
            class="h-3 w-full overflow-hidden rounded-sm border border-slate-500 bg-slate-900">
            <div class="h-full bg-sky-500 transition-[width] duration-200" :style="'width: ' + $store.upload.progress + '%'"></div>
        </div>

        <div class="flex items-center justify-center gap-2 py-2">
            <span x-show="$store.upload.progress < 100"
                x-text="@js(__('dashboard/index.uploading')).replace(':percent', $store.upload.progress)"></span>

            <span x-show="$store.upload.progress >= 100" role="status" class="flex items-center gap-2">
                <i class="fa-solid fa-arrows-rotate fa-spin" aria-hidden="true"></i>
                <span>{{ __('dashboard/index.processing') }}</span>
            </span>
        </div>

        <p class="text-center text-xs text-amber-300">{{ __('dashboard/index.upload_keep_open') }}</p>
    </div>

    <p x-show="$store.upload.failed === $el.closest('form')" x-cloak role="alert" class="mt-3 text-center text-sm text-red-400">
        {{ __('dashboard/index.upload_failed') }}
    </p>
</div>
