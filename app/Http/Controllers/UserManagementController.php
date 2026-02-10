<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $query = User::with('systemRoles');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('username', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('full_name', 'like', "%{$search}%");
        }

        $users = $query->paginate(15);

        // Get admin system role
        $adminSystemRole = \DB::connection('pgsql_app')
            ->table('system_roles')
            ->where('slug', 'admin')
            ->first();

        return view('admin.users.index', compact('users', 'adminSystemRole'));
    }

    public function toggleAdmin(Request $request, $id)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $user = User::findOrFail($id);

        // Get admin system role (not regular role)
        $adminSystemRole = \DB::connection('pgsql_app')
            ->table('system_roles')
            ->where('slug', 'admin')
            ->first();

        if (!$adminSystemRole) {
            return back()->with('error', 'Admin system role not found.');
        }

        // Use DB facade to avoid cross-database connection issues
        $hasAdminRole = \DB::connection('pgsql_app')
            ->table('system_role_user')
            ->where('user_id', $id)
            ->where('system_role_id', $adminSystemRole->id)
            ->exists();

        if ($hasAdminRole) {
            // Remove admin role
            \DB::connection('pgsql_app')
                ->table('system_role_user')
                ->where('user_id', $id)
                ->where('system_role_id', $adminSystemRole->id)
                ->delete();
            $message = "Admin rights revoked for {$user->username}.";
        } else {
            // Add admin role
            \DB::connection('pgsql_app')
                ->table('system_role_user')
                ->insert([
                    'user_id' => $id,
                    'system_role_id' => $adminSystemRole->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            $message = "Admin rights granted to {$user->username}.";
        }

        // Invalidate permission cache
        app(\App\Services\RbacService::class)->clearUserCache($id);

        return back()->with('success', $message);
    }
}
