<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GuestUser;

class GuestUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $guests = GuestUser::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $guests->where(function ($q) use ($search) {
                $q->where('username', 'ILIKE', "%{$search}%")
                    ->orWhere('email', 'ILIKE', "%{$search}%");
            });
        }

        $guests = $guests->paginate(20);
        $guests->appends(request()->query());

        foreach ($guests as $guest) {
            $guest->moduleAccess = $guest->moduleLevels()->with(['module', 'level'])->get();
        }


        return view('admin.guest-users.index', compact('guests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.guest-users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:pgsql_app.guest_users,username',
            'email' => 'required|string|email|max:255|unique:pgsql_app.guest_users,email',
            'mobile' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        $validated['is_active'] = $request->has('is_active');
        
        GuestUser::create($validated);
        
        return redirect()->route('admin.guest-users.index')
            ->with('success', 'Guest user created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $guestUser = GuestUser::findOrFail($id);
        return view('admin.guest-users.edit', compact('guestUser'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $guestUser = GuestUser::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required','string','max:255', \Illuminate\Validation\Rule::unique('pgsql_app.guest_users')->ignore($guestUser->id)],
            'email' => ['required','string','email','max:255', \Illuminate\Validation\Rule::unique('pgsql_app.guest_users')->ignore($guestUser->id)],
            'mobile' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        if (!empty($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        $validated['is_active'] = $request->has('is_active');
        
        $guestUser->update($validated);
        
        return redirect()->route('admin.guest-users.index')
            ->with('success', 'Guest user updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $guestUser = GuestUser::findOrFail($id);
        $guestUser->delete();
        return redirect()->route('admin.guest-users.index')
            ->with('success', 'Guest user deleted successfully.');
    }

    public function toggleStatus(Request $request, string $id)
    {
        $guestUser = GuestUser::findOrFail($id);
        $guestUser->is_active = !$guestUser->is_active;

        //also reset created at to now so that it appears on top of the list when active
        if ($guestUser->is_active) {
            $guestUser->created_at = now();
        }
        
        $guestUser->save();
        return redirect()->route('admin.guest-users.index')
            ->with('success', 'Guest user status updated successfully.');
    }
}
