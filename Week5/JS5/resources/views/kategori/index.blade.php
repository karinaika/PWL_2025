@extends('layouts.app')

@section('subtitle', 'Kategori')
@section('content_header_title', 'Home')
@section('content_header_subtitle', 'Kategori')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                Manage Kategori
                <a href="{{ route('kategori.create') }}" class="btn btn-primary float-right">+ Tambah Kategori</a>
            </div>
            <div class="card-body">
                {{ $dataTable->table(['class' => 'table table-bordered table-striped', 'id' => 'kategoriTable']) }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush
<table id="tabelKategori" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Kategori</th>
            <th>Aksi</th> <!-- Tambahkan kolom aksi -->
        </tr>
    </thead>
    <tbody>
        @foreach ($kategori as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->nama_kategori }}</td>
                <td>
                    <a href="{{ url('/kategori/edit/' . $item->id) }}" class="btn btn-warning btn-sm">
                        Edit
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
