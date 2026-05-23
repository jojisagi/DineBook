<?php
// guest/floormap.php — Interactive SVG floor map with zone filter + 30-min slots
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../auth/security.php';
$isGuest = ($_SESSION['role'] ?? '') === 'guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineBook — Floor Map</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
    <style>
        /* Floor map styles */
        .floor-container { background: #f0ece3; border-radius: 12px; padding: 20px; min-height: 420px; position: relative; }
        .zone-label { font-size: 1.1rem; font-weight: 600; color: var(--dinebook-red); text-transform: uppercase; letter-spacing: 1px; }

        /* SVG table nodes */
        .table-node { cursor: pointer; transition: transform 0.15s, filter 0.15s; }
        .table-node:hover { transform: scale(1.08); filter: drop-shadow(0 2px 6px rgba(0,0,0,0.3)); }
        .table-node.available rect, .table-node.available circle { fill: #28a745; }
        .table-node.occupied  rect, .table-node.occupied  circle { fill: #dc3545; }
        .table-node.selected  rect, .table-node.selected  circle { fill: var(--dinebook-gold); stroke: var(--dinebook-red); stroke-width: 3; }
        .table-node.maintenance rect, .table-node.maintenance circle { fill: #6c757d; }
        .table-node text { fill: white; font-size: 13px; font-weight: 700; pointer-events: none; }

        /* Slot grid */
        .slot-grid { display: flex; flex-wrap: wrap; gap: 6px; }
        .slot-btn { padding: 6px 14px; border-radius: 6px; font-size: 0.85rem; border: 2px solid transparent; cursor: pointer; transition: all 0.15s; }
        .slot-btn.free { background: #d4edda; color: #155724; border-color: #28a745; }
        .slot-btn.free:hover { background: #28a745; color: white; }
        .slot-btn.taken { background: #f8d7da; color: #721c24; border-color: #dc3545; }
        .slot-btn.pending { background: #fff3cd; color: #856404; border-color: #ffc107; }
        .slot-btn.chosen { background: var(--dinebook-gold); color: #333; border-color: var(--dinebook-red); font-weight: 700; }

        /* Booking detail card for admin */
        .booking-detail { background: #f8f9fa; border-radius: 8px; padding: 12px; margin-top: 10px; border-left: 4px solid var(--dinebook-red); }
        .booking-detail .label { font-weight: 600; color: #555; font-size: 0.85rem; }
        .booking-detail .value { color: #222; }

        /* Legend */
        .legend-dot { width: 16px; height: 16px; border-radius: 4px; display: inline-block; }

        /* Info panel */
        #table-info { display: none; }
        #table-info.show { display: block; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo $isGuest ? '/dinebook/guest/dashboard.php' : '/dinebook/index.php'; ?>">DineBook</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <?php if ($isGuest): ?>
                        <li class="nav-item"><a class="nav-link" href="/dinebook/guest/dashboard.php">Home</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/dinebook/index.php">Staff Dashboard</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link active" href="/dinebook/guest/floormap.php">Floor Map</a></li>
                    <li class="nav-item"><a class="nav-link" href="/dinebook/guest/my_reservations.php">My Reservations</a></li>
                    <li class="nav-item"><a class="nav-link" href="/dinebook/auth/logout.php">Logout</a></li>
                </ul>
                <span class="navbar-text ms-auto" style="color:var(--dinebook-gold);">
                    <?php echo htmlspecialchars($_SESSION['user']); ?>
                </span>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="page-header">Restaurant Floor Map</h2>

        <!-- Controls -->
        <div class="row mb-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold">Date</label>
                <input type="date" id="pick-date" class="form-control" value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Zone</label>
                <select id="pick-zone" class="form-select">
                    <option value="indoors">Indoors</option>
                    <option value="terrace">Terrace</option>
                    <option value="bar">Bar</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button id="btn-load" class="btn btn-primary w-100">Load Map</button>
            </div>
        </div>

        <!-- Legend -->
        <div class="d-flex gap-4 mb-3 flex-wrap">
            <span><span class="legend-dot" style="background:#28a745;"></span> Available</span>
            <span><span class="legend-dot" style="background:#dc3545;"></span> Occupied</span>
            <span><span class="legend-dot" style="background:var(--dinebook-gold);"></span> Selected</span>
            <span><span class="legend-dot" style="background:#ffc107;"></span> Pending</span>
            <span><span class="legend-dot" style="background:#6c757d;"></span> Maintenance</span>
        </div>

        <div class="row">
            <!-- SVG Map -->
            <div class="col-lg-8 mb-4">
                <div class="floor-container" id="floor-container">
                    <p class="text-center text-muted mt-5" id="map-placeholder">Select a date and zone, then click <strong>Load Map</strong>.</p>
                    <svg id="floor-svg" width="100%" height="400" viewBox="0 0 700 400" style="display:none;"></svg>
                </div>
            </div>

            <!-- Info Panel -->
            <div class="col-lg-4">
                <div class="card" id="table-info">
                    <div class="card-header" style="background:var(--dinebook-red); color:white;">
                        <strong>Table <span id="info-number"></span></strong>
                        <span class="float-end badge bg-light text-dark" id="info-capacity"></span>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Shape:</strong> <span id="info-shape"></span></p>
                        <p class="mb-2"><strong>Zone:</strong> <span id="info-zone"></span></p>
                        <hr>
                        <h6>Available Slots (30 min each)</h6>
                        <div class="slot-grid" id="slot-grid"></div>
                        <hr>
                        <form id="reserve-form" style="display:none;">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                            <input type="hidden" id="res-table-id" name="table_id">
                            <input type="hidden" id="res-date" name="date">
                            <input type="hidden" id="res-slot" name="time_slot">
                            <div class="mb-2">
                                <label class="form-label">Party size</label>
                                <input type="number" class="form-control" id="res-party" name="party_size" min="1" max="20" value="2" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Duration</label>
                                <select class="form-select" id="res-duration" name="duration" required>
                                    <option value="30">30 minutes</option>
                                    <option value="60" selected>1 hour</option>
                                    <option value="90">1 hour 30 min</option>
                                    <option value="120">2 hours</option>
                                    <option value="150">2 hours 30 min</option>
                                    <option value="180">3 hours</option>
                                </select>
                                <div class="form-text" id="slots-blocked-msg" style="color:var(--dinebook-red);"></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Special requests</label>
                                <textarea class="form-control" id="res-notes" name="notes" rows="2" maxlength="300" placeholder="Allergies, birthday, etc."></textarea>
                            </div>
                            <div class="alert alert-info py-2 px-3 mb-2" style="font-size:0.85rem;">
                                <strong>Note:</strong> Reservations have an estimated arrival window of <strong>&plusmn;15 minutes</strong>. Please arrive within that range to keep your table.
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mt-2"><?php echo $isGuest ? 'Request Reservation' : 'Confirm Reservation'; ?></button>
                        </form>
                        <div id="reserve-msg"></div>

                        <!-- Booking detail panel (staff/admin only) -->
                        <div id="booking-detail" class="booking-detail" style="display:none;">
                            <h6 class="mb-2" style="color:var(--dinebook-red);">Reservation Details</h6>
                            <p class="mb-1"><span class="label">Booking ID:</span> <span class="value" id="bd-id" style="font-family:monospace; font-size:0.8rem;"></span></p>
                            <p class="mb-1"><span class="label">Guest:</span> <span class="value" id="bd-user"></span></p>
                            <p class="mb-1"><span class="label">Email:</span> <span class="value" id="bd-email"></span></p>
                            <p class="mb-1"><span class="label">Party size:</span> <span class="value" id="bd-party"></span></p>
                            <p class="mb-1"><span class="label">Duration:</span> <span class="value" id="bd-duration"></span></p>
                            <p class="mb-1"><span class="label">Status:</span> <span id="bd-status"></span></p>
                            <p class="mb-1"><span class="label">Notes:</span> <span class="value" id="bd-notes"></span></p>
                            <div id="bd-actions" class="mt-2"></div>
                        </div>
                    </div>
                </div>

                <!-- No table selected message -->
                <div class="card" id="no-selection">
                    <div class="card-body text-center text-muted py-5">
                        <div style="font-size:3rem;">&#128072;</div>
                        <p>Click a <strong class="text-success">green table</strong> on the map to see availability and reserve.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer container"><p>DineBook &copy; 2026</p></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function() {
        const SVG_NS = 'http://www.w3.org/2000/svg';
        const IS_GUEST = <?php echo $isGuest ? 'true' : 'false'; ?>;
        const CSRF = '<?= htmlspecialchars(csrf_token()) ?>';
        let tablesData = [];
        let selectedTable = null;
        let selectedSlot = null;

        // --- Load map ---
        $('#btn-load').on('click', loadMap);
        $(document).ready(function() { loadMap(); });

        function loadMap() {
            const date = $('#pick-date').val();
            const zone = $('#pick-zone').val();
            if (!date || !zone) return;

            $('#map-placeholder').text('Loading...');
            $.getJSON('/dinebook/api/availability_slots.php', { date, zone })
                .done(function(data) {
                    tablesData = data;
                    renderSVG(data, zone);
                    selectedTable = null;
                    selectedSlot = null;
                    $('#table-info').removeClass('show');
                    $('#no-selection').show();
                    $('#reserve-form').hide();
                    $('#reserve-msg').html('');
                    $('#booking-detail').hide();
                })
                .fail(function() {
                    $('#map-placeholder').text('Error loading tables. Make sure the server is running.').show();
                });
        }

        // --- Render SVG ---
        function renderSVG(tables, zone) {
            const svg = document.getElementById('floor-svg');
            svg.innerHTML = '';

            if (tables.length === 0) {
                $('#map-placeholder').text('No tables found for this zone.').show();
                svg.style.display = 'none';
                return;
            }

            $('#map-placeholder').hide();
            svg.style.display = 'block';

            const bgText = document.createElementNS(SVG_NS, 'text');
            bgText.setAttribute('x', '350');
            bgText.setAttribute('y', '25');
            bgText.setAttribute('text-anchor', 'middle');
            bgText.setAttribute('fill', '#c0b090');
            bgText.setAttribute('font-size', '18');
            bgText.setAttribute('font-weight', '600');
            bgText.textContent = zone.toUpperCase() + ' ZONE';
            svg.appendChild(bgText);

            const cols = Math.ceil(Math.sqrt(tables.length));
            const cellW = 700 / (cols + 1);
            const cellH = 350 / (Math.ceil(tables.length / cols) + 1);

            tables.forEach(function(t, i) {
                const col = i % cols;
                const row = Math.floor(i / cols);
                const cx = cellW * (col + 1);
                const cy = cellH * (row + 1) + 30;

                const g = document.createElementNS(SVG_NS, 'g');
                g.classList.add('table-node');

                const allTaken = t.slots.every(s => !s.available);
                const status = t.status === 'maintenance' ? 'maintenance'
                             : allTaken ? 'occupied' : 'available';
                g.classList.add(status);
                g.dataset.tableId = t.id;
                g.dataset.index = i;

                const size = 40 + Math.min(t.capacity, 10) * 3;

                if (t.shape === 'round' || t.shape === 'circle') {
                    const circle = document.createElementNS(SVG_NS, 'circle');
                    circle.setAttribute('cx', cx);
                    circle.setAttribute('cy', cy);
                    circle.setAttribute('r', size / 2);
                    g.appendChild(circle);
                } else {
                    const rect = document.createElementNS(SVG_NS, 'rect');
                    rect.setAttribute('x', cx - size / 2);
                    rect.setAttribute('y', cy - size / 2);
                    rect.setAttribute('width', size);
                    rect.setAttribute('height', size);
                    rect.setAttribute('rx', '6');
                    g.appendChild(rect);
                }

                const label = document.createElementNS(SVG_NS, 'text');
                label.setAttribute('x', cx);
                label.setAttribute('y', cy + 5);
                label.setAttribute('text-anchor', 'middle');
                label.textContent = 'T' + t.number;
                g.appendChild(label);

                const cap = document.createElementNS(SVG_NS, 'text');
                cap.setAttribute('x', cx);
                cap.setAttribute('y', cy + size / 2 + 16);
                cap.setAttribute('text-anchor', 'middle');
                cap.setAttribute('font-size', '11');
                cap.setAttribute('fill', '#666');
                cap.textContent = t.capacity + ' seats';
                g.appendChild(cap);

                g.addEventListener('click', function() { selectTable(i); });
                svg.appendChild(g);
            });
        }

        // --- Select table ---
        function selectTable(index) {
            const t = tablesData[index];
            if (!t || t.status === 'maintenance') return;

            document.querySelectorAll('.table-node').forEach(n => n.classList.remove('selected'));
            const node = document.querySelector(`.table-node[data-index="${index}"]`);
            if (node) node.classList.add('selected');

            selectedTable = t;
            selectedSlot = null;

            $('#info-number').text(t.number);
            $('#info-capacity').text(t.capacity + ' seats');
            $('#info-shape').text(t.shape || 'square');
            $('#info-zone').text(t.zone);
            $('#table-info').addClass('show');
            $('#no-selection').hide();
            $('#reserve-form').hide();
            $('#reserve-msg').html('');
            $('#booking-detail').hide();

            // Render slots with role-aware behavior
            const grid = $('#slot-grid').empty();
            t.slots.forEach(function(s) {
                const btn = $('<button type="button">').addClass('slot-btn').text(s.time);

                if (s.available) {
                    // Free slot — anyone can click to reserve
                    btn.addClass('free');
                    btn.on('click', function() { pickSlot(s.time, $(this)); });
                } else {
                    // Occupied slot
                    const bk = s.booking || {};
                    if (bk.status === 'pending') {
                        btn.addClass('pending');
                    } else {
                        btn.addClass('taken');
                    }
                    if (!IS_GUEST) {
                        // Staff/admin: clickable to see booking details
                        btn.css('cursor', 'pointer').css('opacity', '1');
                        btn.on('click', function() { showBookingDetail(s, t, $(this)); });
                    } else {
                        btn.prop('disabled', true).css('cursor', 'not-allowed').css('opacity', '0.6');
                    }
                }
                grid.append(btn);
            });

            $('#res-table-id').val(t.id);
            $('#res-date').val($('#pick-date').val());
        }

        // --- Show booking details (staff/admin only) ---
        function showBookingDetail(slot, table, $btn) {
            const bk = slot.booking || {};
            $('.slot-btn').removeClass('chosen');
            $btn.addClass('chosen');

            $('#reserve-form').hide();
            $('#reserve-msg').html('');

            $('#bd-id').text(bk.booking_id || '—');
            $('#bd-user').text(bk.guest_user || 'Unknown');
            $('#bd-email').text(bk.guest_email || '—');
            $('#bd-party').text(bk.party_size || '—');
            $('#bd-duration').text(
                bk.duration ? (bk.duration + ' min (' + (bk.start_time || '?') + ' – ' + (bk.end_time || '?') + ')') : '30 min'
            );
            $('#bd-notes').text(bk.notes || 'None');

            // Status badge
            let statusHtml = '';
            if (bk.status === 'pending') {
                statusHtml = '<span class="badge bg-warning text-dark">Pending</span>';
            } else if (bk.status === 'confirmed') {
                statusHtml = '<span class="badge bg-success">Confirmed</span>';
            } else {
                statusHtml = '<span class="badge bg-secondary">' + (bk.status || 'unknown') + '</span>';
            }
            $('#bd-status').html(statusHtml);

            // Action buttons for staff
            const $actions = $('#bd-actions').empty();
            if (bk.status === 'pending') {
                $actions.append(
                    $('<button class="btn btn-success btn-sm me-2">Approve</button>').on('click', function() {
                        updateBookingStatus(bk.booking_id, 'confirmed', $(this));
                    }),
                    $('<button class="btn btn-danger btn-sm">Reject</button>').on('click', function() {
                        updateBookingStatus(bk.booking_id, 'cancelled', $(this));
                    })
                );
            } else if (bk.status === 'confirmed') {
                $actions.append(
                    $('<button class="btn btn-outline-danger btn-sm">Cancel Booking</button>').on('click', function() {
                        updateBookingStatus(bk.booking_id, 'cancelled', $(this));
                    })
                );
            }

            $('#booking-detail').slideDown(200);
        }

        // --- Update booking status (approve/reject/cancel) ---
        function updateBookingStatus(bookingId, newStatus, $btn) {
            $btn.prop('disabled', true);
            $.post('/dinebook/guest/admin_booking_action.php', {
                csrf_token: CSRF,
                booking_id: bookingId,
                status: newStatus
            })
            .done(function(res) {
                if (res.success) {
                    const label = newStatus === 'confirmed' ? 'Approved' : 'Cancelled';
                    $('#reserve-msg').html('<div class="alert alert-success mt-2">Booking ' + label.toLowerCase() + ' successfully.</div>');
                    $('#booking-detail').hide();
                    setTimeout(loadMap, 600);
                } else {
                    $('#reserve-msg').html('<div class="alert alert-danger mt-2">' + (res.error || 'Action failed.') + '</div>');
                    $btn.prop('disabled', false);
                }
            })
            .fail(function() {
                $('#reserve-msg').html('<div class="alert alert-danger mt-2">Network error.</div>');
                $btn.prop('disabled', false);
            });
        }

        // --- Pick free time slot (for reservation) ---
        function pickSlot(time, $btn) {
            selectedSlot = time;
            $('.slot-btn').removeClass('chosen');
            $btn.addClass('chosen');
            $('#res-slot').val(time);
            $('#booking-detail').hide();
            $('#reserve-form').slideDown(200);
            $('#reserve-msg').html('');
            $('#res-duration').val('60');
            highlightDuration();
        }

        // --- Duration change: highlight consecutive slots & validate ---
        $('#res-duration').on('change', highlightDuration);

        function highlightDuration() {
            if (!selectedTable || !selectedSlot) return;
            const duration = parseInt($('#res-duration').val(), 10);
            const slotsNeeded = Math.ceil(duration / 30);
            const allSlots = selectedTable.slots;
            const startIdx = allSlots.findIndex(s => s.time === selectedSlot);
            if (startIdx < 0) return;

            // Remove previous highlights
            $('.slot-btn').removeClass('chosen');

            let allFree = true;
            const $buttons = $('#slot-grid .slot-btn');
            for (let i = 0; i < slotsNeeded; i++) {
                const idx = startIdx + i;
                if (idx >= allSlots.length) { allFree = false; break; }
                if (!allSlots[idx].available) { allFree = false; }
                $buttons.eq(idx).addClass('chosen');
            }

            const endIdx = startIdx + slotsNeeded - 1;
            const endTime = endIdx < allSlots.length ? allSlots[endIdx].time : '??:??';
            const $msg = $('#slots-blocked-msg');
            const $submit = $('#reserve-form button[type=submit]');

            if (!allFree) {
                $msg.text('Some slots in this range are occupied. Choose a shorter duration or a different time.');
                $submit.prop('disabled', true);
            } else {
                $msg.text('Table blocked from ' + selectedSlot + ' to ' + addMinutes(endTime, 30) + ' (' + slotsNeeded + ' slot' + (slotsNeeded > 1 ? 's' : '') + ').');
                $submit.prop('disabled', false);
            }
        }

        function addMinutes(time, mins) {
            const [h, m] = time.split(':').map(Number);
            const total = h * 60 + m + mins;
            return String(Math.floor(total / 60)).padStart(2, '0') + ':' + String(total % 60).padStart(2, '0');
        }

        // --- Submit reservation ---
        $('#reserve-form').on('submit', function(e) {
            e.preventDefault();
            if (!selectedTable || !selectedSlot) return;

            const $btn = $(this).find('button[type=submit]');
            $btn.prop('disabled', true).text('Requesting...');

            $.post('/dinebook/guest/reserve_process.php', $(this).serialize())
                .done(function(res) {
                    if (res.success) {
                        const msg = IS_GUEST
                            ? 'Reservation requested for Table ' + selectedTable.number + ' at ' + selectedSlot + '. Waiting for staff approval.'
                            : 'Reserved! Table ' + selectedTable.number + ' at ' + selectedSlot + '.';
                        $('#reserve-msg').html('<div class="alert alert-success mt-2">' + msg + '</div>');
                        $('#reserve-form').hide();
                        setTimeout(loadMap, 800);
                    } else {
                        $('#reserve-msg').html('<div class="alert alert-danger mt-2">' + (res.error || 'Reservation failed.') + '</div>');
                    }
                })
                .fail(function() {
                    $('#reserve-msg').html('<div class="alert alert-danger mt-2">Network error. Try again.</div>');
                })
                .always(function() {
                    $btn.prop('disabled', false).text(IS_GUEST ? 'Request Reservation' : 'Confirm Reservation');
                });
        });
    })();
    </script>
</body>
</html>
