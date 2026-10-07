@php
    use App\Enums\PrayerCategory;
    use App\Enums\Role;
    use App\Models\PrayerTopic;

    // The viewer's role; Guest when not logged in
$role = auth()->user()?->role ?? Role::GUEST;

// Main topics open to the role, in the order chosen on Manage Prayer Topics, 5 per page, each with its subtopics open to the role
// (a subtopic never shows without its main topic). The page is ?prayer_page=, so it does not clash with another list of the page;
// its links come back to this section (#prayer-topics)
$topics = PrayerTopic::whereNull('parent_id')
    ->visibleToRole($role)
    ->with(['subtopics' => fn($query) => $query->visibleToRole($role)])
    ->ordered()
    ->paginate(5, ['*'], 'prayer_page')
    ->withQueryString()
    ->fragment('prayer-topics');

// A card per category that has topics on this page, in the order of the enum
$categories = collect(PrayerCategory::cases())
    ->map(
        fn(PrayerCategory $category) => [
            'category' => $category,
            'topics' => $topics->getCollection()->where('category', $category)->values(),
        ],
    )
    ->filter(fn($group) => $group['topics']->isNotEmpty())
        ->values();
@endphp

@if ($categories->isNotEmpty())
    <!-- Prayer Topics Section: a card per category, then the "Need prayer?" call -->
    <section id="prayer-topics" {{ $attributes->merge(['class' => 'text-white scroll-mt-24']) }} x-data="{ category: 'all' }"
        aria-labelledby="prayer_topics_title">

        <!-- Heading -->
        <div class="flex flex-col md:flex-row justify-center items-center md:items-start pb-6">
            <h2 id="prayer_topics_title" class="text-xl md:text-2xl font-bold text-white">{{ __('home/prayer.title') }}
            </h2>
        </div>

        <!-- Category filter (phones): All, then the categories that have topics -->
        @if ($categories->count() > 1)
            <div class="md:hidden flex flex-wrap gap-2 mb-4">
                @foreach (['all' => __('home/prayer.all')] + $categories->mapWithKeys(fn($group) => [$group['category']->value => $group['category']->label()])->all() as $value => $label)
                    <button type="button" @click="category = '{{ $value }}'"
                        :aria-pressed="category === '{{ $value }}'"
                        :class="category === '{{ $value }}' ? 'bg-sky-900 outline-2' :
                            'bg-sky-900/50 hover:bg-sky-950/50 outline-1 hover:outline-2'"
                        class="px-3 py-1 text-sm text-white font-medium rounded outline-white focus:shadow-outline cursor-pointer">{{ $label }}</button>
                @endforeach
            </div>
        @endif

        <!-- One card per category: its topics, each followed by its subtopics -->
        <div class="grid md:grid-cols-2 gap-5 mb-8">

            @foreach ($categories as $group)
                <x-prayer-topics.article :category="$group['category']" :topics="$group['topics']" :wide="$categories->count() === 1" />
            @endforeach

        </div>

        @if ($topics->hasPages())
            <div class="mb-8">
                {{ $topics->links('pagination.dashboard') }}
            </div>
        @endif

        <x-prayer-topics.request />

    </section>
@endif
