<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stats = [
            'total' => BankAccount::count(),
            'active' => BankAccount::where('is_active', true)->count(),
            'inactive' => BankAccount::where('is_active', false)->count(),
        ];
        return view('admin.bank_accounts.index', compact('stats'));
    }

    /**
     * Display the specified bank account with statistics.
     */
    public function show(BankAccount $bank_account)
    {
        // For now, we don't have a transfers relationship, so we just show account details
        $stats = [
            'total_count' => 0, // Placeholder for future transfer tracking
        ];

        return view('admin.bank_accounts.show', compact('bank_account', 'stats'));
    }

    /**
     * Get data for DataTables.
     */
    public function getData()
    {
        try {
            $accounts = BankAccount::all();

            $data = $accounts->map(function ($account) {
                return [
                    'id' => $account->id,
                    'bank_name' => $account->bank_name,
                    'iban' => '<code>' . $account->iban . '</code>',
                    'beneficiary_name' => $account->beneficiary_name,
                    'logo' => $account->logo_path 
                        ? '<img src="' . asset('storage/' . $account->logo_path) . '" alt="' . $account->bank_name . '" class="rounded" width="40">'
                        : '<span class="badge badge-light">No Logo</span>',
                    'status' => $account->is_active
                        ? '<span class="badge badge-success">' . __('Active') . '</span>'
                        : '<span class="badge badge-danger">' . __('Inactive') . '</span>',
                    'actions' => $this->getActionButtons($account),
                ];
            });

            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Action buttons for DataTables.
     */
    private function getActionButtons($account): string
    {
        $showBtn = '<a href="' . route('admin.bank-accounts.show', $account->id) . '" class="btn btn-info btn-sm me-1" title="' . __('View Details') . '"><i class="fas fa-eye"></i></a>';
        $editBtn = '<button class="btn btn-primary btn-sm me-1" onclick="editAccount(' . $account->id . ')" title="' . __('Edit') . '"><i class="fas fa-edit"></i></button>';
        $toggleBtn = '<button class="btn btn-' . ($account->is_active ? 'warning' : 'success') . ' btn-sm me-1" onclick="toggleAccountStatus(' . $account->id . ')" title="' . ($account->is_active ? __('Deactivate') : __('Activate')) . '"><i class="fas fa-' . ($account->is_active ? 'ban' : 'check') . '"></i></button>';
        $deleteBtn = '<button class="btn btn-danger btn-sm" onclick="deleteAccount(' . $account->id . ')" title="' . __('Delete') . '"><i class="fas fa-trash"></i></button>';

        return '<div class="d-flex">' . $showBtn . $editBtn . $toggleBtn . $deleteBtn . '</div>';
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:255',
            'iban' => 'required|string|max:255',
            'beneficiary_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['bank_name', 'iban', 'beneficiary_name']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('banks', 'public');
        }

        BankAccount::create($data);

        return response()->json([
            'success' => true,
            'message' => __('Bank account created successfully'),
        ]);
    }

    /**
     * Show data for editing.
     */
    public function edit(BankAccount $bank_account)
    {
        return response()->json([
            'success' => true,
            'account' => $bank_account,
            'logo_url' => $bank_account->logo_path ? asset('storage/' . $bank_account->logo_path) : null,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankAccount $bank_account)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:255',
            'iban' => 'required|string|max:255',
            'beneficiary_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $data = $request->only(['bank_name', 'iban', 'beneficiary_name']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('logo')) {
            if ($bank_account->logo_path) {
                Storage::disk('public')->delete($bank_account->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('banks', 'public');
        }

        $bank_account->update($data);

        return response()->json([
            'success' => true,
            'message' => __('Bank account updated successfully'),
        ]);
    }

    /**
     * Toggle status.
     */
    public function toggleActive($id)
    {
        $account = BankAccount::findOrFail($id);
        $account->update(['is_active' => !$account->is_active]);

        return response()->json([
            'success' => true,
            'message' => $account->is_active ? __('Bank account activated') : __('Bank account deactivated'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bank_account)
    {
        if ($bank_account->logo_path) {
            Storage::disk('public')->delete($bank_account->logo_path);
        }
        $bank_account->delete();

        return response()->json([
            'success' => true,
            'message' => __('Bank account deleted successfully'),
        ]);
    }
}
