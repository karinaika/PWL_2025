@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Stok Barang</h3>
            <div class="card-tools">
                <button onclick="modalAction(`{{ url('/stok/import') }}`)" class="btn btn-info">Import Stok</button>
                <a href="{{ url('/stok/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export
                    Stok (.xlsx)</a>
                <button onclick="modalAction(`{{ url('/stok/create_ajax') }}`)" class="btn btn-success">Tambah Data
                    (Ajax)</button>
                <a href="{{ url('/stok/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Stok
                    (.pdf)</a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <table class="table table-bordered table-sm table-striped table-hover" id="table-stok">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Jumlah Stok</th>
                        <th>Tanggal</th>
                        <th>Petugas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false"
        data-width="75%"></div>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function () {
                $('#myModal').modal('show');
            });
        }

        var tableStok;
        $(document).ready(function () {
            tableStok = $('#table-stok').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('stok/list') }}",
                    type: "POST",
                    data: function (d) {
                        d.barang_id = $('.filter_barang').val();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', className: 'text-center', width: '5%', orderable: false, searchable: false },
                    { data: 'barang_kode', width: '15%' },
                    { data: 'barang_nama', width: '15%' },
                    { data: 'stok_jumlah', className: 'text-center', width: '10%' },
                    { data: 'stok_tanggal', className: 'text-center', width: '20%' },
                    { data: 'user_nama', width: '20%' },
                    { data: 'aksi', className: 'text-center', width: '15%', orderable: false, searchable: false }
                ]
            });

            $('#table-stok_filter input').unbind().bind().on('keyup', function (e) {
                if (e.keyCode == 13) {
                    tableStok.search(this.value).draw();
                }
            });

            $('.filter_barang').change(function () {
                tableStok.draw();
            });
        });
    </script>
@endpush