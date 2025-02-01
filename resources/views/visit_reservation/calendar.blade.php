@extends('layouts.app')

@section('content')
    <!-- Success Message -->
    <div id="success-message" class="alert alert-success d-none" role="alert">
        Data Jadwal Gedung berhasil di simpan!
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="mb-0">Jadwal Reservasi Kunjungan</h1>
        <!-- Filter, Sort, and Search Form -->
    <form id="filter-form" class="mb-4">
        <div class="col-md-12 mt-3 justify-content-end">
            <div class="row align-items-center g-2">
                <!-- Group: Nama Gedung -->
                <div class="col-auto ms-auto">
                    <label for="gedung_id" class="form-label mb-0"><strong>Nama Gedung</strong></label>
                </div>
                <div class="col-auto">
                    <select name="gedung_id" id="gedung_id" class="form-select form-select-sm">
                        <option value="" selected>Pilih Gedung</option>
                            @foreach($buildings as $building)
                                <option value="{{ $building->id }}">{{ $building->name }}</option>
                            @endforeach
                    </select>
                </div>
            </div>
        </div>
    </form>
    </div>

    <div id="calendar"></div>

    <!-- Modal for Create/Edit Schedule Form -->
    <div class="modal fade" id="buildingScheduleModal" tabindex="-1" aria-labelledby="buildingScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="buildingScheduleModalLabel">Form Jadwal Gedung</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="buildingSchedule-form">
                        @csrf
                        <input type="hidden" name="id" id="buildingSchedule-id">
                        <input type="hidden" name="visit_id" id="buildingSchedule-visit_id">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" id="buildingSchedule-tanggal" name="tanggal" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Waktu Mulai</label>
                            <input type="time" id="buildingSchedule-start_time" name="start_time" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_time" class="form-label">Waktu Selesai</label>
                            <input type="time" id="buildingSchedule-end_time" name="end_time" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="visitor_company" class="form-label">Nama Instansi Pengunjung</label>
                            <input type="text" id="buildingSchedule-company" name ="visitor_company" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="visitor_name" class="form-label">Nama Pengunjung</label>
                            <input type="text" id="buildingSchedule-visitor_name" name ="visitor_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="visitor_purphose" class="form-label">Tujuan Kunjungan</label>
                            <input type="text" id="buildingSchedule-purphose" name ="visitor_purphose" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="visitor_contact" class="form-label">Kontak</label>
                            <input type="text" id="buildingSchedule-contact" name ="visitor_contact" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="visitor_person" class="form-label">Jumlah Pengunjung</label>
                            <input type="number" id="buildingSchedule-person" name ="visitor_person" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="visitor_jumlah_kendaraan" class="form-label">Jumlah Kendaraan</label>
                            <input type="text" id="buildingSchedule-jumlah_kendaraan" name ="visitor_jumlah_kendaraan" class="form-control" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    <button type="button" class="btn btn-primary" id="save-user">
                        <i class="bi bi-save"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Actions -->
