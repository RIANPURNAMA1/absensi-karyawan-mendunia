@extends('app')

@section('content')
    <div class="container-fluid">

        <div class="mb-3">
            <a href="{{ route('karyawan.index') }}" class="btn btn-secondary btn-sm">
                ← Kembali
            </a>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Detail Karyawan</h5>
            </div>

            <div class="card-body">
                <div class="row g-4">

                    <!-- FOTO -->
                    <div class="col-md-3 text-center">
                        <img src="{{ $karyawan->foto_profil
                            ? asset('storage/foto-karyawan/' . $karyawan->foto_profil)
                            : asset('assets/images/avatar/avatar-1.jpg') }}"
                            class="rounded-3 mb-3" width="200" height="250" style="object-fit: cover">

                        <h6 class="mb-0">{{ $karyawan->name }}</h6>
                        <small class="text-muted">{{ $karyawan->jabatan }}</small>
                    </div>

                    <!-- DATA -->
                    <div class="col-md-9">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">NIP</th>
                                <td>{{ $karyawan->nip }}</td>
                            </tr>
                            <tr>
                                <th>Divisi</th>
                                <td>{{ $karyawan->divisi->nama_divisi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Jabatan</th>
                                <td>{{ $karyawan->jabatan }}</td>
                            </tr>
                            <tr>
                                <th>No HP</th>
                                <td>{{ $karyawan->no_hp }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $karyawan->email }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Masuk</th>
                                <td>{{ \Carbon\Carbon::parse($karyawan->tanggal_masuk)->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Status Kerja</th>
                                <td>
                                    <span
                                        class="badge 
                                    {{ $karyawan->status_kerja == 'TETAP'
                                        ? 'bg-success'
                                        : ($karyawan->status_kerja == 'KONTRAK'
                                            ? 'bg-warning'
                                            : 'bg-info') }}">
                                        {{ $karyawan->status_kerja }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td>{{ $karyawan->alamat ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Dibuat</th>
                                <td>{{ $karyawan->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Terakhir Update</th>
                                <td>{{ $karyawan->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <!-- AKUN LOGIN KARYAWAN -->
        <div class="card mt-3">
            <div class="card-header">
                <h5>Akun Login Karyawan</h5>
            </div>
            <div class="card-body">
                @if ($karyawan->user)
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Email</th>
                            <td>{{ $karyawan->user->email }}</td>
                        </tr>
                        <tr>
                            <th>Password Default</th>
                            <td><code>12345678</code> (harap ganti setelah login pertama)</td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td>{{ $karyawan->user->role }}</td>
                        </tr>
                        <tr>
                            <th>Status Akun</th>
                            <td>
                                <span class="badge {{ $karyawan->user->status == 'AKTIF' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $karyawan->user->status }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Terakhir Login</th>
                            <td>{{ $karyawan->user->last_login ? \Carbon\Carbon::parse($karyawan->user->last_login)->format('d M Y H:i') : '-' }}
                            </td>
                        </tr>
                    </table>
                @else
                    <p class="text-muted">Akun user belum dibuat.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
