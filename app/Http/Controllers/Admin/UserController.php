<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => User::where('is_deleted', false)->count(),
            'approved' => User::where('is_deleted', false)->where('status', 'approved')->count(),
            'pending' => User::where('is_deleted', false)->where('status', 'pending')->count(),
            'rejected' => User::where('is_deleted', false)->where('status', 'rejected')->count(),
            'unverified' => User::where('is_deleted', false)->whereNull('email_verified_at')->count(),
        ];
        
        $roles = Role::all();
        return view('admin.users.index', compact('stats', 'roles'));
    }

    /**
     * Get users for Custom AJAX Table.
     */
    public function getData(Request $request)
    {
        $query = User::with(['roles', 'latestKycRequest'])->where('is_deleted', false);

        // Filtering
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $role = $request->role;
            $query->whereHas('roles', function($q) use ($role) {
                $q->where('name', $role);
            });
        }

        $perPage = $request->input('per_page', 10);
        $users = $query->latest()->paginate($perPage);

        $data = $users->map(function($user) {
            if ($user->status === 'approved') {
                $statusBadge = '<span class="badge badge-success">'.__("Approved").' ✅</span>';
            } elseif ($user->status === 'rejected') {
                $statusBadge = '<span class="badge badge-danger">'.__("Rejected").' ❌</span>';
            } else {
                $statusBadge = '<span class="badge badge-warning text-dark">'.__("Pending").' ⏳</span>';
            }

            $kycLevelBadge = '<span class="badge badge-info">Level ' . $user->kyc_level . '</span>';

            $verifiedBadge = $user->email_verified_at
                ? '<span class="badge badge-success">'.__("Verified").'</span>'
                : '<span class="badge badge-warning text-dark">'.__("Unverified").'</span>';

            $rolesHtml = '';
            foreach($user->roles as $role) {
                $rolesHtml .= '<span class="badge badge-primary me-1">'.$role->name.'</span>';
            }
            if(empty($rolesHtml)) $rolesHtml = '<span class="text-muted">'.__("No Role").'</span>';

            $approveBtn = $user->status !== 'approved' 
                ? '<li><a class="dropdown-item text-success" href="#" onclick="updateUserStatus(' . $user->id . ', \'approved\')"><svg width="16" height="16" class="me-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> '.__("Approve").'</a></li>' 
                : '';
                
            $rejectBtn = $user->status !== 'rejected' 
                ? '<li><a class="dropdown-item text-danger" href="#" onclick="updateUserStatus(' . $user->id . ', \'rejected\')"><svg width="16" height="16" class="me-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> '.__("Reject").'</a></li>' 
                : '';

            $actions = '
                <div class="dropdown">
                    <button class="btn btn-sm btn-light dropdown-toggle action-btn-kebab" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" style="border:none; background:transparent;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><a class="dropdown-item text-primary" href="#" onclick="viewUser(' . $user->id . ')"><svg width="16" height="16" class="me-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> '.__("View Profile").'</a></li>
                        <li><a class="dropdown-item text-info" href="#" onclick="editUser(' . $user->id . ')"><svg width="16" height="16" class="me-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> '.__("Edit User").'</a></li>
                        '.$approveBtn.'
                        '.$rejectBtn.'
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteUser(' . $user->id . ')"><svg width="16" height="16" class="me-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg> '.__("Delete").'</a></li>
                    </ul>
                </div>';

            return [
                'id' => $user->id,
                'photo' => '<img src="' . $user->profile_photo_url . '" class="rounded-lg me-2" width="40" height="40" style="border-radius:50%; object-fit: cover;" alt="">',
                'info' => '<div style="display:inline-block; vertical-align:middle;">
                            <strong>' . $user->full_name . '</strong><br>
                            <small class="text-muted">' . $user->email . '</small>
                        </div>',
                'phone' => '<span dir="ltr" class="d-inline-block text-start">' . ($user->country_code ? $user->country_code . ' ' : '') . ($user->phone ?? '---') . '</span>',
                'roles' => $rolesHtml,
                'kyc_level' => $kycLevelBadge,
                'status' => $statusBadge,
                'verified' => $verifiedBadge,
                'actions' => $actions
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'total' => $users->total(),
                'links' => $users->linkCollection()->toArray()
            ]
        ]);
    }

    public function show(Request $request, User $user)
    {
        $kycRequest = $user->latestKycRequest;
        $wallet = $user->wallet;
        $stats = [
            'wallet_balance' => $wallet ? number_format($wallet->balance, 2) : '0.00',
            'total_deposits' => $wallet ? number_format($wallet->total_deposits, 2) : '0.00',
            'total_withdrawals' => $wallet ? number_format($wallet->total_withdrawals, 2) : '0.00',
            'kyc_count' => $user->kycRequests()->count(),
            'deposit_requests_count' => $user->depositRequests()->count(),
            'withdrawal_requests_count' => $user->withdrawalRequests()->count(),
            'auctions_created' => \App\Models\Auction::where('created_by', $user->id)->count(),
            'auctions_won' => \App\Models\Auction::where('winner_id', $user->id)->count(),
            'bids_count' => \App\Models\Bid::where('user_id', $user->id)->count(),
        ];
        
        return response()->json([
            'success' => true,
            'user' => $user,
            'roles' => $user->roles->pluck('name')->toArray(),
            'photo_url' => $user->profile_photo_url,
            'kyc_request' => $kycRequest ? [
                'full_name' => $kycRequest->full_name,
                'country' => $kycRequest->country,
                'id_number' => $kycRequest->id_number,
                'id_image_url' => asset('storage/' . $kycRequest->id_image),
                'selfie_image_url' => asset('storage/' . $kycRequest->selfie_image),
                'status' => $kycRequest->status,
                'admin_note' => $kycRequest->admin_note,
            ] : null,
            'stats' => $stats,
            'created_at' => $user->created_at->format('Y-m-d H:i')
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'nullable|string|unique:users,phone',
            'country_code' => 'nullable|string|max:10',
            'password'     => 'required|min:8',
            'status'       => 'required|in:approved,pending,rejected',
            'kyc_level'    => 'required|integer|min:0|max:3',
            'country'      => 'nullable|string|max:100',
            'city'         => 'nullable|string|max:100',
            'address'      => 'nullable|string|max:500',
            'gender'       => 'nullable|in:male,female',
            'date_of_birth'=> 'nullable|date',
            'id_number'    => 'nullable|string|max:50|unique:users,id_number',
            'roles'        => 'array'
        ]);

        $validated['password'] = Hash::make($request->password);
        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        $user = User::create($validated);

        $roles = array_filter($request->roles ?? []);
        if (!empty($roles)) {
            $user->syncRoles($roles);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المستخدم بنجاح'
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', Rule::unique('users')->ignore($user->id)],
            'country_code' => 'nullable|string|max:10',
            'password' => 'nullable|min:8',
            'status' => 'required|in:approved,pending,rejected',
            'kyc_level' => 'required|integer|min:0|max:3',
            'country'      => 'nullable|string|max:100',
            'city'         => 'nullable|string|max:100',
            'address'      => 'nullable|string|max:500',
            'gender'       => 'nullable|in:male,female',
            'date_of_birth'=> 'nullable|date',
            'id_number' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'roles' => 'array'
        ]);

        $data = $request->only(['first_name', 'last_name', 'email', 'phone', 'country_code', 'status', 'kyc_level', 'country', 'city', 'address', 'gender', 'date_of_birth', 'id_number']);
        $data['name'] = $request->first_name . ' ' . $request->last_name;

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $roles = array_filter($request->roles ?? []);
        $user->syncRoles($roles);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات المستخدم بنجاح'
        ]);
    }

    public function updateStatus(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:approved,pending,rejected',
            'note' => 'nullable|string'
        ]);

        $newStatus = $request->status;
        $user->update([
            'status' => $newStatus,
            'kyc_level' => ($newStatus === 'approved' ? 3 : ($newStatus === 'rejected' ? 1 : $user->kyc_level))
        ]);

        // Update the latest KYC request as well
        $kyc = $user->latestKycRequest;
        if ($kyc) {
            $kyc->update([
                'status' => $newStatus,
                'admin_note' => $request->note,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now()
            ]);
        }

        if ($newStatus === 'approved') {
            \Illuminate\Support\Facades\Mail::raw('يسعدنا إخبارك بأنه تم قبول طلب التحقق (KYC) الخاص بك بنجاح. حسابك الآن موثق بالكامل ويمكنك استخدام كافة مميزات المنصة.', function ($message) use ($user) {
                $message->to($user->email)->subject('تم قبول توثيق حسابك ✅ - موتورزاد');
            });
        } elseif ($newStatus === 'rejected') {
            $note = $request->note ? "\nسبب الرفض: " . $request->note : "";
            \Illuminate\Support\Facades\Mail::raw('نأسف لإخبارك بأنه تم رفض طلب التحقق (KYC) الخاص بك.' . $note . "\nيرجى إعادة رفع المستندات بشكل أوضح.", function ($message) use ($user) {
                $message->to($user->email)->subject('تم رفض توثيق حسابك ❌ - موتورزاد');
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة التحقق بنجاح وإرسال إشعار للمستخدم.',
            'status' => $newStatus
        ]);
    }

    public function verify(User $user)
    {
        $user->update([
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم توثيق حساب المستخدم بنجاح'
        ]);
    }

    public function verifyIdentity(User $user)
    {
        $user->update([
            'identity_verified_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم توثيق هوية المستخدم بنجاح'
        ]);
    }

    public function destroy(User $user)
    {
        try {
            // Check wallet balance
            if ($user->wallet && $user->wallet->balance > 0) {
                return response()->json([
                    'success' => false,
                    'message' => __('Cannot delete user because they have a non-zero wallet balance.')
                ], 400);
            }

            // Check active auctions
            $hasActiveAuctions = \App\Models\Auction::where('created_by', $user->id)
                ->whereIn('status', ['live', 'scheduled'])
                ->exists();
            if ($hasActiveAuctions) {
                return response()->json([
                    'success' => false,
                    'message' => __('Cannot delete user because they have active or scheduled auctions.')
                ], 400);
            }

            // Check active bids
            $hasActiveBids = \App\Models\Bid::where('user_id', $user->id)
                ->active()
                ->exists();
            if ($hasActiveBids) {
                return response()->json([
                    'success' => false,
                    'message' => __('Cannot delete user because they have active bids on auctions.')
                ], 400);
            }

            $user->update(['is_deleted' => true]);

            return response()->json([
                'success' => true,
                'message' => __('User moved to trash successfully (associated data will remain visible).')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while attempting deletion: ') . $e->getMessage()
            ], 500);
        }
    }
}
