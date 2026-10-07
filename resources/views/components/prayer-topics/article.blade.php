@props([
    'category', // PrayerCategory of the card
    'topics', // The category's main topics, each with its subtopics
    'wide' => false, // The only card of the section: it takes the full width
])

@php
    $headerImage = $topics->firstWhere('image')?->image_url; // The first image among the category's topics, if any
@endphp

<!-- A category's card: its heading, then its topics. Shown or hidden by the category filter of the section (phones),
     which holds "category" in its Alpine scope -->
<article x-show="category === 'all' || category === '{{ $category->value }}'" @class([
    'bg-slate-800/60 border border-white rounded-lg overflow-hidden',
    'md:col-span-2' => $wide,
])>

    <x-prayer-topics.header :category="$category" :header-image="$headerImage" />

    <!-- The topics of a category card -->
    <ul>
        @foreach ($topics as $topic)
            <x-prayer-topics.item :topic="$topic" />
        @endforeach
    </ul>

</article>
