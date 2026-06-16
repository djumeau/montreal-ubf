<x-layout>

    <x-slot name="title">Create Article</x-slot>

    <h1>Create a New Article</h1>

    <form action="/articles" method="POST">
        @csrf
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br><br>

        <label for="author">Author:</label>
        <input type="text" id="author" name="author" required><br><br>

        <label for="type">Type:</label>
        <input type="text" id="type" name="type" required><br><br>

        <button type="submit">Create Article</button>
    </form>

</x-layout>
