<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Roles; // singular, bukan Roles

class MasterDataController extends Controller
{
    public function index(Request $r)
    {
        // Jika ini request JSON/AJAX -> kirim data tabel (API)
        if ($r->wantsJson() || $r->ajax() || $r->header('Accept') === 'application/json') {
            $perPage = min(max((int)$r->integer('per_page', 10), 5), 100);
            $q       = trim((string)$r->query('q', ''));
            $status  = $r->query('status');
            $roleId  = $r->query('role_id');

            $users = User::with('role:id,name')
                ->select(['id','name','username','email','role_id','status','created_at'])
                ->when($q, function($qq) use ($q) {
                    $qq->where(function($w) use ($q){
                        $w->where('name','like',"{$q}%")
                          ->orWhere('email','like',"{$q}%");
                    });
                })
                ->when($status, fn($qq)=>$qq->where('status',$status))
                ->when($roleId, fn($qq)=>$qq->where('role_id',$roleId))
                ->latest()
                ->paginate($perPage)
                ->appends($r->query());

            return response()->json([
                'data' => $users->getCollection()->map(function($u){
                    return [
                        'id'         => $u->id,
                        'name'       => $u->name,
                        'email'       => $u->email,
                        'role'       => $u->role->name ?? '-',
                        'status'     => $u->status,
                        'created_at' => optional($u->created_at)->toDateTimeString(),
                    ];
                }),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'per_page'     => $users->perPage(),
                    'total'        => $users->total(),
                    'last_page'    => $users->lastPage(),
                ],
            ]);
        }

        // Kalau bukan request JSON -> render Blade (title tetap ada)
        $roles = Roles::select('id','name')->orderBy('name')->get();

        return view('users', [
            'title' => 'Users & Role Management',
            'roles' => $roles,
        ]);
    }

    public function wl_management()
    {
        
    }

    public function pi_master()
    {

    }

    public function sc()
    {

    }

    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Ambil status baru dari query atau request body
        $status = $request->input('status') ?? $request->status ?? null;

        // Validasi status agar hanya bisa nilai tertentu
        if (!in_array($status, ['active', 'inactive', 'suspend', 'pending'])) {
            return response()->json(['message' => 'Invalid status'], 400);
        }

        // Update status
        $user->status = $status;
        $user->save();

        return response()->json([
            'message' => "Status user berhasil diubah menjadi {$status}.",
            'user' => $user
        ]);
    }

    public function show(\App\Models\User $user)
    {
        return response()->json([
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'role_id'    => $user->role_id,
            'role'       => $user->role->name ?? '-',
            'status'     => $user->status,
            'created_at' => optional($user->created_at)->toDateTimeString()
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->role_id = $request->role_id;
        $user->save();

        return response()->json(['message' => 'User role updated successfully']);
    }

}

