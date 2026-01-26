@extends('layouts.app')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
    <style>
        /* SweetAlert compact & modern */
        .swal2-popup {
            border-radius: 14px !important;
            padding: 1.25rem !important;
            font-size: 0.9rem;
        }

        .swal2-input,
        .swal2-textarea,
        .swal2-select {
            font-size: 0.85rem !important;
            padding: 8px 10px !important;
            border-radius: 8px !important;
            height: 38px !important;
        }

        .swal2-textarea {
            min-height: 70px !important;
            resize: none !important;
        }

        .swal2-actions button {
            border-radius: 8px !important;
            padding: 6px 14px !important;
            font-size: 0.85rem !important;
        }



        #calendar {
            background: #fff;
            padding: 15px;
            border-radius: 14px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
            margin-top: 12px;
        }

        .fc .fc-button {
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 8px;
        }

        .fc .fc-button:hover {
            background: #4338ca;
        }

        .fc-toolbar-title {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .fc-event {
            font-size: 0.85rem;
            padding: 4px;
            border-radius: 6px;
            cursor: pointer;
            min-height: 22px;
            max-height: 22px;
            line-height: 22px;
        }

        .fc-event-title {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            display: block;
        }

        /* .completed { text-decoration:line-through !important; opacity:0.85; background:#9CA3AF !important; } */
        .legend {
            display: flex;
            gap: 12px;
            align-items: center;
            margin-bottom: 12px;
        }

        .legend .item {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .legend .swatch {
            width: 14px;
            height: 14px;
            border-radius: 3px;
        }

        .search-container {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .search-results {
            position: absolute;
            background: #fff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
            z-index: 50;
            width: 320px;
            max-height: 300px;
            overflow: auto;
            border-radius: 8px;
        }

        .search-results .row {
            padding: 8px;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
        }

        .search-results .row:hover {
            background: #f8fafc;
        }

        /* .completed { text-decoration:line-through !important; opacity:0.85; background:#9CA3AF !important; } */
        /* Completed task style */
        /* Completed task style */
        .fc-event.completed {
            background-color: #22c55e !important;
            border-color: #16a34a !important;
            color: #ffffff !important;
            text-decoration: line-through;
            opacity: 0.9;
        }

        .fc-event.completed * {
            text-decoration: line-through;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h5 class="fw-semibold"><i class="bi bi-calendar-event me-2"></i> Task Calendar</h5>
                <div class="text-muted">Add tasks and manage occurrences. Search tasks to jump to their next occurrence.
                </div>
            </div>

            <div style="min-width:340px; position:relative;">
                <div class="d-flex search-container">
                    <input id="task-search" class="form-control" placeholder="Search tasks by title..." autocomplete="off" />
                    <button id="refresh-btn" class="btn btn-outline-secondary">Refresh</button>
                </div>
                <div id="search-results" class="search-results" style="display:none;"></div>
            </div>
        </div>

        <div class="legend mb-2" title="Legend">
            <div class="item">
                <div class="swatch" style="background:#3b82f6"></div><small>Task</small>
            </div>
            <div class="item">
                <div class="swatch" style="background:#f97316"></div><small>Event</small>
            </div>
            <div class="item">
                <div class="swatch" style="background:#8b5cf6"></div><small>Meeting</small>
            </div>
            <div class="item">
                <div class="swatch" style="background:#6b7280"></div><small>Other</small>
            </div>
        </div>

        <div id="calendar"></div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const tokenEl = document.querySelector('meta[name="csrf-token"]');
            const csrf = tokenEl ? tokenEl.content : '';
            const searchInput = document.getElementById('task-search');
            const resultsBox = document.getElementById('search-results');
            const refreshBtn = document.getElementById('refresh-btn');

            // debounce helper
            function debounce(fn, delay = 300) {
                let t;
                return function(...args) {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, args), delay);
                };
            }

            // simple HTML escape for injecting values into SweetAlert content
            function escapeHtml(unsafe) {
                return String(unsafe || '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                // events endpoint (server returns current-month occurrences)
                events: {
                    url: "{{ route('calendar.events') }}",
                    method: 'GET'
                },

                dateClick(info) {
    const defaultStart = info.dateStr;

    Swal.fire({
        title: 'Add Task / Event',
        html: `
<div style="display:flex; flex-direction:column; gap:12px; font-family:sans-serif;">

  <!-- Title -->
  <input id="title" class="swal2-input" placeholder="Title"
    style="font-weight:600; font-size:0.95rem; border:2px solid #3b82f6; border-radius:8px; padding:8px; transition:0.2s;"/>
  
  <!-- Description -->
  <textarea id="description" class="swal2-textarea" placeholder="Description (optional)"
    style="min-height:70px; resize:none; border:2px solid #f97316; border-radius:8px; padding:8px; transition:0.2s;"></textarea>

  <!-- Type & Repeat -->
  <div style="display:flex; gap:10px;">
    <div style="flex:1; display:flex; flex-direction:column;">
      <label style="font-size:0.8rem; font-weight:500; color:#374151; margin-bottom:4px;">Type</label>
      <select id="type" class="swal2-select"
        style="border:2px solid #3b82f6; border-radius:8px; padding:6px; background:#eff6ff; color:#1e3a8a; font-weight:500;">
        <option value="task">Task</option>
        <option value="event">Event</option>
        <option value="meeting">Meeting</option>
        <option value="other">Other</option>
      </select>
    </div>
    <div style="flex:1; display:flex; flex-direction:column;">
      <label style="font-size:0.8rem; font-weight:500; color:#374151; margin-bottom:4px;">Repeat</label>
      <select id="repeat" class="swal2-select"
        style="border:2px solid #f97316; border-radius:8px; padding:6px; background:#fff7ed; color:#c2410c; font-weight:500;">
        <option value="none">No Repeat</option>
        <option value="daily">Daily</option>
        <option value="weekly">Weekly</option>
        <option value="monthly">Monthly</option>
      </select>
    </div>
  </div>

  <!-- Time & Start Date -->
  <div style="display:flex; gap:10px;">
    <div style="flex:1; display:flex; flex-direction:column;">
      <label style="font-size:0.8rem; font-weight:500; color:#374151; margin-bottom:4px;">Time</label>
      <input id="time" type="time" class="swal2-input"
        style="border:2px solid #22c55e; border-radius:8px; padding:6px; background:#ecfdf5; color:#166534; font-weight:500;" value="10:00">
    </div>
    <div style="flex:1; display:flex; flex-direction:column;">
      <label style="font-size:0.8rem; font-weight:500; color:#374151; margin-bottom:4px;">Start Date</label>
      <input id="start_date" type="date" class="swal2-input"
        style="border:2px solid #8b5cf6; border-radius:8px; padding:6px; background:#f3e8ff; color:#5b21b6; font-weight:500;" value="${defaultStart}">
    </div>
  </div>

  <!-- Repeat End -->
  <div style="display:flex; flex-direction:column;">
    <label style="font-size:0.8rem; font-weight:500; color:#374151; margin-bottom:4px;">Repeat End (optional)</label>
    <input id="repeat_end_date" type="date" class="swal2-input"
      style="border:2px solid #f43f5e; border-radius:8px; padding:6px; background:#fef2f2; color:#be123c; font-weight:500;">
  </div>

</div>
        `,
        confirmButtonText: 'Save',
        showCancelButton: true,
        focusConfirm: false,
        customClass: {
            popup: 'swal2-popup-modern'
        },
        preConfirm: () => ({
            title: document.getElementById('title').value.trim(),
            description: document.getElementById('description').value.trim(),
            task_type: document.getElementById('type').value,
            repeat_type: document.getElementById('repeat').value,
            task_time: document.getElementById('time').value,
            start_date: document.getElementById('start_date').value,
            repeat_end_date: document.getElementById('repeat_end_date').value
        })
    }).then(result => {
        if (!result.isConfirmed) return;
        const dt = info.dateStr + ' ' + result.value.task_time;
        fetch("{{ route('calendar.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                title: result.value.title,
                description: result.value.description,
                task_type: result.value.task_type,
                repeat_type: result.value.repeat_type,
                task_datetime: dt,
                start_date: result.value.start_date || info.dateStr,
                repeat_end_date: result.value.repeat_end_date || null
            })
        }).then(() => calendar.refetchEvents());
    });
}
,

                eventClick(info) {
                    const evt = info.event;
                    const ext = evt.extendedProps || {};
                    const occurrenceDate = evt.startStr;
                    const dateOnly = new Date(occurrenceDate).toISOString().slice(0, 10);
                    const completedAt = ext.completed_at || null;
                    const completedNote = ext.completed_note || null;
                    const isCompleted = !!ext.is_completed;

                    // Build modal content with completed note shown if present
                    Swal.fire({
                        title: escapeHtml(evt.title),
                        html: `
                    <div style="text-align:left">
                        <p><strong>Type:</strong> ${escapeHtml(ext.task_type || 'N/A')}</p>
                        <p><strong>Repeat:</strong> ${escapeHtml(ext.repeat_type || 'none')}</p>
                        <p><strong>Date:</strong> ${new Date(occurrenceDate).toLocaleString()}</p>
                        <p><strong>Description:</strong></p>
                        <div style="font-size:0.9rem; opacity:0.9; margin-top:6px;">${ext.description ? escapeHtml(ext.description) : '<i>No description</i>'}</div>
                        ${isCompleted ? `<p style="margin-top:8px"><strong>Completed at:</strong> ${new Date(completedAt).toLocaleString()}</p><div style="margin-top:6px; font-size:0.9rem;"><strong>Note:</strong> ${completedNote ? escapeHtml(completedNote) : '<i>No note provided</i>'}</div>` : ''}
                        <div style="margin-top:8px; font-size:0.85rem; color:#6b7280;">
                            ${ext.start_date ? 'Start: '+escapeHtml(ext.start_date) : ''} ${ext.repeat_end_date ? ' • End: '+escapeHtml(ext.repeat_end_date) : ''}
                        </div>
                    </div>
                `,
                        showCancelButton: true,
                        showDenyButton: true,
                        showCloseButton: true,
                        confirmButtonText: isCompleted ? 'Uncomplete' : 'Complete',
                        denyButtonText: 'Edit',
                        cancelButtonText: 'Delete'
                    }).then(result => {
                        if (result.isConfirmed) {
                            if (isCompleted) {
                                // Uncomplete: remove completion for this date
                                fetch(`{{ url('/calendar') }}/${evt.id}/uncomplete`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': csrf,
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        date: dateOnly
                                    })
                                }).then(() => calendar.refetchEvents());
                            } else {
                                // Complete: prompt for optional note (server will write completed_at)
                                Swal.fire({
                                    title: 'Complete occurrence (optional note)',
                                    html: `<textarea id="complete_note" class="swal2-textarea" placeholder="Add note (optional)"></textarea>`,
                                    showCancelButton: true,
                                    confirmButtonText: 'Mark complete',
                                    preConfirm: () => ({
                                        note: document.getElementById(
                                            'complete_note').value
                                    })
                                }).then(res => {
                                    if (!res.isConfirmed) return;
                                    const payload = {
                                        date: dateOnly
                                    };
                                    if (res.value && res.value.note) payload.note = res
                                        .value.note;
                                    fetch(`{{ url('/calendar') }}/${evt.id}/complete`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': csrf,
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify(payload)
                                    }).then(() => calendar.refetchEvents());
                                });
                            }
                        } else if (result.isDenied) {
                            // Edit flow: prefill fields (use occurrence date for date defaults)
                            const datePart = new Date(occurrenceDate).toISOString().slice(0, 10);
                            const startVal = ext.start_date || datePart;
                            const endVal = ext.repeat_end_date || '';

                            Swal.fire({
                                title: 'Edit Task',
                                html: `
                            <input id="title2" class="swal2-input" placeholder="Title" value="${escapeHtml(evt.title)}">
                            <textarea id="description2" class="swal2-textarea" placeholder="Description">${escapeHtml(ext.description || '')}</textarea>

                            <div style="display:flex; gap:8px;">
                                <select id="type2" class="swal2-input" style="flex:1">
                                    <option value="task" ${ext.task_type === 'task' ? 'selected' : ''}>Task</option>
                                    <option value="event" ${ext.task_type === 'event' ? 'selected' : ''}>Event</option>
                                    <option value="meeting" ${ext.task_type === 'meeting' ? 'selected' : ''}>Meeting</option>
                                    <option value="other" ${ext.task_type === 'other' ? 'selected' : ''}>Other</option>
                                </select>

                                <select id="repeat2" class="swal2-input" style="flex:1">
                                    <option value="none" ${ext.repeat_type === 'none' ? 'selected' : ''}>No Repeat</option>
                                    <option value="daily" ${ext.repeat_type === 'daily' ? 'selected' : ''}>Daily</option>
                                    <option value="weekly" ${ext.repeat_type === 'weekly' ? 'selected' : ''}>Weekly</option>
                                    <option value="monthly" ${ext.repeat_type === 'monthly' ? 'selected' : ''}>Monthly</option>
                                </select>
                            </div>

                            <div style="display:flex; gap:8px; margin-top:8px;">
                                <input id="time2" type="time" class="swal2-input" value="${(new Date(occurrenceDate)).toTimeString().slice(0,5)}" style="flex:1">
                                <input id="start_date2" type="date" class="swal2-input" value="${escapeHtml(startVal)}" style="flex:1">
                            </div>

                            <div style="display:flex; gap:8px; margin-top:8px;">
                                <label style="font-size:0.85rem; align-self:center;">Repeat end (optional)</label>
                                <input id="repeat_end_date2" type="date" class="swal2-input" value="${escapeHtml(endVal)}" style="flex:1">
                            </div>
                        `,
                                confirmButtonText: 'Save',
                                showCancelButton: true,
                                preConfirm: () => {
                                    return {
                                        title: document.getElementById('title2').value,
                                        description: document.getElementById(
                                            'description2').value,
                                        task_type: document.getElementById('type2')
                                            .value,
                                        repeat_type: document.getElementById('repeat2')
                                            .value,
                                        task_time: document.getElementById('time2')
                                            .value,
                                        start_date: document.getElementById(
                                            'start_date2').value,
                                        repeat_end_date: document.getElementById(
                                            'repeat_end_date2').value
                                    };
                                }
                            }).then(res => {
                                if (!res.isConfirmed) return;
                                const datePart = new Date(occurrenceDate).toISOString()
                                    .slice(0, 10);
                                const dt = datePart + ' ' + res.value.task_time;
                                fetch(`{{ url('/calendar') }}/${evt.id}`, {
                                    method: 'PUT',
                                    headers: {
                                        'X-CSRF-TOKEN': csrf,
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        title: res.value.title,
                                        description: res.value.description,
                                        task_type: res.value.task_type,
                                        repeat_type: res.value.repeat_type,
                                        task_datetime: dt,
                                        start_date: res.value.start_date ||
                                            datePart,
                                        repeat_end_date: res.value
                                            .repeat_end_date || null
                                    })
                                }).then(() => calendar.refetchEvents());
                            });
                        } else if (result.isDismissed && result.dismiss === Swal.DismissReason
                            .cancel) {
                            // Delete
                            Swal.fire({
                                title: 'Are you sure?',
                                text: 'This will delete the task.',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, delete'
                            }).then(del => {
                                if (!del.isConfirmed) return;
                                fetch(`{{ url('/calendar') }}/${evt.id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': csrf,
                                        'Accept': 'application/json'
                                    }
                                }).then(r => r.json()).then(json => {
                                    if (json && json.success) calendar
                                        .refetchEvents();
                                    else Swal.fire('Could not delete', json
                                        .message || 'Delete failed', 'error');
                                });
                            });
                        }
                    });
                },

                eventDidMount(info) {
                    const ext = info.event.extendedProps || {};

                    if (ext.is_completed) {
                        info.el.classList.add('completed');

                        // ✔ icon
                        const check = document.createElement('span');
                        check.innerHTML = '✔ ';
                        check.style.fontWeight = 'bold';
                        check.style.marginRight = '4px';
                        info.el.prepend(check);
                    } else {
                        info.el.style.backgroundColor =
                            info.event.backgroundColor || ext.original_color || '#4f46e5';
                        info.el.style.color = '#fff';
                    }

                    // Description
                    if (ext.description) {
                        const desc = document.createElement('div');
                        desc.style.fontSize = '0.75rem';
                        desc.style.opacity = 0.85;
                        desc.style.marginTop = '4px';
                        desc.innerText = ext.description;
                        info.el.appendChild(desc);
                    }

                    // Completed time
                    if (ext.is_completed && ext.completed_at) {
                        const infoLine = document.createElement('div');
                        infoLine.style.fontSize = '0.7rem';
                        infoLine.style.opacity = 0.85;
                        infoLine.style.marginTop = '6px';
                        infoLine.innerText = `Completed: ${new Date(ext.completed_at).toLocaleString()}`;
                        info.el.appendChild(infoLine);
                    }

                    // Completed note
                    if (ext.is_completed && ext.completed_note) {
                        const noteLine = document.createElement('div');
                        noteLine.style.fontSize = '0.7rem';
                        noteLine.style.opacity = 0.85;
                        noteLine.style.marginTop = '4px';
                        noteLine.innerText = `Note: ${ext.completed_note}`;
                        info.el.appendChild(noteLine);
                    }
                }

            });

            calendar.render();

            // Search behavior
            const doSearch = debounce(function() {
                const q = searchInput.value.trim();
                if (!q) {
                    resultsBox.style.display = 'none';
                    resultsBox.innerHTML = '';
                    return;
                }
                fetch(`{{ route('calendar.search') }}?q=${encodeURIComponent(q)}`, {
                        method: 'GET'
                    })
                    .then(r => r.json()).then(json => {
                        resultsBox.innerHTML = '';
                        if (!json.length) {
                            const no = document.createElement('div');
                            no.className = 'row';
                            no.innerText = 'No tasks found';
                            resultsBox.appendChild(no);
                        } else {
                            json.forEach(item => {
                                const row = document.createElement('div');
                                row.className = 'row';
                                row.innerHTML =
                                    `<div><strong>${escapeHtml(item.title)}</strong> <small class="text-muted">(${escapeHtml(item.task_type)})</small><div style="font-size:0.85rem; color:#6b7280">${item.next_occurrence ? 'Next: '+ new Date(item.next_occurrence).toLocaleString() : 'No upcoming occurrence'}</div></div>`;
                                row.addEventListener('click', () => {
                                    resultsBox.style.display = 'none';
                                    searchInput.value = '';
                                    if (item.next_occurrence) {
                                        calendar.gotoDate(item.next_occurrence);
                                        calendar.refetchEvents();
                                    } else {
                                        calendar.refetchEvents();
                                    }
                                });
                                resultsBox.appendChild(row);
                            });
                        }
                        resultsBox.style.display = 'block';
                    });
            }, 300);

            searchInput.addEventListener('input', doSearch);
            document.addEventListener('click', (e) => {
                if (!resultsBox.contains(e.target) && e.target !== searchInput) resultsBox.style.display =
                    'none';
            });

            refreshBtn.addEventListener('click', () => calendar.refetchEvents());
        });
    </script>
@endpush
