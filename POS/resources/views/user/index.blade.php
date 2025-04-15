@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Pengguna</h3>
            <div class="card-tools">
                <button onclick="modalAction(`{{ url('/user/import') }}`)" class="btn btn-info">Import User</button>
                <a href="{{ url('/user/export_excel') }}" class="btn btn-primary"><i class="fa fa-file- excel"></i> Export User (.xlsx)</a>
                <button onclick="modalAction(`{{ url('/user/create_ajax') }}`)" class="btn btn-success">Tambah Data (Ajax)</button>
                <a href="{{ url('/user/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file- pdf"></i> Export User (.pdf)</a>
            </div>
        </div>
        <div class="card-body">
            <div id="filter" class="form-horizontal filter-date p-2 border-bottom mb-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group form-group-sm row text-sm mb-0">
                            <label for="filter_level" class="col-md-1 col-form-label">Filter</label>
                            <div class="col-md-3">
                                <select name="filter_level" class="form-control form-control-sm filter_level">
                                    <option value="">- Semua -</option>
                                    @foreach($level as $l)
                                        <option value="{{ $l->level_id }}">{{ $l->level_nama }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Level Pengguna</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Users Table -->
            <table class="table table-bordered table-sm table-striped table-hover" id="table-user">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID Pengguna</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th>Level</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Import Form -->
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

        var tableUser;
        $(document).ready(function () {
            tableUser = $('#table-user').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ url('user/list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.filter_level = $('.filter_level').val();
                    }
                },
                columns: [
                    {
                        data: "DT_RowIndex",
                        className: "text-center",
                        width: "5%",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "user_id",
                        className: "",
                        width: "15%",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "username",
                        className: "",
                        width: "25%",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "nama",
                        className: "",
                        width: "25%",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "level_id",
                        className: "",
                        width: "15%",
                        orderable: true,
                        searchable: false,
                        render: function (data) {
                            const levels = {
                                1: 'Admin',
                                2: 'Manager',
                                3: 'Staff',
                                4: 'Customer'
                            };
                            return levels[data] || 'Unknown';
                        }
                    },
                    {
                        data: "aksi",
                        className: "text-center",
                        width: "15%",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#table-user_filter input').unbind().bind().on('keyup', function (e) {
                if (e.keyCode == 13) { // Enter key
                    tableUser.search(this.value).draw();
                }
            });

            $('.filter_level').change(function () {
                tableUser.draw();
            });
        });
    </script>
@endpush