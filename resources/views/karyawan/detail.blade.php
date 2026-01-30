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
                            ? asset('uploads/foto_profil/' . $karyawan->foto_profil)
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
                                <td>{{ $karyawan->nip ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Divisi</th>
                                <td>{{ $karyawan->divisi->nama_divisi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Jabatan</th>
                                <td>{{ $karyawan->jabatan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Cabang</th>
                                <td>{{ $karyawan->cabang->nama_cabang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>No HP</th>
                                <td>{{ $karyawan->no_hp ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $karyawan->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tempat, Tanggal Lahir</th>
                                <td>{{ $karyawan->tempat_lahir ?? '-' }},
                                    {{ $karyawan->tanggal_lahir ? \Carbon\Carbon::parse($karyawan->tanggal_lahir)->format('d M Y') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Jenis Kelamin</th>
                                <td>{{ $karyawan->jenis_kelamin == 'L' ? 'Laki-laki' : ($karyawan->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}
                                </td>
                            </tr>
                            <tr>
                                <th>Agama</th>
                                <td>{{ $karyawan->agama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status Pernikahan</th>
                                <td>{{ $karyawan->status_pernikahan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Masuk</th>
                                <td>{{ $karyawan->tanggal_masuk ? \Carbon\Carbon::parse($karyawan->tanggal_masuk)->format('d M Y') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Status Kerja</th>
                                <td>
                                    <span
                                        class="badge 
                                    {{ $karyawan->status_kerja == 'TETAP' ? 'bg-success' : ($karyawan->status_kerja == 'KONTRAK' ? 'bg-warning text-dark' : 'bg-info') }}">
                                        {{ $karyawan->status_kerja ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td>{{ $karyawan->alamat ?? '-' }}</td>
                            </tr>

                            <!-- FILES -->
                            <tr>
                                <th>Foto KTP</th>
                                <td>
                                    @if ($karyawan->foto_ktp && file_exists(public_path('uploads/foto_ktp/' . $karyawan->foto_ktp)))
                                        <a href="{{ asset('uploads/foto_ktp/' . $karyawan->foto_ktp) }}"
                                            target="_blank">Lihat File</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Foto Ijazah</th>
                                <td>
                                    @if ($karyawan->foto_ijazah && file_exists(public_path('uploads/foto_ijazah/' . $karyawan->foto_ijazah)))
                                        <a href="{{ asset('uploads/foto_ijazah/' . $karyawan->foto_ijazah) }}"
                                            target="_blank">Lihat File</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Foto KK</th>
                                <td>
                                    @if ($karyawan->foto_kk && file_exists(public_path('uploads/foto_kk/' . $karyawan->foto_kk)))
                                        <a href="{{ asset('uploads/foto_kk/' . $karyawan->foto_kk) }}"
                                            target="_blank">Lihat File</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>CV</th>
                                <td>
                                    @if ($karyawan->cv_file && file_exists(public_path('uploads/cv_file/' . $karyawan->cv_file)))
                                        <a href="{{ asset('uploads/cv_file/' . $karyawan->cv_file) }}"
                                            target="_blank">Lihat File</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Sertifikat</th>
                                <td>
                                    @if ($karyawan->sertifikat_file && file_exists(public_path('uploads/sertifikat_file/' . $karyawan->sertifikat_file)))
                                        <a href="{{ asset('uploads/sertifikat_file/' . $karyawan->sertifikat_file) }}"
                                            target="_blank">Lihat File</a>
                                    @else
                                        -
                                    @endif
                                </td>
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

                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Email</th>
                        <td>{{ $karyawan->email }}</td>
                    </tr>
                    <tr>
                        <th>Password Default</th>
                        <td><code>12345678</code> (harap ganti setelah login pertama)</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>{{ $karyawan->role }}</td>
                    </tr>
                    <tr>
                        <th>Status Akun</th>
                        <td>
                            <span class="badge {{ $karyawan->status == 'AKTIF' ? 'bg-success' : 'bg-danger' }}">
                                {{ $karyawan->status }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Terakhir Login</th>
                        <td>{{ $karyawan->last_login ? \Carbon\Carbon::parse($karyawan->last_login)->format('d M Y H:i') : '-' }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
@endsection
