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
            'total' => User::count(),
            'approved' => User::where('status', 'approved')->count(),
            'pending' => User::where('status', 'pending')->count(),
            'rejected' => User::where('status', 'rejected')->count(),
            'unverified' => User::whereNull('email_verified_at')->count(),
        ];
        
        $roles = Role::all();
        return view('admin.users.index', compact('stats', 'roles'));
    }

    /**
     * Get users for DataTables.
     */
    public function getData(Request $request)
    {
        $users = User::with(['roles', 'latestKycRequest'])->get();

        return response()->json([
            'data' => $users->map(function($user) {
                if ($user->status === 'approved') {
                    $statusBadge = '<span class="badge badge-success">مقبول ✅</span>';
                } elseif ($user->status === 'rejected') {
                    $statusBadge = '<span class="badge badge-danger">مرفوض ❌</span>';
                } else {
                    $statusBadge = '<span class="badge badge-warning">بانتظار التحقق ⏳</span>';
                }

                $kycLevelBadge = '<span class="badge badge-info">Level ' . $user->kyc_level . '</span>';

                $verifiedBadge = $user->email_verified_at
                    ? '<span class="badge badge-success">موثق</span>'
                    : '<span class="badge badge-warning">غير موثق</span>';

                $rolesHtml = '';
                foreach($user->roles as $role) {
                    $rolesHtml .= '<span class="badge badge-primary me-1">'.$role->name.'</span>';
                }
                if(empty($rolesHtml)) $rolesHtml = '<span class="text-muted">بدون دور</span>';

                return [
                    'id' => $user->id,
                    'photo' => '<img src="' . $user->profile_photo_url . '" class="rounded-lg me-2" width="35" style="border-radius:50%;" alt="">',
                    'info' => '<div style="display:inline-block; vertical-align:middle;">
                                <strong>' . $user->full_name . '</strong><br>
                                <small style="color:var(--text-muted);">' . $user->email . '</small>
                            </div>',
                    'phone' => ($user->country_code ? $user->country_code . ' ' : '') . ($user->phone ?? '---'),
                    'roles' => $rolesHtml,
                    'kyc_level' => $kycLevelBadge,
                    'status' => $statusBadge,
                    'verified' => $verifiedBadge,
                    'actions' => '
                        <div class="actions-cell" style="display:flex; gap:5px; justify-content:center;">
                            <button onclick="viewUser(' . $user->id . ')" class="btn-icon-only info" title="عرض الملف" style="background:#3b82f6; color:white; border:none; padding:5px 8px; border-radius:4px; cursor:pointer;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                            <button onclick="editUser(' . $user->id . ')" class="btn-icon-only edit" title="تعديل" style="background:var(--primary); color:white; border:none; padding:5px 8px; border-radius:4px; cursor:pointer;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                            ' . ($user->status !== 'approved' ? '<button onclick="updateUserStatus(' . $user->id . ', \'approved\')" class="btn-icon-only success" title="قبول" style="background:#10b981; color:white; border:none; padding:5px 8px; border-radius:4px; cursor:pointer;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></button>' : '') . '
                            ' . ($user->status !== 'rejected' ? '<button onclick="updateUserStatus(' . $user->id . ', \'rejected\')" class="btn-icon-only danger" title="رفض" style="background:#ef4444; color:white; border:none; padding:5px 8px; border-radius:4px; cursor:pointer;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>' : '') . '
                            <button onclick="deleteUser(' . $user->id . ')" class="btn-icon-only delete" title="حذف" style="background:#ef4444; color:white; border:none; padding:5px 8px; border-radius:4px; cursor:pointer;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>
                        </div>'
                ];
            })
        ]);
    }

    public function show(Request $request, User $user)
    {
        $kycRequest = $user->latestKycRequest;
        
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
            if ($user->profile_photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_photo);
            }
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المستخدم بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في حذف المستخدم'
            ], 500);
        }
    }
}
