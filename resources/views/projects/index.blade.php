@extends('layouts.app')
<style>
  tr.even-row > td {
    background-color: #c2d9ff;
  }
  tr.odd-row > td {
    background-color: #c2ffef;
  }
</style>
@section('content')
<div class="container">
    <h2>All Projects</h2>
    <a href="{{ route('projects.create') }}" class="btn btn-primary mb-3">+ Add Project</a>

    @foreach ($projects as $project)
        @php
            $total = $project->tasks->count();
            $completed = $project->tasks->where('status', 'completed')->count();
            $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
        @endphp

        <div class="card mb-4">
            <!-- <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ $project->name }}</strong>
                <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-warning">Edit</a>
            </div> -->
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>{{ $project->name }}</strong>
                <div>
                    <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-sm btn-warning">Edit</a>

                    <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this project?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <p>{{ $project->description }}</p>
                <p><strong>Tasks:</strong> {{ $total }} | <strong>Completed:</strong> {{ $percent }}%</p>

                <input type="text" class="form-control mb-2 task-search" placeholder="Search tasks..." data-project="{{ $project->id }}">
                <div class="btn-group mb-3" role="group">
                    <button class="btn btn-outline-secondary task-filter" data-project="{{ $project->id }}" data-status="">All</button>
                    <button class="btn btn-outline-secondary task-filter" data-project="{{ $project->id }}" data-status="pending">Pending</button>
                    <button class="btn btn-outline-secondary task-filter" data-project="{{ $project->id }}" data-status="completed">Completed</button>
                </div>

                <table class="table task-table" data-project="{{ $project->id }}">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="task-list"></tbody>
                </table>
                
                <button type="button" class="btn btn-sm btn-success add-task-btn" data-project="{{ $project->id }}" data-bs-toggle="modal" data-bs-target="#taskModal">+ Add Task</button>
            </div>
        </div>
    @endforeach
</div>

<!-- Task Modal -->
<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="taskForm" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add/Edit Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="task_id" id="task_id">
          <input type="hidden" name="project_id" id="project_id">
          <div class="mb-2">
            <label>Title</label>
            <input type="text" name="title" id="task_title" class="form-control" required>
          </div>
          <div class="mb-2">
            <label>Description</label>
            <textarea name="description" id="task_description" class="form-control" required></textarea>
          </div>
          <div class="mb-2">
            <label>Due Date</label>
            <input type="date" name="due_date" id="task_due_date" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="edit-task-form" method="POST">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="task_id" id="edit-task-id">
          <input type="hidden" name="project_id" id="edit-project-id">

          <div class="mb-2">
            <label>Title</label>
            <input type="text" name="title" id="edit-task-title" class="form-control" required>
          </div>

          <div class="mb-2">
            <label>Description</label>
            <textarea name="description" id="edit-task-description" class="form-control" required></textarea>
          </div>

          <div class="mb-2">
            <label>Due Date</label>
            <input type="date" name="due_date" id="edit-task-due-date" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update Task</button>
        </div>
      </div>
    </form>
  </div>
</div>


@endsection

@section('scripts')

@endsection
