@php
    use App\Support\HeroImages;

    $text = fn (string $key, array $replace = []) => __('dashboard/manage-home-page/index.' . $key, $replace);

    // Upload: errors come from the "uploadHeroImages" bag
    $uploadErrors = $errors->uploadHeroImages;
    $maxMegabytes = round(HeroImages::maxKilobytes() / 1024, 1);

    // The two drop zones, one per image of a slide
    $zones = [
        'desktop' => ['icon' => 'fa-desktop', 'size' => '1920 × 1080 (16:9)'],
        'mobile' => ['icon' => 'fa-mobile-screen', 'size' => '1080 × 1080 (1:1)'],
    ];

    // Preview carousel: only the slides with both images (see the Image Library for the others)
    $previewSlides = $slides->where('complete', true)->values();

    // "Sep 25, 2026" / "25 sept. 2026"
    $uploadedOn = fn (array $image) => $text('uploaded_on', ['date' => $image['uploaded_at']->isoFormat('ll')]);
@endphp

<x-layout class="bg-slate-900" textColor="text-white">

    <!-- dashboards/manage-home-page.blade.php -->

    <x-slot name="title">{{ __('header.name') }} - {{ __('dashboard/index.title') }}</x-slot>

    <h2 class='text-right text-2xl font-bold pt-18 pb-6'>{{ __('dashboard/index.welcome', ['name' => $user->name]) }}
    </h2>

    <!-- UI - Left sidebar with main area -->
    <div x-data="{
        sidebarOpen: true,
        slide: 0, // Slide shown in the Hero Preview (a card of the Image Library shows its own)
        slideCount: {{ $previewSlides->count() }},
        init() {
            // The preview moves on by itself, like a carousel
            if (this.slideCount > 1) {
                setInterval(() => this.slide = (this.slide + 1) % this.slideCount, 5000);
            }
        },
    }" class="flex min-h-screen text-white">

        <x-dashboards::left-column />

        <!-- Right Column: Interactive Work Space Context -->
        <main :class="sidebarOpen ? 'hidden md:block' : 'block'" class="flex-1 min-w-0 pl-6 overflow-y-auto">

            <!-- UI Segment: Manage Home Page -->
            @if (auth()->user()->canManageRoles())
                <div class="w-full border rounded-sm border-slate-100">

                    <div class="p-4">
                        <h2 class="text-lg font-bold text-slate-100">{{ $text('manage_home_page') }}</h2>
                    </div>

                    <!-- Display Success Notifications -->
                    @if (session('status'))
                        <div class="flex items-center justify-left ml-4 mb-4">
                            <div class="w-fit p-2 border rounded-sm border-emerald-600 text-emerald-400 text-sm">
                                {{ session('status') }}
                            </div>
                        </div>
                    @endif

                    <!-- Hero Images: preview, upload, then the library of the images uploaded -->
                    <section id="hero-images" class="px-4 pb-4 space-y-4">

                        <div>
                            <h3 class="text-2xl font-bold text-slate-100">{{ $text('hero_images') }}</h3>
                            <p class="mt-1 text-sm text-slate-300">{{ $text('hero_images_intro') }}</p>
                        </div>

                        <!-- Hero Preview (Carousel): the part of each image the hero shows, on desktop and on a phone -->
                        <div class="border border-slate-600 rounded-sm p-3">
                            <h4 class="font-bold text-slate-100 mb-3">{{ $text('hero_preview') }}</h4>

                            <div class="grid grid-cols-1 xl:grid-cols-[1fr_16rem] gap-4">

                                @if ($previewSlides->isEmpty())
                                    <div class="grid place-items-center min-h-40 p-4 rounded-sm border border-dashed border-slate-600 text-sm text-slate-400 italic text-center">
                                        {{ $text('hero_preview_empty') }}
                                    </div>
                                @else
                                    <div class="flex items-center gap-4">

                                        <!-- Desktop: the hero is 660px high, so a 1920px wide screen shows this band of the image -->
                                        <div class="relative flex-1 min-w-0 aspect-1920/660 overflow-hidden rounded-sm border border-slate-600 bg-black">
                                            @foreach ($previewSlides as $index => $previewSlide)
                                                <img src="{{ $previewSlide['desktop']['url'] }}" alt="{{ $previewSlide['desktop']['name'] }}"
                                                    x-show="slide === {{ $index }}" x-transition.opacity.duration.500ms @if (!$loop->first) x-cloak @endif
                                                    class="absolute inset-0 size-full object-cover">
                                            @endforeach

                                            <x-dashboards::carousel-pills :count="$previewSlides->count()" />
                                        </div>

                                        <!-- Phone: the hero is 580px high there -->
                                        <div class="relative shrink-0 w-24 sm:w-32 aspect-390/580 overflow-hidden rounded-2xl border-4 border-slate-500 bg-black">
                                            @foreach ($previewSlides as $index => $previewSlide)
                                                <img src="{{ $previewSlide['mobile']['url'] }}" alt="{{ $previewSlide['mobile']['name'] }}"
                                                    x-show="slide === {{ $index }}" x-transition.opacity.duration.500ms @if (!$loop->first) x-cloak @endif
                                                    class="absolute inset-0 size-full object-cover">
                                            @endforeach

                                            <x-dashboards::carousel-pills :count="$previewSlides->count()" small />
                                        </div>

                                    </div>
                                @endif

                                <!-- How it works -->
                                <div class="p-3 rounded-sm bg-black/30 text-xs text-slate-300">
                                    <h5 class="mb-2 text-sm font-bold text-slate-100">
                                        <i class="fa-solid fa-circle-info mr-1 text-blue-400" aria-hidden="true"></i>{{ $text('how_it_works') }}
                                    </h5>

                                    <ul class="space-y-2 list-disc pl-4 marker:text-blue-400">
                                        <li>
                                            {{ $text('how_two_images') }}
                                            <ul class="mt-1 space-y-1 list-disc pl-4 marker:text-blue-400">
                                                <li>{{ $text('how_desktop') }}</li>
                                                <li>{{ $text('how_mobile') }}</li>
                                            </ul>
                                        </li>
                                        <li>
                                            {{ $text('how_names') }}
                                            <div class="mt-1">
                                                <code class="px-1.5 py-0.5 rounded-sm bg-slate-700 text-slate-100">-desktop</code>
                                                {{ $text('how_names_and') }}
                                                <code class="px-1.5 py-0.5 rounded-sm bg-slate-700 text-slate-100">-mobile</code>
                                            </div>
                                            <div class="mt-1">{{ $text('how_pair') }}</div>
                                        </li>
                                        <li>{{ $text('how_carousel') }}</li>
                                    </ul>
                                </div>

                            </div>
                        </div>

                        <div class="grid grid-cols-1 xl:grid-cols-[1fr_16rem] gap-4">

                            <!-- Upload New Hero Images: one drop zone per image; either or both can be sent -->
                            <form action="{{ route('hero-images.store') }}" method="POST" enctype="multipart/form-data"
                                class="border border-slate-600 rounded-sm p-3">
                                @csrf

                                <h4 class="font-bold text-slate-100 mb-3">{{ $text('upload_new') }}</h4>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach ($zones as $type => $zone)
                                        <!-- Drop zone: a dropped or browsed file goes in the file input; a wrong name ending is said right away -->
                                        <div x-data="{
                                            over: false,
                                            name: '',
                                            preview: null,
                                            wrongName: false,
                                            pick(file) {
                                                this.name = file.name;
                                                this.wrongName = !file.name.replace(/\.[^.]+$/, '').toLowerCase().endsWith('-{{ $type }}');
                                                if (this.preview) URL.revokeObjectURL(this.preview);
                                                this.preview = URL.createObjectURL(file);
                                            },
                                            drop(event) {
                                                this.over = false;
                                                const file = event.dataTransfer.files[0];
                                                if (!file) return;

                                                // Only the first file dropped: a slide has one image of each kind
                                                const files = new DataTransfer();
                                                files.items.add(file);
                                                this.$refs.input.files = files.files;
                                                this.pick(file);
                                            },
                                        }">

                                            <div class="flex items-start gap-3 mb-2">
                                                <i class="fa-solid {{ $zone['icon'] }} w-6 pt-1 text-center text-xl text-slate-300" aria-hidden="true"></i>
                                                <div class="text-xs text-slate-300">
                                                    <div class="text-sm font-bold text-slate-100">{{ $text($type . '_image') }}</div>
                                                    <div>{{ $text('recommended_size', ['size' => $zone['size']]) }}</div>
                                                    <div>{{ $text('accepted_formats', ['size' => $maxMegabytes]) }}</div>
                                                </div>
                                            </div>

                                            <div @dragover.prevent="over = true" @dragleave.prevent="over = false" @drop.prevent="drop($event)"
                                                :class="over ? 'border-blue-400 bg-blue-950/40' : '{{ $uploadErrors->has($type) ? 'border-red-500' : 'border-slate-500' }}'"
                                                class="flex flex-col items-center justify-center gap-2 min-h-44 p-4 rounded-sm border border-dashed text-center text-sm transition-colors">

                                                <!-- Image chosen: its thumbnail and name take the place of the cloud -->
                                                <img x-show="preview" x-cloak :src="preview" alt="" class="max-h-24 rounded-sm object-contain border border-slate-600">
                                                <span x-show="name" x-cloak x-text="name" class="max-w-full truncate text-xs text-slate-100"></span>

                                                <i x-show="!preview" class="fa-solid fa-cloud-arrow-up text-3xl text-blue-400" aria-hidden="true"></i>
                                                <span x-show="!preview">{{ $text('drop_' . $type) }}</span>

                                                <span class="text-xs text-slate-300" :class="wrongName && 'text-red-400'">
                                                    {{ $text('name_ends_with') }}
                                                    <code class="px-1.5 py-0.5 rounded-sm bg-slate-700 text-slate-100">-{{ $type }}</code>
                                                </span>

                                                <input x-ref="input" id="hero_image_{{ $type }}" type="file" name="{{ $type }}"
                                                    accept="image/jpeg,image/png,image/webp" class="sr-only"
                                                    @change="$event.target.files[0] && pick($event.target.files[0])">

                                                <label for="hero_image_{{ $type }}"
                                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium border border-white rounded-sm cursor-pointer">
                                                    {{ $text('browse_files') }}
                                                </label>
                                            </div>

                                            @if ($uploadErrors->has($type))
                                                <p class="text-xs text-red-500 mt-1">{{ $uploadErrors->first($type) }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-3 mt-3">
                                    <p class="text-xs text-slate-400">{{ $text('replace_hint') }}</p>

                                    <button type="submit"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium border border-white rounded-sm cursor-pointer">
                                        <i class="fas fa-upload mr-1" aria-hidden="true"></i>{{ __('dashboard/index.upload') }}
                                    </button>
                                </div>
                            </form>

                            <!-- Image Guidelines, with the safe center area of each format -->
                            <div class="border border-slate-600 rounded-sm p-3 text-xs text-slate-300">
                                <h4 class="mb-2 text-sm font-bold text-slate-100">
                                    <i class="fa-regular fa-image mr-1 text-blue-400" aria-hidden="true"></i>{{ $text('guidelines') }}
                                </h4>

                                <ul class="space-y-2 list-disc pl-4 marker:text-blue-400">
                                    <li>{{ $text('guideline_quality') }}</li>
                                    <li>{{ $text('guideline_pairs') }}</li>
                                    <li>{{ $text('guideline_safe_area') }}</li>
                                    <li>{{ $text('guideline_contrast') }}</li>
                                </ul>

                                <div class="flex items-end justify-center gap-4 mt-3 text-center" aria-hidden="true">
                                    <div>
                                        <div class="grid place-items-center w-28 aspect-video rounded-sm bg-slate-700">
                                            <div class="w-1/2 h-3/5 border border-dashed border-white"></div>
                                        </div>
                                        <div class="mt-1 font-bold text-slate-100">{{ $text('desktop_ratio') }}</div>
                                        <div>1920 × 1080</div>
                                    </div>
                                    <div>
                                        <div class="grid place-items-center w-14 aspect-square mx-auto rounded-sm bg-slate-700">
                                            <div class="w-3/5 h-3/5 border border-dashed border-white"></div>
                                        </div>
                                        <div class="mt-1 font-bold text-slate-100">{{ $text('mobile_ratio') }}</div>
                                        <div>1080 × 1080</div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Image Library: one card per slide, newest first, in a single row that scrolls sideways (the bar shows under the cards);
                             clicking a card shows its slide in the preview -->
                        <div class="border border-slate-600 rounded-sm p-3">
                            <h4 class="font-bold text-slate-100 mb-3">{{ $text('image_library') }}</h4>

                            @if ($slides->isEmpty())
                                <p class="py-4 text-center text-sm text-slate-400">{{ $text('library_empty') }}</p>
                            @endif

                            <div class="flex gap-3 overflow-x-auto pb-3 [scrollbar-width:thin] [scrollbar-color:var(--color-slate-500)_transparent]">
                                @foreach ($slides as $slide)
                                    @php $previewIndex = $previewSlides->search(fn ($previewSlide) => $previewSlide['slug'] === $slide['slug']); @endphp

                                    <div x-data="{ confirming: false }"
                                        @if ($previewIndex !== false)
                                            @click="slide = {{ $previewIndex }}" :class="slide === {{ $previewIndex }} ? 'border-blue-500' : 'border-slate-600'"
                                        @endif
                                        @class(['shrink-0 w-80 max-w-full p-2 rounded-sm border bg-black/30 text-xs', 'cursor-pointer' => $previewIndex !== false, 'border-amber-500' => $previewIndex === false])>

                                        <div class="grid grid-cols-[1fr_5rem] gap-2">
                                            @foreach (HeroImages::TYPES as $type)
                                                <div class="min-w-0">
                                                    @if ($slide[$type])
                                                        <img src="{{ $slide[$type]['url'] }}" alt="" loading="lazy"
                                                            class="h-20 w-full rounded-sm object-cover border border-slate-600">
                                                        <div class="mt-1 truncate font-bold text-slate-100" title="{{ $slide[$type]['name'] }}">{{ $slide[$type]['name'] }}</div>
                                                        <div class="truncate text-slate-400">{{ $uploadedOn($slide[$type]) }}</div>
                                                    @else
                                                        <div class="grid place-items-center h-20 rounded-sm border border-dashed border-slate-600 text-slate-500 italic">
                                                            {{ $text('missing_image') }}
                                                        </div>
                                                        <div class="mt-1 truncate text-slate-500">{{ $slide['slug'] }}-{{ $type }}</div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>

                                        @unless ($slide['complete'])
                                            <p class="mt-2 text-amber-300">
                                                <i class="fa-solid fa-triangle-exclamation mr-1" aria-hidden="true"></i>{{ $text('slide_incomplete') }}
                                            </p>
                                        @endunless

                                        <!-- Delete: asks first, inline -->
                                        <div class="flex justify-center mt-2" @click.stop>
                                            <button type="button" x-show="!confirming" @click="confirming = true"
                                                class="px-3 py-1 bg-red-700 hover:bg-red-800 text-white border border-white rounded-sm cursor-pointer">
                                                <i class="fa-solid fa-trash-can mr-1" aria-hidden="true"></i>{{ $text('delete_slide') }}
                                            </button>

                                            <form x-show="confirming" x-cloak action="{{ route('hero-images.destroy', $slide['slug']) }}" method="POST"
                                                class="flex flex-wrap items-center justify-center gap-2">
                                                @csrf
                                                @method('DELETE')
                                                <span class="text-amber-300">{{ $text('delete_slide_confirm') }}</span>
                                                <button type="button" @click="confirming = false"
                                                    class="px-2 py-1 border border-white rounded-sm hover:bg-sky-950/50 cursor-pointer">
                                                    {{ __('dashboard/index.no') }}
                                                </button>
                                                <button type="submit"
                                                    class="px-2 py-1 bg-red-700 hover:bg-red-800 text-white border border-white rounded-sm cursor-pointer">
                                                    {{ __('dashboard/index.yes') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </section>

                </div>
            @endif
            <!-- END UI Segment: Manage Home Page -->

        </main>

    </div>

</x-layout>
