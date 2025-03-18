@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Edit Kategori</h3>
                    </div>

                    <form action="{{ route('kategori.update', $kategori->kategori_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <div class="form-group">
                                <label for="kodeKategori">Kode Kategori</label>
                                <input type="text" class="form-control" name="kodeKategori" id="kodeKategori"
                                    value="{{ $kategori->kategori_kode }}">
                            </div>

                            <div class="form-group">
                                <label for="namaKategori">Nama Kategori</label>
                                <input type="text" class="form-control" name="namaKategori" id="namaKategori"
                                    value="{{ $kategori->kategori_nama }}">
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection