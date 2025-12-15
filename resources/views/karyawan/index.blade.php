<table class="table table-bordered" id="karyawanTable">
    <thead>
        <tr>
            <th>NIP</th>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Departemen</th>
            <th>No HP</th>
            <th>Status Kerja</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($karyawan as $k)
        <tr>
            <td>{{ $k->nip }}</td>
            <td>{{ $k->user->name }}</td>
            <td>{{ $k->jabatan }}</td>
            <td>{{ $k->departemen }}</td>
            <td>{{ $k->no_hp }}</td>
            <td>{{ $k->status_kerja }}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editKaryawan({{ $k->id }})">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteKaryawan({{ $k->id }})">Hapus</button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
