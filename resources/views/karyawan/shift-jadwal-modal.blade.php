<!-- MODAL ATUR SHIFT JADWAL PER TANGGAL -->
<div class="modal fade" id="modalAturShift" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title text-white fw-bold">
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

                    <div class="legend-box p-3 bg-light rounded mb-3">
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

<!-- MODAL PILIH SHIFT -->
<div class="modal fade" id="modalPilihShift" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 bg-primary text-white">
                <h6 class="modal-title fw-bold">Pilih Shift</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <p class="mb-2"><strong>Tanggal:</strong> <span id="tanggalDipilih"></span></p>
                <select id="pilihShiftModal" class="form-select mb-3">
                    <option value="">-- Pilih Shift --</option>
                    @foreach($shifts as $s)
                        <option value="{{ $s->id }}">{{ $s->nama_shift }} ({{ \Carbon\Carbon::parse($s->jam_masuk)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->jam_pulang)->format('H:i') }})</option>
                    @endforeach
                </select>
                <input type="text" id="keteranganShiftModal" class="form-control" placeholder="Keterangan (opsional)">
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-danger" onclick="hapusJadwalTanggal()">
                    <i class="ph ph-trash me-1"></i> Hapus
                </button>
                <button type="button" class="btn btn-primary" onclick="simpanJadwalTanggal()">
                    <i class="ph ph-floppy-disk me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let jadwalShiftData = {};
let currentMonth = parseInt('{{ now()->month }}');
let currentYear = parseInt('{{ now()->year }}');
let selectedTanggal = null;
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
                renderCalendar();
                renderLegend();
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

        let bgClass = 'bg-light';
        let textClass = 'text-dark';
        let borderClass = '';

        if (isCurrentMonth && day === today.getDate()) {
            borderClass = 'border border-2 border-primary';
        }

        if (shiftId && shiftColors[shiftId]) {
            bgClass = shiftColors[shiftId];
            textClass = 'text-white';
        }

        const cell = `
            <div class="col py-1">
                <div class="calendar-day ${bgClass} ${textClass} ${borderClass} rounded p-1 text-center h-100 cursor-pointer"
                     onclick="pilihTanggal('${dateStr}')"
                     style="min-height: 40px; cursor: pointer; font-size: 0.75rem;">
                    <div class="fw-bold">${day}</div>
                    ${shift ? `<div>${shift.nama_shift}</div>` : ''}
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
    loadJadwalShift();
}

function pilihTanggal(tanggal) {
    selectedTanggal = tanggal;
    const jadwal = jadwalShiftData[tanggal];
    
    $('#tanggalDipilih').text(tanggal);
    $('#pilihShiftModal').val(jadwal?.shift?.id || '');
    $('#keteranganShiftModal').val(jadwal?.keterangan || '');
    
    new bootstrap.Modal(document.getElementById('modalPilihShift')).show();
}

function simpanJadwalTanggal() {
    const userId = $('#selectKaryawanShift').val();
    const shiftId = $('#pilihShiftModal').val();
    const keterangan = $('#keteranganShiftModal').val();

    if (!userId || !shiftId) {
        Swal.fire({ icon: 'warning', title: 'Pilih Shift', text: 'Silakan pilih shift terlebih dahulu' });
        return;
    }

    $.ajax({
        url: '{{ route("shift-jadwal.store") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            user_id: userId,
            shift_id: shiftId,
            tanggal: selectedTanggal,
            keterangan: keterangan
        },
        success: function(res) {
            bootstrap.Modal.getInstance(document.getElementById('modalPilihShift')).hide();
            Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message });
            loadJadwalShift();
        },
        error: function(xhr) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: xhr.responseJSON?.message || 'Terjadi kesalahan' });
        }
    });
}

function hapusJadwalTanggal() {
    const userId = $('#selectKaryawanShift').val();
    
    const jadwal = jadwalShiftData[selectedTanggal];
    if (!jadwal?.id) {
        Swal.fire({ icon: 'warning', title: 'Tidak Ada Jadwal', text: 'Tidak ada jadwal shift di tanggal ini' });
        return;
    }

    $.ajax({
        url: `/shift-jadwal/${jadwal.id}`,
        type: 'DELETE',
        data: { _token: '{{ csrf_token() }}' },
        success: function(res) {
            bootstrap.Modal.getInstance(document.getElementById('modalPilihShift')).hide();
            Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message });
            loadJadwalShift();
        }
    });
}
</script>