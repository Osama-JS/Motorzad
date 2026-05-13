<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletTransactionController extends Controller
{
    /**
     * جلب سجل الحركات للجدول وعرض الصفحة
     */
    public function index(Request $request, Wallet $wallet)
    {
        if ($request->ajax()) {
            $query = $wallet->transactions()->latest();

            // تطبيق فلتر التاريخ إذا قام المستخدم باختياره
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
            }

            // تطبيق فلتر نوع المعاملة (All, Credit, Debit) كما يظهر بجانب حقل البحث
            if ($request->filled('type') && $request->type !== 'All') {
                $query->where('type', strtolower($request->type));
            }

            // دعم مكتبة Yajra DataTables إن كانت متوفرة
            if (function_exists('datatables')) {
                return datatables()->of($query)
                    ->addColumn('id', function($row) {
                        return $row->id;
                    })
                    ->editColumn('created_at', function($row) {
                        return $row->created_at->format('Y-m-d H:i');
                    })
                    ->editColumn('type', function($row) {
                        return '<span class="transaction-type-badge ' . ($row->type === 'credit' ? 'type-credit' : 'type-debit') . '">' . ($row->type === 'credit' ? __('Deposit') : __('Withdraw')) . '</span>';
                    })
                    ->editColumn('amount', function($row) {
                        $color = $row->type == 'credit' ? 'text-success' : 'text-danger';
                        $sign = $row->type == 'credit' ? '+' : '-';
                        return "<span class='{$color} font-weight-bold'>{$sign} " . number_format($row->amount, 2) . "</span>";
                    })
                    ->addColumn('description', function($row) {
                        return $row->description ?: '---';
                    })
                    ->addColumn('created_by', function($row) {
                        return $row->creator ? $row->creator->name : __('System');
                    })
                    ->addColumn('attachment', function($row) {
                        return $row->attachment_path 
                            ? '<a href="' . asset('storage/' . $row->attachment_path) . '" target="_blank" class="btn btn-sm btn-ghost"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg></a>'
                            : '---';
                    })
                    ->rawColumns(['type', 'amount', 'attachment'])
                    ->make(true);
            }

            // في حال عدم توفر مكتبة Yajra، يتم إرجاع البيانات بتنسيق JSON متوافق مع DataTables
            $transactions = $query->with('creator')->get();
            return response()->json([
                'data' => $transactions->map(function($row) {
                    $color = $row->type == 'credit' ? 'text-success' : 'text-danger';
                    $sign = $row->type == 'credit' ? '+' : '-';
                    $amountHtml = "<span class='{$color} font-weight-bold'>{$sign} " . number_format($row->amount, 2) . "</span>";
                    
                    $typeHtml = '<span class="transaction-type-badge ' . ($row->type === 'credit' ? 'type-credit' : 'type-debit') . '">' . ($row->type === 'credit' ? __('Deposit') : __('Withdraw')) . '</span>';
                    
                    $creatorName = $row->creator ? $row->creator->name : __('System');
                    
                    $attachmentHtml = $row->attachment_path 
                        ? '<a href="' . asset('storage/' . $row->attachment_path) . '" target="_blank" class="btn btn-sm btn-ghost"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg></a>'
                        : '---';

                    return [
                        'id' => $row->id,
                        'created_at' => $row->created_at->format('Y-m-d H:i'),
                        'type' => $typeHtml,
                        'amount' => $amountHtml,
                        'description' => $row->description ?: '---',
                        'created_by' => $creatorName,
                        'attachment' => $attachmentHtml,
                    ];
                })
            ]);
        }

        // تحميل العلاقات الضرورية لعرض الصفحة بشكل احترافي
        $wallet->load('user');
        return view('admin.wallets.transactions', compact('wallet'));
    }

    /**
     * حفظ معاملة جديدة من النافذة المنبثقة
     */
    public function store(Request $request, Wallet $wallet)
    {
        // 1. فحص البيانات (Validation) بناءً على قيود الواجهة
        $validated = $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'type'           => 'required|in:credit,debit', // إيداع أو سحب
            'description'    => 'nullable|string|max:500',
            'attachment'     => 'nullable|file|mimes:jpeg,png,webp,pdf|max:10240', // Max 10MB
            'maturity_time'  => 'nullable|date',
            'payment_method' => 'nullable|string|max:255',
        ]);

        // 2. معالجة الملف المرفق (إن وجد)
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('transactions/attachments', 'public');
        }

        try {
            // 3. استخدام DB Transaction لضمان الأمان المالي
            DB::transaction(function () use ($validated, $wallet, $attachmentPath, $request) {
                
                // التأكد من عدم تجاوز سقف الدين عند السحب
                if ($validated['type'] === 'debit') {
                    $availableBalance = $wallet->balance + $wallet->debt_ceiling;
                    if ($validated['amount'] > $availableBalance) {
                        throw new \Exception('المبلغ يتجاوز الرصيد المتاح وسقف الدين.');
                    }
                }

                // أ. إنشاء سجل المعاملة
                $wallet->transactions()->create([
                    'type'            => $validated['type'],
                    'amount'          => $validated['amount'],
                    'description'     => $validated['description'],
                    'attachment_path' => $attachmentPath,
                    'maturity_time'   => $validated['maturity_time'] ?? null,
                    'payment_method'  => $validated['payment_method'] ?? null,
                    'created_by'      => auth()->id(), // من قام بالعملية
                ]);

                // ب. تحديث رصيد المحفظة الفعلي والإحصائيات
                if ($validated['type'] === 'credit') {
                    $wallet->increment('balance', $validated['amount']);
                    $wallet->increment('total_deposits', $validated['amount']);
                } else {
                    $wallet->decrement('balance', $validated['amount']);
                    $wallet->increment('total_withdrawals', $validated['amount']);
                }

                // تحديث نسبة استهلاك الدين تلقائياً
                if ($wallet->debt_ceiling > 0 && $wallet->balance < 0) {
                    $usage = (abs($wallet->balance) / $wallet->debt_ceiling) * 100;
                    $wallet->update(['debt_usage' => min($usage, 100)]);
                } else {
                    $wallet->update(['debt_usage' => 0]);
                }
            });

            // إرسال رد ناجح للواجهة الأمامية مع تحديثات الأرقام العلوية
            return response()->json([
                'status' => 'success',
                'message' => 'تمت إضافة المعاملة بنجاح وتحديث الرصيد.',
                'wallet' => [
                    'balance' => number_format($wallet->balance, 2),
                    'total_deposits' => number_format($wallet->total_deposits, 2),
                    'total_withdrawals' => number_format($wallet->total_withdrawals, 2),
                    'debt_usage' => $wallet->debt_usage,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * جلب بيانات معاملة محددة لعرضها في نموذج التعديل
     */
    public function edit(Wallet $wallet, WalletTransaction $transaction)
    {
        // التحقق من أن المعاملة تتبع المحفظة المطلوبة
        if ($transaction->wallet_id !== $wallet->id) {
            return response()->json(['error' => 'المعاملة غير موجودة في هذه المحفظة.'], 404);
        }

        return response()->json($transaction);
    }

    /**
     * تحديث بيانات المعاملة المالية مع ضبط التأثير المالي على الرصيد
     */
    public function update(Request $request, Wallet $wallet, WalletTransaction $transaction)
    {
        // التحقق من أن المعاملة تتبع المحفظة المطلوبة
        if ($transaction->wallet_id !== $wallet->id) {
            return response()->json(['error' => 'المعاملة غير موجودة في هذه المحفظة.'], 404);
        }

        $validated = $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'type'           => 'required|in:credit,debit',
            'description'    => 'nullable|string|max:500',
            'attachment'     => 'nullable|file|mimes:jpeg,png,webp,pdf|max:10240',
            'maturity_time'  => 'nullable|date',
            'payment_method' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($validated, $wallet, $transaction, $request) {
                // 1. التراجع عن التأثير المالي القديم للمعاملة
                $oldAmount = $transaction->amount;
                $oldType   = $transaction->type;

                if ($oldType === 'credit') {
                    $wallet->balance -= $oldAmount;
                    $wallet->total_deposits -= $oldAmount;
                } else {
                    $wallet->balance += $oldAmount;
                    $wallet->total_withdrawals -= $oldAmount;
                }

                // 2. التحقق من سقف الدين مع القيمة الجديدة في حالة السحب
                if ($validated['type'] === 'debit') {
                    $availableBalance = $wallet->balance + $wallet->debt_ceiling;
                    if ($validated['amount'] > $availableBalance) {
                        throw new \Exception('المبلغ المعدل يتجاوز الرصيد المتاح وسقف الدين.');
                    }
                }

                // 3. معالجة الملف المرفق الجديد إن وجد
                $attachmentPath = $transaction->attachment_path;
                if ($request->hasFile('attachment')) {
                    $attachmentPath = $request->file('attachment')->store('transactions/attachments', 'public');
                }

                // 4. تطبيق التأثير المالي الجديد على المحفظة
                $newAmount = $validated['amount'];
                $newType   = $validated['type'];

                if ($newType === 'credit') {
                    $wallet->balance += $newAmount;
                    $wallet->total_deposits += $newAmount;
                } else {
                    $wallet->balance -= $newAmount;
                    $wallet->total_withdrawals += $newAmount;
                }

                // حفظ التغييرات في المحفظة
                $wallet->save();

                // 5. تحديث سجل المعاملة نفسه
                $transaction->update([
                    'type'            => $newType,
                    'amount'          => $newAmount,
                    'description'     => $validated['description'],
                    'attachment_path' => $attachmentPath,
                    'maturity_time'   => $validated['maturity_time'] ?? null,
                    'payment_method'  => $validated['payment_method'] ?? null,
                ]);

                // 6. تحديث نسبة استهلاك الدين
                if ($wallet->debt_ceiling > 0 && $wallet->balance < 0) {
                    $usage = (abs($wallet->balance) / $wallet->debt_ceiling) * 100;
                    $wallet->update(['debt_usage' => min($usage, 100)]);
                } else {
                    $wallet->update(['debt_usage' => 0]);
                }
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'تم تعديل المعاملة المالية بنجاح وتحديث الأرصدة.',
                'wallet'  => [
                    'balance'           => number_format($wallet->balance, 2),
                    'total_deposits'    => number_format($wallet->total_deposits, 2),
                    'total_withdrawals' => number_format($wallet->total_withdrawals, 2),
                    'debt_usage'        => $wallet->debt_usage,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
