<?php
namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request, $projectId)
    {
        $query = Task::where('project_id', $projectId);

        if ($request->has('status') && in_array($request->status, ['pending', 'completed'])) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $tasks = $query->get();

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date',
        ]);
        
        $task = Task::create($validated);
        $this->logActivity("Created task: {$task->title}");

        return response()->json(['success' => true, 'task' => $task]);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);
        $this->logActivity("Updated task: {$task->title}");

        // return response()->json(['success' => true]);
        return response()->json([
            'success' => true,
            'task' => $task
        ]);
    }

    public function toggleStatus(Task $task)
    {
        $task->status = $task->status === 'pending' ? 'completed' : 'pending';
        $task->save();

        $this->logActivity("Toggled task status: {$task->title} to {$task->status}");

        return response()->json(['success' => true, 'status' => $task->status]);
    }

    public function edit(Task $task)
    {
        return response()->json([
            'task' => $task
        ]);
    }

    public function destroy(Task $task)
    {
        $this->logActivity("Deleted task: {$task->title}");
        $task->delete();

        return response()->json(['success' => true]);
    }

    private function logActivity($action)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
        ]);
    }
}
