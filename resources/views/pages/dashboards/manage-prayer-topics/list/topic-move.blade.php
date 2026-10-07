@props([
    'prayerTopic',
    'canMove' => ['up' => true, 'down' => true], // Off for the first (up) and the last (down) of a list
])

<td class="py-3 px-2 whitespace-nowrap">

    <div class="flex flex-col items-center justify-center gap-2">

        @foreach (['up' => 'fa-arrow-up', 'down' => 'fa-arrow-down'] as $direction => $icon)
            <form action="{{ route('prayer-topics.move', $prayerTopic) }}" method="POST">

                @csrf
                @method('PUT')

                <input type="hidden" name="direction" value="{{ $direction }}">

                <button type="submit" @disabled(!$canMove[$direction])
                    title="{{ __('dashboard/manage-prayer-topics/index.move_' . $direction) }}"
                    aria-label="{{ __('dashboard/manage-prayer-topics/index.move_' . $direction) }}"
                    class="px-2 py-1.5 bg-slate-700 hover:bg-slate-600 text-white text-xs font-medium rounded outline-1 outline-white hover:outline-2 focus:shadow-outline cursor-pointer disabled:opacity-30 disabled:cursor-default disabled:hover:bg-slate-700 disabled:hover:outline-1">

                    <i class="fa-solid {{ $icon }}"></i>

                </button>

            </form>
        @endforeach

    </div>

</td>
