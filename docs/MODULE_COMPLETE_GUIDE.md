# Complete Module Development Guide

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Module Creation Checklist](#module-creation-checklist)
3. [Step-by-Step Tutorial](#step-by-step-tutorial)
4. [Advanced Topics](#advanced-topics)
5. [Troubleshooting](#troubleshooting)

---

## Prerequisites

### Required Knowledge
- ✅ PHP 8.2+ and Laravel 12
- ✅ PostgreSQL basics
- ✅ Blade templating
- ✅ Basic understanding of MVC pattern

### Tools Needed
- PHP 8.2+
- Composer
- PostgreSQL client
- Code editor (VS Code recommended)

---

## Module Creation Checklist

Use this checklist when creating a new module:

```
Module: _______________

[ ] 1. Create module folder structure
[ ] 2. Create Module.php configuration
[ ] 3. Create database migration
[ ] 4. Create seeder for module registration
[ ] 5. Create controller(s)
[ ] 6. Create routes file
[ ] 7. Create views
[ ] 8. Register module in AppServiceProvider
[ ] 9. Run migrations and seeders
[ ] 10. Test all access levels
[ ] 11. Document module features
```

---

## Step-by-Step Tutorial

We'll create a **"Tasks"** module for managing to-do items.

### Step 1: Create Module Folder Structure

```bash
cd /home/sohail/Documents/Projects/eoffice

# Create module directories
mkdir -p app/Modules/Tasks/{Controllers,Resources/Views,Services}
```

**Expected structure:**
```
app/Modules/Tasks/
├── Module.php
├── Controllers/
│   └── TasksController.php
├── Resources/
│   └── Views/
│       ├── index.blade.php
│       ├── show.blade.php
│       ├── create.blade.php
│       └── edit.blade.php
├── Services/           # Optional
│   └── TaskService.php
└── routes.php
```

### Step 2: Create Module Configuration

**File:** `app/Modules/Tasks/Module.php`

```php
<?php

namespace App\Modules\Tasks;

class Module
{
    /**
     * Module name (displayed in UI)
     */
    const NAME = 'Tasks';

    /**
     * Module slug (used in URLs and database)
     */
    const SLUG = 'tasks';

    /**
     * Module description
     */
    const DESCRIPTION = 'Manage tasks and to-do items';

    /**
     * Access levels for this module
     * Key = slug, Value = hierarchy (1-4)
     */
    const LEVELS = [
        'browse' => 1,  // Can view task list
        'read' => 2,    // Can view task details
        'write' => 3,   // Can create/edit tasks
        'manage' => 4,  // Can delete tasks
    ];

    /**
     * Module version
     */
    const VERSION = '1.0.0';
}
```

### Step 3: Create Database Migration

```bash
php artisan make:migration create_tasks_table
```

**File:** `database/migrations/YYYY_MM_DD_HHMMSS_create_tasks_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('pgsql_app')->create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable(); // User ID from pgsql
            $table->unsignedBigInteger('created_by'); // User ID from pgsql
            $table->timestamps();
            $table->softDeletes(); // For soft delete functionality
            
            // Indexes for performance
            $table->index('status');
            $table->index('assigned_to');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::connection('pgsql_app')->dropIfExists('tasks');
    }
};
```

### Step 4: Create Model

**File:** `app/Models/Task.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    protected $connection = 'pgsql_app';
    protected $table = 'tasks';

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
        'assigned_to',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created this task
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'userid')
            ->setConnection('pgsql');
    }

    /**
     * Get the user assigned to this task
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'userid')
            ->setConnection('pgsql');
    }

    /**
     * Scope for pending tasks
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for high priority tasks
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }
}
```

### Step 5: Create Module Seeder

```bash
php artisan make:seeder TasksModuleSeeder
```

**File:** `database/seeders/TasksModuleSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Modules\Tasks\Module;

class TasksModuleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Register module
        $moduleId = DB::connection('pgsql_app')->table('modules')->insertGetId([
            'name' => Module::NAME,
            'slug' => Module::SLUG,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Create access levels
        $levels = [];
        foreach (Module::LEVELS as $slug => $hierarchy) {
            $levelId = DB::connection('pgsql_app')->table('module_levels')->insertGetId([
                'module_id' => $moduleId,
                'name' => ucfirst($slug) . ' Access',
                'slug' => $slug,
                'hierarchy' => $hierarchy,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $levels[$slug] = $levelId;
        }

        // 3. Optional: Assign default access to admin users
        $adminUsers = DB::connection('pgsql_app')
            ->table('system_role_user')
            ->where('system_role_id', 1) // Admin role
            ->pluck('user_id');

        foreach ($adminUsers as $userId) {
            DB::connection('pgsql_app')->table('user_module_levels')->insert([
                'user_id' => $userId,
                'module_level_id' => $levels['manage'], // Give admins full access
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        echo "✅ Tasks module registered successfully!\n";
    }
}
```

**Add to DatabaseSeeder:**

```php
// database/seeders/DatabaseSeeder.php
public function run(): void
{
    $this->call([
        // ... other seeders
        TasksModuleSeeder::class,
    ]);
}
```

### Step 6: Create Controller

**File:** `app/Modules/Tasks/Controllers/TasksController.php`

```php
<?php

namespace App\Modules\Tasks\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{
    /**
     * Display task listing (Browse level)
     */
    public function index(Request $request)
    {
        $query = Task::with(['creator', 'assignee'])
            ->orderBy('created_at', 'desc');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ILIKE', "%{$search}%")
                  ->orWhere('description', 'ILIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        $tasks = $query->paginate(15);

        return view('tasks::index', compact('tasks'));
    }

    /**
     * Show task details (Read level)
     */
    public function show($id)
    {
        $task = Task::with(['creator', 'assignee'])->findOrFail($id);
        return view('tasks::show', compact('task'));
    }

    /**
     * Show create form (Write level)
     */
    public function create()
    {
        // Get all users for assignment dropdown
        $users = User::orderBy('full_name')->get();
        return view('tasks::create', compact('users'));
    }

    /**
     * Store new task (Write level)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date|after:today',
            'assigned_to' => 'nullable|exists:pgsql.users,userid',
        ]);

        $task = Task::create([
            ...$validated,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('tasks.show', $task->id)
            ->with('success', 'Task created successfully!');
    }

    /**
     * Show edit form (Write level)
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $users = User::orderBy('full_name')->get();
        return view('tasks::edit', compact('task', 'users'));
    }

    /**
     * Update task (Write level)
     */
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:pgsql.users,userid',
        ]);

        $task->update($validated);

        return redirect()
            ->route('tasks.show', $task->id)
            ->with('success', 'Task updated successfully!');
    }

    /**
     * Delete task (Manage level)
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted successfully!');
    }
}
```

### Step 7: Create Routes

**File:** `app/Modules/Tasks/routes.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Tasks\Controllers\TasksController;

/*
|--------------------------------------------------------------------------
| Tasks Module Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'module:tasks'])->prefix('tasks')->name('tasks.')->group(function () {
    
    // Browse level - View list
    Route::middleware('level:browse')->group(function () {
        Route::get('/', [TasksController::class, 'index'])->name('index');
    });

    // Read level - View details
    Route::middleware('level:read')->group(function () {
        Route::get('/{id}', [TasksController::class, 'show'])->name('show');
    });

    // Write level - Create and edit
    Route::middleware('level:write')->group(function () {
        Route::get('/create/form', [TasksController::class, 'create'])->name('create');
        Route::post('/', [TasksController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [TasksController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TasksController::class, 'update'])->name('update');
    });

    // Manage level - Delete
    Route::middleware('level:manage')->group(function () {
        Route::delete('/{id}', [TasksController::class, 'destroy'])->name('destroy');
    });
});
```

### Step 8: Create Views

**File:** `app/Modules/Tasks/Resources/Views/index.blade.php`

```blade
<x-layouts.app>
    <x-ui.page-header 
        title="Tasks" 
        description="Manage your tasks and to-do items"
    />

    <!-- Filters -->
    <div class="glass-panel rounded-xl p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <input 
                type="text" 
                name="search" 
                placeholder="Search tasks..." 
                value="{{ request('search') }}"
                class="px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-india-saffron"
            />

            <!-- Status Filter -->
            <select name="status" class="px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>

            <!-- Priority Filter -->
            <select name="priority" class="px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white">
                <option value="">All Priorities</option>
                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-india-saffron text-white rounded-lg hover:bg-india-saffron/80 transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Create Button (Write level) -->
    @if(app(\App\Services\RbacService::class)->userHasLevel(auth()->user(), 'tasks', 'write'))
        <div class="mb-6">
            <a href="{{ route('tasks.create') }}" class="inline-flex items-center px-4 py-2 bg-india-saffron text-white rounded-lg hover:bg-india-saffron/80 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Task
            </a>
        </div>
    @endif

    <!-- Tasks List -->
    <div class="space-y-4">
        @forelse($tasks as $task)
            <div class="glass-panel rounded-xl p-6 hover:ring-2 hover:ring-india-saffron/40 transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-white mb-2">
                            <a href="{{ route('tasks.show', $task->id) }}" class="hover:text-india-saffron transition">
                                {{ $task->title }}
                            </a>
                        </h3>
                        
                        <p class="text-slate-400 mb-4">{{ Str::limit($task->description, 150) }}</p>
                        
                        <div class="flex items-center gap-4 text-sm">
                            <!-- Status Badge -->
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @if($task->status == 'completed') bg-green-500/20 text-green-400
                                @elseif($task->status == 'in_progress') bg-blue-500/20 text-blue-400
                                @else bg-slate-500/20 text-slate-400
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>

                            <!-- Priority Badge -->
                            <span class="px-3 py-1 rounded-full text-xs font-medium
                                @if($task->priority == 'high') bg-red-500/20 text-red-400
                                @elseif($task->priority == 'medium') bg-yellow-500/20 text-yellow-400
                                @else bg-slate-500/20 text-slate-400
                                @endif">
                                {{ ucfirst($task->priority) }} Priority
                            </span>

                            <!-- Due Date -->
                            @if($task->due_date)
                                <span class="text-slate-400">
                                    Due: {{ $task->due_date->format('M d, Y') }}
                                </span>
                            @endif

                            <!-- Assigned To -->
                            @if($task->assignee)
                                <span class="text-slate-400">
                                    Assigned to: {{ $task->assignee->full_name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="glass-panel rounded-xl p-12 text-center">
                <p class="text-slate-400">No tasks found.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $tasks->links() }}
    </div>
</x-layouts.app>
```

**File:** `app/Modules/Tasks/Resources/Views/show.blade.php`

```blade
<x-layouts.app>
    <x-ui.page-header 
        title="{{ $task->title }}" 
        description="Task Details"
    />

    <div class="glass-panel rounded-xl p-8">
        <!-- Task Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <h3 class="text-sm font-bold text-slate-400 uppercase mb-2">Status</h3>
                <span class="px-4 py-2 rounded-lg inline-block
                    @if($task->status == 'completed') bg-green-500/20 text-green-400
                    @elseif($task->status == 'in_progress') bg-blue-500/20 text-blue-400
                    @else bg-slate-500/20 text-slate-400
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                </span>
            </div>

            <div>
                <h3 class="text-sm font-bold text-slate-400 uppercase mb-2">Priority</h3>
                <span class="px-4 py-2 rounded-lg inline-block
                    @if($task->priority == 'high') bg-red-500/20 text-red-400
                    @elseif($task->priority == 'medium') bg-yellow-500/20 text-yellow-400
                    @else bg-slate-500/20 text-slate-400
                    @endif">
                    {{ ucfirst($task->priority) }}
                </span>
            </div>

            @if($task->due_date)
                <div>
                    <h3 class="text-sm font-bold text-slate-400 uppercase mb-2">Due Date</h3>
                    <p class="text-white">{{ $task->due_date->format('F d, Y') }}</p>
                </div>
            @endif

            @if($task->assignee)
                <div>
                    <h3 class="text-sm font-bold text-slate-400 uppercase mb-2">Assigned To</h3>
                    <p class="text-white">{{ $task->assignee->full_name }}</p>
                </div>
            @endif

            <div>
                <h3 class="text-sm font-bold text-slate-400 uppercase mb-2">Created By</h3>
                <p class="text-white">{{ $task->creator->full_name }}</p>
            </div>

            <div>
                <h3 class="text-sm font-bold text-slate-400 uppercase mb-2">Created At</h3>
                <p class="text-white">{{ $task->created_at->format('F d, Y H:i') }}</p>
            </div>
        </div>

        <!-- Description -->
        <div class="mb-8">
            <h3 class="text-sm font-bold text-slate-400 uppercase mb-2">Description</h3>
            <p class="text-white whitespace-pre-wrap">{{ $task->description ?? 'No description provided.' }}</p>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
            <a href="{{ route('tasks.index') }}" class="px-4 py-2 bg-white/5 text-white rounded-lg hover:bg-white/10 transition">
                Back to List
            </a>

            @if(app(\App\Services\RbacService::class)->userHasLevel(auth()->user(), 'tasks', 'write'))
                <a href="{{ route('tasks.edit', $task->id) }}" class="px-4 py-2 bg-india-saffron text-white rounded-lg hover:bg-india-saffron/80 transition">
                    Edit Task
                </a>
            @endif

            @if(app(\App\Services\RbacService::class)->userHasLevel(auth()->user(), 'tasks', 'manage'))
                <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" onsubmit="return confirm('Are you sure you want to delete this task?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition">
                        Delete Task
                    </button>
                </form>
            @endif
        </div>
    </div>
</x-layouts.app>
```

**File:** `app/Modules/Tasks/Resources/Views/create.blade.php`

```blade
<x-layouts.app>
    <x-ui.page-header 
        title="Create Task" 
        description="Add a new task"
    />

    <div class="glass-panel rounded-xl p-8 max-w-2xl">
        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf

            <!-- Title -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-400 uppercase mb-2">Title *</label>
                <input 
                    type="text" 
                    name="title" 
                    value="{{ old('title') }}"
                    required
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-india-saffron"
                />
                @error('title')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-400 uppercase mb-2">Description</label>
                <textarea 
                    name="description" 
                    rows="4"
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-india-saffron"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-400 uppercase mb-2">Status *</label>
                <select name="status" required class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white">
                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                @error('status')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Priority -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-400 uppercase mb-2">Priority *</label>
                <select name="priority" required class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white">
                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                </select>
                @error('priority')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Due Date -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-400 uppercase mb-2">Due Date</label>
                <input 
                    type="date" 
                    name="due_date" 
                    value="{{ old('due_date') }}"
                    class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-india-saffron"
                />
                @error('due_date')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Assign To -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-slate-400 uppercase mb-2">Assign To</label>
                <select name="assigned_to" class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white">
                    <option value="">Unassigned</option>
                    @foreach($users as $user)
                        <option value="{{ $user->userid }}" {{ old('assigned_to') == $user->userid ? 'selected' : '' }}>
                            {{ $user->full_name }}
                        </option>
                    @endforeach
                </select>
                @error('assigned_to')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-india-saffron text-white rounded-lg hover:bg-india-saffron/80 transition">
                    Create Task
                </button>
                <a href="{{ route('tasks.index') }}" class="px-6 py-2 bg-white/5 text-white rounded-lg hover:bg-white/10 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-layouts.app>
```

### Step 9: Register Module in AppServiceProvider

**File:** `app/Providers/AppServiceProvider.php`

```php
public function boot(): void
{
    // Load module routes
    $this->loadModuleRoutes();
    
    // Load module views
    $this->loadModuleViews();
}

private function loadModuleRoutes(): void
{
    $modulesPath = app_path('Modules');
    
    if (is_dir($modulesPath)) {
        foreach (scandir($modulesPath) as $module) {
            $routesFile = "{$modulesPath}/{$module}/routes.php";
            if (file_exists($routesFile)) {
                require $routesFile;
            }
        }
    }
}

private function loadModuleViews(): void
{
    $modulesPath = app_path('Modules');
    
    if (is_dir($modulesPath)) {
        foreach (scandir($modulesPath) as $module) {
            $viewsPath = "{$modulesPath}/{$module}/Resources/Views";
            if (is_dir($viewsPath)) {
                $this->loadViewsFrom($viewsPath, strtolower($module));
            }
        }
    }
}
```

### Step 10: Run Migrations and Seeders

```bash
# Run migration
php artisan migrate --database=pgsql_app

# Run seeder
php artisan db:seed --class=TasksModuleSeeder
```

### Step 11: Assign Module Access to Users

Use the admin panel at `/admin/module-access` to assign Tasks module access to users, or use Tinker:

```bash
php artisan tinker
```

```php
// Give user ID 103 full access to Tasks
$user = App\Models\User::find(103);
$tasksManageLevel = DB::connection('pgsql_app')
    ->table('module_levels')
    ->join('modules', 'module_levels.module_id', '=', 'modules.id')
    ->where('modules.slug', 'tasks')
    ->where('module_levels.slug', 'manage')
    ->first();

DB::connection('pgsql_app')->table('user_module_levels')->insert([
    'user_id' => $user->userid,
    'module_level_id' => $tasksManageLevel->id,
    'created_at' => now(),
    'updated_at' => now(),
]);
```

---

## Advanced Topics

### Using External Libraries

#### Example: Adding PDF Export with DomPDF

**1. Install library:**
```bash
composer require barryvdh/laravel-dompdf
```

**2. Create service:**

**File:** `app/Modules/Tasks/Services/TaskPdfService.php`

```php
<?php

namespace App\Modules\Tasks\Services;

use App\Models\Task;
use Barryvdh\DomPDF\Facade\Pdf;

class TaskPdfService
{
    public function generateTaskPdf(Task $task)
    {
        $pdf = Pdf::loadView('tasks::pdf.task', compact('task'));
        return $pdf->download("task-{$task->id}.pdf");
    }

    public function generateTaskListPdf($tasks)
    {
        $pdf = Pdf::loadView('tasks::pdf.task-list', compact('tasks'));
        return $pdf->download('tasks-list.pdf');
    }
}
```

**3. Create PDF view:**

**File:** `app/Modules/Tasks/Resources/Views/pdf/task.blade.php`

```blade
<!DOCTYPE html>
<html>
<head>
    <title>Task: {{ $task->title }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { background: #f3f4f6; padding: 20px; }
        .content { padding: 20px; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $task->title }}</h1>
    </div>
    <div class="content">
        <p><strong>Status:</strong> <span class="badge">{{ $task->status }}</span></p>
        <p><strong>Priority:</strong> {{ $task->priority }}</p>
        <p><strong>Description:</strong></p>
        <p>{{ $task->description }}</p>
    </div>
</body>
</html>
```

**4. Add route:**

```php
// In routes.php
Route::middleware('level:read')->group(function () {
    Route::get('/{id}/pdf', function($id) {
        $task = Task::findOrFail($id);
        $service = new \App\Modules\Tasks\Services\TaskPdfService();
        return $service->generateTaskPdf($task);
    })->name('pdf');
});
```

### Adding API Endpoints

**File:** `app/Modules/Tasks/routes-api.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Tasks\Controllers\Api\TasksApiController;

Route::middleware(['auth:sanctum', 'module:tasks'])->prefix('api/tasks')->group(function () {
    Route::get('/', [TasksApiController::class, 'index']);
    Route::get('/{id}', [TasksApiController::class, 'show']);
    Route::post('/', [TasksApiController::class, 'store']);
    Route::put('/{id}', [TasksApiController::class, 'update']);
    Route::delete('/{id}', [TasksApiController::class, 'destroy']);
});
```

---

## Troubleshooting

### Common Issues

#### 1. Module not showing in dashboard

**Problem:** Module registered but not visible

**Solution:**
```bash
# Check if module is active
php artisan tinker
DB::connection('pgsql_app')->table('modules')->where('slug', 'tasks')->first();

# Check user has access
$user = App\Models\User::find(103);
$user->moduleLevels()->with('module')->get();
```

#### 2. Routes not working (404)

**Problem:** Routes return 404

**Solution:**
- Clear route cache: `php artisan route:clear`
- Check AppServiceProvider loads routes
- Verify middleware is correct

#### 3. Views not found

**Problem:** View [tasks::index] not found

**Solution:**
- Check view namespace matches module name (lowercase)
- Verify AppServiceProvider loads views
- Clear view cache: `php artisan view:clear`

#### 4. Database connection errors

**Problem:** SQLSTATE[42P01]: Undefined table

**Solution:**
```bash
# Check migration ran
php artisan migrate:status --database=pgsql_app

# Run migration
php artisan migrate --database=pgsql_app
```

#### 5. Access denied (403)

**Problem:** User gets 403 on module routes

**Solution:**
```bash
php artisan tinker

# Check user has module access
$user = App\Models\User::find(103);
$access = DB::connection('pgsql_app')
    ->table('user_module_levels')
    ->where('user_id', $user->userid)
    ->get();
```

---

## Testing Checklist

After creating your module, test:

- [ ] Browse level can view list
- [ ] Read level can view details
- [ ] Write level can create/edit
- [ ] Manage level can delete
- [ ] Users without access get 403
- [ ] Module shows in dashboard
- [ ] Sidebar link works (if added)
- [ ] All forms validate correctly
- [ ] Database queries are optimized
- [ ] Views render correctly

---

## Summary

You now have a complete, working module! Key files created:

1. ✅ Module configuration (`Module.php`)
2. ✅ Database migration
3. ✅ Model with relationships
4. ✅ Seeder for registration
5. ✅ Controller with CRUD
6. ✅ Routes with access control
7. ✅ Views (index, show, create, edit)
8. ✅ Service provider registration

Your module is now:
- Registered in database
- Protected by middleware
- Accessible based on user permissions
- Fully integrated with the eOffice system