<div class="modal fade" id="calendarActionsModal" tabindex="-1" aria-labelledby="calendarActionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 80%; width: auto;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="calendarActionsModalLabel">Aksi pada Tanggal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center">
                    <p id="selectedDateText" class="m-0"></p>
                    @if (auth()->user()->role === 'admin' or auth()->user()->role === 'building')
                        <button class="btn btn-primary btn-sm" id="open-create-form">
                            <i class="bi bi-plus-lg" style="font-size: 1rem;"></i>
                        </button>
                    @endif
                </div>
                <div id="actionButtons" class="d-flex flex-column gap-2">
                    <!-- Buttons will be dynamically added here -->
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        fetchbuildingSchedules();
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    function fetchbuildingSchedules() {
        let calendarEl = document.getElementById('calendar');
        let eventsData = []; // Variable to store events data
        let gedung_id = document.getElementById('gedung_id').value;

        let calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id', // Bahasa Indonesia
            height: 600, // Tinggi tetap
            aspectRatio: 1.5, // Rasio tinggi/lebar
            events: function (fetchInfo, successCallback, failureCallback) {
                fetch(`/api/reservation-schedules?start_date=${fetchInfo.startStr}&end_date=${fetchInfo.endStr}&gedung_id=${gedung_id}`)
                    .then(response => response.json())
                    .then(data => {
                        eventsData = data; // Save events data to global variable
                        successCallback(data); // Return the events to FullCalendar
                    })
                    .catch(error => {
                        console.error('Error fetching schedules:', error);
                        failureCallback(error);
                    });
            },
            selectable: true,
            dateClick: function (info) {
                let selectedDate = info.dateStr;  // Selected date from the calendar
                let today = new Date().toISOString().split('T')[0];  // Current date in 'YYYY-MM-DD' format

                // Check if the selected date is in the past
                if (selectedDate < today) {
                    alert('Tanggal ini sudah lewat dan tidak bisa dipilih!');
                    return; // Don't allow action for past dates
                }

                // Ensure the selected date is in the same format as the event start date (YYYY-MM-DD)
                let filteredEvents = eventsData.filter(event => {
                    // Extract date from the start field (YYYY-MM-DD)
                    let eventDate = event.start.split('T')[0]; // Extract only the date part (YYYY-MM-DD)
                    return eventDate === selectedDate;
                });

                // If no events exist for this date, show the create schedule modal
                if (filteredEvents.length === 0) {
                    let modal = new bootstrap.Modal(document.getElementById('buildingScheduleModal'));
                    $('#buildingScheduleModalLabel').text('Booking Jadwal Gedung');
                    $('#buildingSchedule-form')[0].reset(); // Clear form
                    $('#buildingSchedule-id').val(''); // Clear hidden id field
                    $('#buildingSchedule-tanggal').val(selectedDate);
                    $('#buildingSchedule-building_id').val(gedung_id);
                    $('#buildingScheduleModal').modal('show');
                } else {
                    // If there are events, show the calendar actions modal
                    let modal = new bootstrap.Modal(document.getElementById('calendarActionsModal'));
                    document.getElementById('selectedDateText').textContent = `Tanggal yang dipilih: ${selectedDate}`;
                    loadButtons(selectedDate, filteredEvents);
                    modal.show();
                }
            },
            eventClick: function (info) {
                        alert('Event: ' + info.event.title);
                    },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            dayCellClassNames: function (arg) {
                if (arg.date) {
                    let date = arg.date.toISOString().split('T')[0]; // Ambil tanggal dalam format YYYY-MM-DD

                    let today = new Date(); // Ambil tanggal hari ini sebagai objek Date
                    today.setHours(0, 0, 0, 0); // Set jam, menit, detik, dan milidetik ke 0 (menjadi tengah malam)
                    let todayStr = today.toISOString().split('T')[0]; // Ambil tanggal hari ini dalam format YYYY-MM-DD

                    // Periksa tanggal dan kembalikan kelas yang sesuai
                    if (date === todayStr) {
                        return ['today-date'];
                    } else if (date < todayStr) {
                        return ['past-date'];
                    } else {
                        return [];
                    }
                } else {
                    console.error("Tanggal tidak ditemukan di argumen.");
                    return []; // Kembalikan kelas kosong jika `arg.date` tidak ada
                }
            },
            dayCellDidMount: function (info) {
                // You can apply additional styling here if needed
            }
        });

        calendar.render();
    }

    // Function to load buttons in the calendar actions modal
    function loadButtons(selectedDate, scheduleData) {
        const actionButtons = document.getElementById('actionButtons');
        actionButtons.innerHTML = ''; // Clear previous buttons

        if (scheduleData.length === 0) {
            actionButtons.innerHTML = `<p class="text-muted">Tidak ada jadwal untuk tanggal ini.</p>`;
            return;
        }

        // Create table header
        let tableHTML = `
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Gedung</th>
                        <th>Jam</th>
                        <th>Kegiatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
        `;

        scheduleData.forEach(schedule => {
            // Build table row for each schedule
            let rowHTML = `
                <tr>
                    <td>${schedule.title}</td>
                    <td>${schedule.start_time} - ${schedule.end_time}</td>
                    <td>${schedule.kegiatan}</td>
                    <td>
                        <button class="btn btn-primary btn-sm booking"
                            data-id="${schedule.id}"
                            data-status="${schedule.is_booked  ? 'Batalkan Reservasi' : 'Reservasi' }"
                            data-bs-toggle="tooltip" title="${schedule.is_booked  ? 'Batalkan Reservasi' : 'Reservasi' }" hidden>
                            <i class="bi bi-calendar-x"></i>
                        </button>
                        <button class="btn btn-warning btn-sm edit-buildingSchedule"
                            data-id="${schedule.id}"
                            data-building_id="${schedule.building_id }"
                            data-tanggal="${schedule.tanggal }"
                            data-start_time="${schedule.start_time }"
                            data-end_time="${schedule.end_time }"
                            data-purphose="${schedule.kegiatan }"
                            data-person="${schedule.peserta }"
                            data-visit_id="${schedule.visit_id }"
                            data-company="${schedule.company }"
                            data-visitor_name="${schedule.visitor_name }"
                            data-contact="${schedule.contact }"
                            data-jumlah_kendaraan="${schedule.jumlah_kendaraan }"
                            data-bs-toggle="tooltip" title="Ubah"
                            ${!schedule.is_owner  ? 'hidden' : '' }>
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-danger btn-sm delete-buildingSchedule"
                            data-id="${schedule.id}"
                            data-name="${schedule.title}"
                            data-bs-toggle="tooltip" title="Batakan Reservasi"
                            ${!schedule.is_owner  ? 'hidden' : '' }>
                            <i class="bi bi-calendar-x"></i>
                        </button>
                        <button class="btn btn-sm ${schedule.is_available ? 'btn-secondary' : 'btn-success'} toggleStatus"
                            data-id="${schedule.id}"
                            data-status="${schedule.is_available  ? 'Tidak' : 'Ya' }"
                            data-bs-toggle="tooltip" title="${schedule.is_available  ? 'Tidak tersedia' : 'Tersedia' }"
                            ${schedule.is_booked  ? 'disabled' : '' } hidden>
                            <i class="bi ${schedule.is_available ? 'bi-toggle-on' : 'bi-toggle-off'}"></i>
                        </button>
                    </td>
                </tr>
            `;

            // Append row to the table
            tableHTML += rowHTML;
        });

        // Close the table
        tableHTML += `
                </tbody>
            </table>
        `;

        // Add the table to the actionButtons element
        actionButtons.innerHTML = tableHTML;
    }

    // Event listener for changes in form inputs
    $('#filter-form').on('change', 'select', function() {
            fetchbuildingSchedules();
        });

    // Open the Create Schedule form
    $('#open-create-form').on('click', function() {
        let selectedDate = $('#selectedDateText').text().split(': ')[1]; // Ambil tanggal yang dipilih dari text modal calendarActionsModal
        $('#buildingScheduleModalLabel').text('Booking Jadwal Gedung');
        $('#buildingSchedule-form')[0].reset(); // Clear form
        $('#buildingSchedule-id').val(''); // Clear hidden id field

        // Set tanggal yang dipilih pada input tanggal
        $('#buildingSchedule-tanggal').val(selectedDate); // Set tanggal pada form

        // Close the calendar actions modal
        $('#calendarActionsModal').modal('hide');

        // Open the create schedule modal
        var buildingScheduleModal = new bootstrap.Modal(document.getElementById('buildingScheduleModal'));
        buildingScheduleModal.show();
    });

    // Open the Edit Schedule form
    $(document).on('click', '.edit-buildingSchedule', function() {
        const buildingScheduleId = $(this).data('id');
        const building_id = $(this).data('building_id');
        const tanggal = $(this).data('tanggal');
        const start_time = $(this).data('start_time');
        const end_time = $(this).data('end_time');
        const buildingScheduleStatus = $(this).data('status');
        const kegiatan = $(this).data('purphose');
        const peserta = $(this).data('person');
        const visit_id = $(this).data('visit_id');
        const company = $(this).data('company');
        const visitor_name = $(this).data('visitor_name');
        const contact = $(this).data('contact');
        const jumlah_kendaraan = $(this).data('jumlah_kendaraan');

        $('#buildingScheduleModalLabel').text('Ubah Jadwal Gedung');
        $('#buildingSchedule-id').val(buildingScheduleId);
        $('#buildingSchedule-building_id').val(building_id);
        $('#buildingSchedule-tanggal').val(tanggal);
        $('#buildingSchedule-start_time').val(start_time);
        $('#buildingSchedule-end_time').val(end_time);
        $('#buildingSchedule-status').val(buildingScheduleStatus);
        $('#buildingSchedule-purphose').val(kegiatan);
        $('#buildingSchedule-person').val(peserta);
        $('#buildingSchedule-visit_id').val(visit_id);
        $('#buildingSchedule-company').val(company);
        $('#buildingSchedule-visitor_name').val(visitor_name);
        $('#buildingSchedule-contact').val(contact);
        $('#buildingSchedule-jumlah_kendaraan').val(jumlah_kendaraan);

        // Close the calendar actions modal
        $('#calendarActionsModal').modal('hide');

        // Open the create schedule modal
        var buildingScheduleModal = new bootstrap.Modal(document.getElementById('buildingScheduleModal'));
        buildingScheduleModal.show();
    });

    // Save or update the user
    $('#save-user').on('click', function() {
        const formData = $('#buildingSchedule-form').serialize();
        const buildingScheduleId = $('#buildingSchedule-id').val();

        const url = buildingScheduleId ? `/visitReservations/${buildingScheduleId}` : '/visitReservations';
        const method = buildingScheduleId ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                // Show success message
                // $('#success-message').removeClass('d-none').text('Data Jadwal Gedung Berhasil Di Simpan!');
                $('#success-message').removeClass('d-none').text(response.success);

                // Hide success message after 3 seconds
                setTimeout(function() {
                    $('#success-message').addClass('d-none');
                }, 3000);

                $('#buildingScheduleModal').modal('hide'); // Close the modal
                fetchbuildingSchedules();  // Reload the table after saving
            },
            error: function(xhr) {
                alert(xhr.responseJSON.error || 'Terjadi kesalahan');
            }
        });
    });

    // Event listener for the delete button
    $(document).on('click', '.delete-buildingSchedule', function() {
        const buildingScheduleId = $(this).data('id');
        const buildingScheduleTrxNo = $(this).data('name');

        // Confirm before deletion
        if (confirm(`Batalkan Reservasi "${buildingScheduleTrxNo}"?`)) {
            $.ajax({
                url: `/visitReservations/${buildingScheduleId}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                },
                success: function(response) {
                    // Show success message
                    // $('#success-message').removeClass('d-none').text('Data Jadwal Gedung berhasil di hapus!');
                    $('#success-message').removeClass('d-none').text(response.success);

                    // Close the calendar actions modal
                    $('#calendarActionsModal').modal('hide');

                    // Hide success message after 3 seconds
                    setTimeout(function() {
                        $('#success-message').addClass('d-none');
                    }, 3000);

                    fetchbuildingSchedules(); // Reload the table after deletion
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.error || 'Terjadi kesalahan');
                }
            });
        }
    });

    // Event listener for the toggleStatus button
    $(document).on('click', '.toggleStatus', function() {
        const buildingScheduleId = $(this).data('id');
        const buildingScheduleStatus = $(this).data('status');

        // Confirm before deletion
        if (confirm(`Ubah Ketersediaan Menjadi "${buildingScheduleStatus}" ?`)) {
            $.ajax({
                url: `/buildingSchedules/${buildingScheduleId}/toggleStatus`,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                },
                success: function(response) {
                    // Show success message
                    // $('#success-message').removeClass('d-none').text(`Jadwal Gedung "${buildingScheduleStatus}" Berhasil!`);
                    $('#success-message').removeClass('d-none').text(response.success);

                    // Close the calendar actions modal
                    $('#calendarActionsModal').modal('hide');

                    // Hide success message after 3 seconds
                    setTimeout(function() {
                        $('#success-message').addClass('d-none');
                    }, 3000);

                    fetchbuildingSchedules(); // Reload the table after deletion
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.error || 'Terjadi kesalahan');
                }
            });
        }
    });

    // Event listener for the booking button
    $(document).on('click', '.booking', function() {
        const buildingScheduleId = $(this).data('id');
        const buildingScheduleStatus = $(this).data('status');
        const buildingScheduleBooked = $(this).data('booked');

        // Confirm before deletion
        if (confirm(`"${buildingScheduleStatus}" ?`)) {
            $.ajax({
                url: `/buildingSchedules/${buildingScheduleId}/bookingGedung`,
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                },
                success: function(response) {
                    // Show success message
                    // $('#success-message').removeClass('d-none').text(`Jadwal Gedung "${buildingScheduleStatus}" Berhasil!`);
                    $('#success-message').removeClass('d-none').text(response.success);

                    // Close the calendar actions modal
                    $('#calendarActionsModal').modal('hide');

                    // Hide success message after 3 seconds
                    setTimeout(function() {
                        $('#success-message').addClass('d-none');
                    }, 3000);

                    fetchbuildingSchedules(); // Reload the table after deletion
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.error || 'Terjadi kesalahan');
                }
            });
        }
    });

</script>
@endpush
