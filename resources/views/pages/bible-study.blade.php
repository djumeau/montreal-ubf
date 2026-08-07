<x-layout class="bg-slate-900" textColor="text-white">

    <x-slot name="title">Bible Studies</x-slot>

    <h1>Bible Studies</h1>

    <ul>
        @forelse($biblestudies as $biblestudy)
            <li>
                <a href="{{ route('bible-studies.show', $biblestudy->id) }}">
                    {{ $biblestudy->title }}
                </a>
            </li>
        @empty
            <li>No Bible studies available.</li>
        @endforelse
    </ul>

</x-layout>
