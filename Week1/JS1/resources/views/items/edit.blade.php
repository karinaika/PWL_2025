<!DOCTYPE html>
<html>

<head>
    <title>Edit Item</title> <!-- Judul halaman -->
</head>

<body>
    <h1>Edit Item</h1> <!-- Judul utama halaman -->

    <!-- Form untuk mengedit item yang sudah ada -->
    <form action="{{ route('items.update', $item) }}" method="POST">
        @csrf <!-- Token keamanan Laravel -->
        @method('PUT') <!-- Metode HTTP PUT untuk mengupdate data -->

        <!-- Input untuk nama item, dengan nilai default dari data item -->
        <label for="name">Name:</label>
        <input type="text" name="name" value="{{ $item->name }}" required> <!-- Wajib diisi -->
        <br>

        <!-- Input untuk deskripsi item, dengan nilai default dari data item -->
        <label for="description">Description:</label>
        <textarea name="description" required>{{ $item->description }}</textarea> <!-- Wajib diisi -->
        <br>

        <!-- Tombol submit untuk mengupdate item -->
        <button type="submit">Update Item</button>
    </form>

    <!-- Link kembali ke halaman daftar item -->
    <a href="{{ route('items.index') }}">Back to List</a>
</body>

</html>