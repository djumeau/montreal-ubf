@props([
    'topic', // One main topic of the category, with its subtopics
])

<li class="flex gap-3 px-4 py-3 even:bg-black/50">
    <i class="fa-solid fa-cross mt-1 shrink-0 text-white" aria-hidden="true"></i>

    <div class="flex-1 min-w-0">
        <p class="whitespace-pre-line">{{ $topic->current_topic }}</p>

        @if ($topic->subtopics->isNotEmpty())
            <ul class="mt-2 ml-1 pl-3 space-y-1 border-l border-slate-600 text-sm text-slate-300">
                @foreach ($topic->subtopics as $subtopic)
                    <li class="whitespace-pre-line">
                        {{ $subtopic->current_topic }}
                        @if ($subtopic->answered)
                            <i class="fa-solid fa-square-check ml-1 text-emerald-400"
                                title="{{ __('home/prayer.answered') }}" aria-hidden="true"></i>
                            <span class="sr-only">{{ __('home/prayer.answered') }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($topic->url)
            <a href="{{ $topic->url }}" target="_blank" rel="noopener noreferrer"
                class="inline-flex items-center gap-1 mt-1 text-sm text-sky-400 hover:text-sky-300 hover:underline">
                {{ __('home/prayer.more') }}<i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i>
            </a>
        @endif
    </div>

    @if ($topic->answered)
        <span
            class="shrink-0 self-start inline-flex items-center gap-1 px-2 py-0.5 text-xs text-emerald-300 border border-emerald-600 rounded-full">
            <i class="fa-solid fa-square-check" aria-hidden="true"></i>{{ __('home/prayer.answered') }}
        </span>
    @endif

</li>
