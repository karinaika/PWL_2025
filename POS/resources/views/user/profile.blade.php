@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="card-title mb-0">Profil Pengguna</h3>
        </div>
        <div class="card-body">
            <div class="row">

                <!-- Kolom kiri: Foto profil dan form upload -->
                <div class="col-md-4 text-center border-end">
                    <div class="p-3">
                        <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('img/default-profile.png') }}"
                            class="img-fluid rounded-circle shadow mb-3" alt="User Image" id="preview-image"
                            style="width: 200px; height: 200px; object-fit: cover;">

                        <!-- Form upload foto -->
                        <form action="{{ route('user.updatePhoto') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group text-start">
                                <label for="profile_picture">Pilih Foto</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture"
                                    accept="image/*">
                                @error('profile_picture')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">
                                <i class="fas fa-upload me-1"></i> Upload Foto
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Kolom kanan: Data user -->
                <div class="col-md-8">
                    <h4 class="mb-3">Informasi Pengguna</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Username</th>
                            <td>: {{ $user->username }}</td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>: {{ $user->nama }}</td>
                        </tr>
                        <tr>
                            <th>Level</th>
                            <td>: {{ $user->level->level_nama ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#profile_picture').on('change', function () {
                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#preview-image').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    </script>
@endpush