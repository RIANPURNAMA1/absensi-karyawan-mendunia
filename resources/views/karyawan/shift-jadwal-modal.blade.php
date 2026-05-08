<!-- MODAL ATUR SHIFT JADWAL PER TANGGAL -->
<div class="modal fade" id="modalAturShift" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0" style="">
                <h5 class="modal-title fw-bold">
                    <i class="ph ph-calendar-plus me-2"></i>Atur Shift Jadwal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Karyawan</label>
                    <select id="selectKaryawanShift" class="form-select select2">
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawan as $k)
                            <option value="{{ $k->id }}">{{ $k->name }} ({{ $k->nip }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Bulan & Tahun</label>
                    <div class="row">
                        <div class="col-md-6">
                            <select id="bulanShift" class="form-select">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $i == now()->month ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->format('F') }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select id="tahunShift" class="form-select">
                                @for($i = now()->year - 1; $i <= now()->year + 1; $i++)
                                    <option value="{{ $i }}" {{ $i == now()->year ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>

                <hr>

                <div id="calendarContainer" class="d-none">
                    <div class="calendar-header d-flex justify-content-between align-items-center mb-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="changeMonth(-1)">
                            <i class="ph ph-caret-left"></i>
                        </button>
                        <h6 class="fw-bold mb-0" id="calendarTitle"></h6>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="changeMonth(1)">
                            <i class="ph ph-caret-right"></i>
                        </button>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="fw-bold text-muted">PILIH TANGGAL:</small>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="pilihSemuaTanggal()">Pilih Semua</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSemuaTanggal()">Clear</button>
                            </div>
                        </div>
                        <div id="selectedDatesBadge" class="d-flex flex-wrap gap-1"></div>
                    </div>

                    <div class="calendar-grid mb-3">
                        <div class="row row-cols-7 g-1 text-center fw-bold mb-2">
                            <div class="col py-1 text-danger">Min</div>
                            <div class="col py-1">Sen</div>
                            <div class="col py-1">Sel</div>
                            <div class="col py-1">Rab</div>
                            <div class="col py-1">Kam</div>
                            <div class="col py-1">Jum</div>
                            <div class="col py-1 text-primary">Sab</div>
                        </div>
                        <div class="calendar-days" id="calendarDays"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">PILIH SHIFT:</label>
                        <select id="pilihShiftBatch" class="form-select">
                            <option value="">-- Pilih Shift --</option>
                            @foreach($shifts as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_shift }} ({{ \Carbon\Carbon::parse($s->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->jam_pulang)->format('H:i') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <input type="text" id="keteranganShiftBatch" class="form-control" placeholder="Keterangan (opsional)">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-danger flex-grow-1" onclick="hapusJadwalBatch()" disabled id="btnHapusBatch">
                            <i class="ph ph-trash me-1"></i> Hapus Jadwal
                        </button>
                        <button type="button" class="btn btn-primary flex-grow-1" onclick="simpanJadwalBatch()" id="btnSimpanBatch">
                            <i class="ph ph-floppy-disk me-1"></i> Simpan Jadwal
                        </button>
                    </div>

                    <div class="legend-box p-3 bg-light rounded mt-3">
                        <small class="fw-bold">Legenda Shift:</small>
                        <div class="d-flex flex-wrap gap-2 mt-2" id="shiftLegend"></div>
                    </div>
                </div>

                <div id="jadwalKosong" class="text-center py-5 text-muted">
                    <i class="ph ph-calendar-blank fs-1"></i>
                    <p class="mt-2">Pilih karyawan untuk melihat calendar jadwal shift</p>
                </div>
            </div>

            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
let jadwalShiftData = {};
let currentMonth = parseInt('{{ now()->month }}');
let currentYear = parseInt('{{ now()->year }}');
let selectedTanggalList = [];
let allShifts = @json($shifts);

const shiftColors = {
    1: 'bg-success',
    2: 'bg-info', 
    3: 'bg-warning',
    4: 'bg-danger',
    5: 'bg-primary',
    6: 'bg-secondary',
    7: 'bg-dark'
};

$('#selectKaryawanShift, #bulanShift, #tahunShift').on('change', function() {
    loadJadwalShift();
});

function loadJadwalShift() {
    const userId = $('#selectKaryawanShift').val();
    const bulan = $('#bulanShift').val();
    const tahun = $('#tahunShift').val();

    if (!userId) {
        $('#calendarContainer').addClass('d-none');
        $('#jadwalKosong').removeClass('d-none');
        return;
    }

    currentMonth = parseInt(bulan);
    currentYear = parseInt(tahun);

    $.ajax({
        url: `/shift-jadwal/${userId}?bulan=${bulan}&tahun=${tahun}`,
        type: 'GET',
        success: function(res) {
            if (res.success) {
                jadwalShiftData = res.jadwals;
                selectedTanggalList = [];
                renderCalendar();
                renderLegend();
                updateSelectedDatesDisplay();
                $('#calendarContainer').removeClass('d-none');
                $('#jadwalKosong').addClass('d-none');
            }
        }
    });
}

function renderCalendar() {
    const bulanNama = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $('#calendarTitle').text(`${bulanNama[currentMonth - 1]} ${currentYear}`);

    const firstDay = new Date(currentYear, currentMonth - 1, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();

    const container = $('#calendarDays');
    container.empty();
    container.addClass('row row-cols-7 g-1');

    let startDay = firstDay;
    let currentWeek = $('<div class="row g-1 w-100"></div>');
    for (let i = 0; i < startDay; i++) {
        currentWeek.append('<div class="col"></div>');
    }

    const today = new Date();
    const isCurrentMonth = today.getFullYear() === currentYear && today.getMonth() + 1 === currentMonth;

    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const jadwal = jadwalShiftData[dateStr];
        const shift = jadwal?.shift;
        const shiftId = shift?.id;
        const isSelected = selectedTanggalList.includes(dateStr);

        let bgClass = 'bg-light';
        let textClass = 'text-dark';
        let borderClass = '';

        if (isCurrentMonth && day === today.getDate()) {
            borderClass = 'border border-2 border-primary';
        }

        if (isSelected) {
            borderClass = 'border border-3 border-primary';
            bgClass = 'bg-primary';
            textClass = 'text-white';
        } else if (shiftId && shiftColors[shiftId]) {
            bgClass = shiftColors[shiftId];
            textClass = 'text-white';
        }

        const cell = `
            <div class="col py-1">
                <div class="calendar-day ${bgClass} ${textClass} ${borderClass} rounded p-1 text-center h-100 cursor-pointer"
                     onclick="toggleTanggal('${dateStr}')"
                     style="min-height: 40px; cursor: pointer; font-size: 0.75rem;">
                    <div class="fw-bold">${day}</div>
                    ${shift && !isSelected ? `<div>${shift.nama_shift}</div>` : ''}
                    ${isSelected ? '<div><i class="ph ph-check"></i></div>' : ''}
                </div>
            </div>
        `;
        currentWeek.append(cell);

        const dayOfWeek = new Date(currentYear, currentMonth - 1, day).getDay();
        if (dayOfWeek === 6 || day === daysInMonth) {
            container.append(currentWeek);
            if (day !== daysInMonth) {
                currentWeek = $('<div class="row g-1 w-100"></div>');
            }
        }
    }
}

function renderLegend() {
    const legend = $('#shiftLegend');
    legend.empty();
    
    allShifts.forEach(function(shift) {
        const color = shiftColors[shift.id] || 'bg-secondary';
        legend.append(`
            <span class="badge ${color} text-white">
                ${shift.nama_shift} (${shift.jam_masuk.substring(0,5)}-${shift.jam_pulang.substring(0,5)})
            </span>
        `);
    });
}

function toggleTanggal(tanggal) {
    const index = selectedTanggalList.indexOf(tanggal);
    if (index > -1) {
        selectedTanggalList.splice(index, 1);
    } else {
        selectedTanggalList.push(tanggal);
    }
    renderCalendar();
    updateSelectedDatesDisplay();
}

function pilihSemuaTanggal() {
    selectedTanggalList = [];
    for (let day = 1; day <= new Date(currentYear, currentMonth, 0).getDate(); day++) {
        const dateStr = `${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        selectedTanggalList.push(dateStr);
    }
    renderCalendar();
    updateSelectedDatesDisplay();
}

function clearSemuaTanggal() {
    selectedTanggalList = [];
    renderCalendar();
    updateSelectedDatesDisplay();
}

function updateSelectedDatesDisplay() {
    const container = $('#selectedDatesBadge');
    container.empty();
    
    if (selectedTanggalList.length === 0) {
        container.append('<small class="text-muted">Belum ada tanggal dipilih</small>');
        $('#btnSimpanBatch').prop('disabled', true);
        $('#btnHapusBatch').prop('disabled', true);
    } else {
        selectedTanggalList.forEach(function(tgl) {
            container.append(`<span class="badge bg-primary">${tgl}</span>`);
        });
        $('#btnSimpanBatch').prop('disabled', false);
        $('#btnHapusBatch').prop('disabled', false);
    }
}

function changeMonth(delta) {
    currentMonth += delta;
    if (currentMonth > 12) {
        currentMonth = 1;
        currentYear++;
    } else if (currentMonth < 1) {
        currentMonth = 12;
        currentYear--;
    }
    $('#bulanShift').val(currentMonth);
    $('#tahunShift').val(currentYear);
    selectedTanggalList = [];
    loadJadwalShift();
}

function simpanJadwalBatch() {
    const userId = $('#selectKaryawanShift').val();
    const shiftId = $('#pilihShiftBatch').val();
    const keterangan = $('#keteranganShiftBatch').val();

    if (!userId || !shiftId) {
        Swal.fire({ icon: 'warning', title: 'Pilih Shift', text: 'Silakan pilih shift terlebih dahulu' });
        return;
    }

    if (selectedTanggalList.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Pilih Tanggal', text: 'Silakan pilih minimal 1 tanggal' });
        return;
    }

    $.ajax({
        url: '{{ route("shift-jadwal.multiple") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            user_id: userId,
            shift_id: shiftId,
            tanggal_list: selectedTanggalList,
            keterangan: keterangan
        },
        success: function(res) {
            Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message });
            selectedTanggalList = [];
            $('#pilihShiftBatch').val('');
            $('#keteranganShiftBatch').val('');
            loadJadwalShift();
        },
        error: function(xhr) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: xhr.responseJSON?.message || 'Terjadi kesalahan' });
        }
    });
}

function hapusJadwalBatch() {
    const userId = $('#selectKaryawanShift').val();

    if (selectedTanggalList.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Pilih Tanggal', text: 'Silakan pilih minimal 1 tanggal' });
        return;
    }

    const datesToDelete = selectedTanggalList.filter(tgl => jadwalShiftData[tgl]?.id);
    if (datesToDelete.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Tidak Ada Jadwal', text: 'Tidak ada jadwal shift di tanggal yang dipilih' });
        return;
    }

    Swal.fire({
        title: 'Hapus Jadwal?',
        text: ` Akan menghapus ${datesToDelete.length} jadwal shift`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            let deleted = 0;
            datesToDelete.forEach(function(tgl) {
                const jadwal = jadwalShiftData[tgl];
                $.ajax({
                    url: `/shift-jadwal/${jadwal.id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    async: false,
                    success: function() {
                        deleted++;
                    }
                });
            });

            setTimeout(function() {
                Swal.fire({ icon: 'success', title: 'Berhasil', text: `${deleted} jadwal dihapus` });
                selectedTanggalList = [];
                loadJadwalShift();
            }, 100);
        }
    });
}
</script>