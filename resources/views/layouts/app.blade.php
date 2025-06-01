<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- jQuery (optional if you use jQuery for your code) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- Bootstrap Bundle JS (includes Popper) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


        <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->


        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            tr.even-row {
                background-color: #c2d9ff;
            }
            tr.odd-row {
                background-color: #c2ffef;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
            @yield('content')
            </main>
        </div>
    </body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {

            function loadTasks(projectId, status = '', search = '') {
                $.get(`/projects/${projectId}/tasks`, { status: status, search: search }, function(tasks) {
                    let rows = '';
                    tasks.forEach((task, index) => {
                        let rowClass = index % 2 === 0 ? 'even-row' : 'odd-row';
                        rows += `
                            <tr class="${rowClass}">
                                <td>${task.title}</td>
                                <td>
                                    <button class="btn btn-sm toggle-status" data-id="${task.id}" data-project="${projectId}">
                                        ${task.status}
                                    </button>
                                </td>
                                <td>${task.due_date ?? ''}</td>
                                <td>
                                    <button class="btn btn-primary btn-sm edit-task-btn"
                                            data-id="${task.id}"
                                            data-title="${task.title}"
                                            data-description="${task.description}"
                                            data-due="${task.due_date ?? ''}"
                                            data-project="${projectId}">
                                        Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm delete-task" data-id="${task.id}" data-project="${projectId}">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $(`.task-table[data-project="${projectId}"] .task-list`).html(rows);
                });
            }


            $('.task-filter').on('click', function() {
                const projectId = $(this).data('project');
                const status = $(this).data('status');
                const search = $(`.task-search[data-project="${projectId}"]`).val();
                loadTasks(projectId, status, search);
            });

            $('.task-search').on('keyup', function() {
                const projectId = $(this).data('project');
                const search = $(this).val();
                loadTasks(projectId, '', search);
            });

            $(document).on('click', '.toggle-status', function() {
                const taskId = $(this).data('id');
                const projectId = $(this).data('project');

                $.ajax({
                    url: `/tasks/${taskId}/toggle`,
                    method: 'PATCH',
                    data: {
                            _token: '{{ csrf_token() }}'
                        },
                    success: function() {
                        loadTasks(projectId);
                    }
                });
            });

            $(document).on('click', '.edit-task-btn', function () {
                const id = $(this).data('id');
                const title = $(this).data('title');
                const description = $(this).data('description');
                const due_date = $(this).data('due');
                const project_id = $(this).data('project');

                $('#taskModalLabel').text('Edit Task');
                $('#task_id').val(id);
                $('#task_title').val(title);
                $('#task_description').val(description);
                $('#task_due_date').val(due_date);
                $('#project_id').val(project_id);

                $('#taskModal').modal('show');

                // $.get(`/tasks/${taskId}/edit`, function (res) {
                //     $('#edit-task-id').val(res.task.id);
                //     $('#edit-task-name').val(res.task.name);
                //     // Populate other fields if any
                //     $('#edit-task-form').attr('action', `/tasks/${taskId}`);
                //     $('#editTaskModal').modal('show');
                // });
            });

            $('#edit-task-form').on('submit', function (e) {
                e.preventDefault();

                const taskId = $('#edit-task-id').val();
                const formData = $(this).serialize();

                $.ajax({
                    url: `/tasks/${taskId}`,
                    method: 'POST', // Because you're spoofing PUT via @method('PUT')
                    data: formData,
                    success: function (res) {
                        if (res.success) {
                            $('#editTaskModal').modal('hide');

                            // ✅ Update task row on the page
                            $(`#task-row-${res.task.id} .task-title`).text(res.task.title);
                            $(`#task-row-${res.task.id} .task-desc`).text(res.task.description);
                            $(`#task-row-${res.task.id} .task-due`).text(res.task.due_date ?? 'No due date');

                            // Show success alert or toast
                            alert('Task updated successfully.');
                        }
                    },
                    error: function (xhr) {
                        alert('Something went wrong while updating the task.');
                        console.log(xhr.responseText);
                    }
                });
            });

            $(document).on('click', '.delete-task', function() {
                const taskId = $(this).data('id');
                const projectId = $(this).data('project');

                if (confirm('Are you sure you want to delete this task?')) {
                    $.ajax({
                        url: `/tasks/${taskId}`,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            loadTasks(projectId);
                        }
                    });
                }
            });

            $('.task-table').each(function() {
                const projectId = $(this).data('project');
                loadTasks(projectId);
            });

            $(document).on('click', '.add-task-btn', function() {
                $('#taskForm').trigger('reset');
                $('#task_id').val('');
                $('#project_id').val($(this).data('project'));

                var taskModal = new bootstrap.Modal(document.getElementById('taskModal'));
                taskModal.show();
            });

            $('#taskForm').on('submit', function(e) {
                e.preventDefault();

                let taskId = $('#task_id').val();
                let method = taskId ? 'PUT' : 'POST';
                let url = taskId ? `/tasks/${taskId}` : `/tasks`;

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: $(this).serialize() + (taskId ? '&_method=PUT' : ''),
                    success: function() {
                        $('#taskModal').modal('hide');
                        setTimeout(() => {
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                            $('body').css('padding-right', '');
                        }, 500);

                        loadTasks($('#project_id').val());
                        $('#taskForm')[0].reset();
                        $('#task_id').val('');
                    },
                    error: function(xhr) {
                        alert('Something went wrong: ' + xhr.responseText);
                    }
                });
            });

        });
    </script>

</html>
