<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Profile</title>
</head>
<body>
    <h1>User Profile</h1>
    <p>ID: {{ $id }}</p>
    <p>Name: {{ $name }}</p>
    <a href="{{ url('/') }}">Back to Home</a>
</body>
</html>
