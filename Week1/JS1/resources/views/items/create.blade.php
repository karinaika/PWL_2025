<!DOCTYPE html>
<html>

<head>
    <title>Add Item</title> <!-- Judul halaman -->
</head>

<body>
    <h1>Add Item</h1> <!-- Judul utama halaman -->

    <!-- Form untuk menambahkan item baru -->
    <form action="{{ route('items.store') }}" method="POST">
        @csrf <!-- Token keamanan Laravel -->

        <!-- Input untuk nama item -->
        <label for="name">Name:</label>
        <input type="text" name="name" required> <!-- Wajib diisi -->
        <br>

        <!-- Input untuk deskripsi item -->
        <label for="description">Description:</label>
        <textarea name="description" required></textarea> <!-- Wajib diisi -->
        <br>

        <!-- Tombol submit untuk menambahkan item -->
        <button type="submit">Add Item</button>
    </form>

    <!-- Link kembali ke halaman daftar item -->
    <a href="{{ route('items.index') }}">Back to List</a>
</body>

</html>