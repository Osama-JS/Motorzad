<?php

namespace App\Http\Controllers\Bidder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FormTemplate;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SellerSubscriptionController extends Controller
{
    /**
     * Display the become a seller page with dynamic form.
     */
    public function index()
    {
        $user = auth()->user();
        
        // If they are already a seller
        $isSeller = $user->hasRole('seller');
        
        $pendingRequest = \App\Models\SellerRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'under_review'])
            ->first();
            
        $rejectedRequest = \App\Models\SellerRequest::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->latest()
            ->first();

        $draftRequest = \App\Models\SellerRequest::where('user_id', $user->id)
            ->where('status', 'draft')
            ->first();

        // Fetch Dynamic Form Template
        $templateId = Setting::get('seller_request_template_id');
        $template = null;
        
        if ($templateId) {
            $template = FormTemplate::with('fields')->where('id', $templateId)->where('is_active', true)->first();
        }

        // Fallback: If no template specified or found, get the oldest active template
        if (!$template) {
            $template = FormTemplate::with('fields')->where('is_active', true)->orderBy('id', 'asc')->first();
        }
        
        // Action Required Request
        $actionRequiredRequest = \App\Models\SellerRequest::where('user_id', $user->id)
            ->where('status', 'action_required')
            ->first();

        return view('bidder.seller-subscription.index', compact('user', 'isSeller', 'pendingRequest', 'rejectedRequest', 'draftRequest', 'actionRequiredRequest', 'template'));
    }

    /**
     * Handle the upgrade request and dynamic data.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // 1. Check if they are already a seller
        if ($user->hasRole('seller')) {
            return redirect()->back()->with('success', __('You are already a seller.'));
        }

        // 2. Check if they have completed KYC
        if ($user->status !== 'approved') {
            return redirect()->route('kyc.index')
                ->with('error', __('Please complete identity verification to become a seller.'));
        }
        
        // 3. Prevent Double Submit / Race Condition
        $lock = \Illuminate\Support\Facades\Cache::lock('seller_subscribe_web_' . $user->id, 5);
        if (!$lock->get()) {
            return redirect()->back()->with('error', __('Your request is already being processed. Please wait.'));
        }

        // 4. Check if there is already a pending or under review request
        $pendingRequest = \App\Models\SellerRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'under_review'])
            ->first();
            
        if ($pendingRequest) {
            $lock->release();
            return redirect()->back()->with('info', __('Your request to become a seller is already pending approval.'));
        }

        // 5. Dynamic Validation based on Template
        $templateId = $request->input('template_id');
        $template = FormTemplate::with('fields')->where('is_active', true)->find($templateId);

        if (!$template) {
            $lock->release();
            return redirect()->back()->with('error', __('Form template not found or is currently inactive.'));
        }

        $existingRequest = \App\Models\SellerRequest::where('user_id', $user->id)
            ->whereIn('status', ['draft', 'action_required'])
            ->first();
        $oldData = $existingRequest ? ($existingRequest->data ?? []) : [];

        $rules = [];
        $messages = [];
        $submittedData = $request->input('data', []);

        foreach ($template->fields as $field) {
            if (in_array($field->type, ['html', 'step-divider'])) {
                continue;
            }

            // Check conditional logic: if hidden, skip validation and don't save it
            if (!empty($field->depends_on_field_name)) {
                $actualValue = $submittedData[$field->depends_on_field_name] ?? null;
                if (is_array($actualValue) && in_array($field->depends_on_value, $actualValue)) {
                    // if actual value is an array (multi-select) and contains the condition value
                } else if (!is_array($actualValue) && $actualValue == $field->depends_on_value) {
                    // condition met
                } else {
                    continue; // Skip this field completely
                }
            }

            $ruleSet = [];
            $isDraft = $request->boolean('is_draft');
            $hasOldFile = $field->type === 'file' && !empty($oldData[$field->name]);
            $isNewFileUpload = $field->type === 'file' && $request->hasFile("data.{$field->name}");
            
            if ($field->is_required && !$isDraft) {
                if ($field->type === 'file') {
                    if (!$hasOldFile && !$isNewFileUpload) {
                        $ruleSet[] = 'required';
                    }
                } else {
                    $ruleSet[] = 'required';
                }
                $messages["data.{$field->name}.required"] = __('الحقل :label مطلوب.', ['label' => $field->label]);
            } else {
                $ruleSet[] = 'nullable';
            }

            // Type specific rules
            if ($field->type === 'file') {
                if ($isNewFileUpload) {
                    $ruleSet[] = 'file';
                    $ruleSet[] = 'max:10240'; // 10MB max
                    $ruleSet[] = 'mimes:jpeg,png,jpg,pdf';
                    $messages["data.{$field->name}.mimes"] = __('يجب أن يكون الملف من نوع صورة أو PDF.');
                    $messages["data.{$field->name}.max"] = __('حجم الملف يجب ألا يتجاوز 10 ميجابايت.');
                }
            } elseif ($field->type === 'checkbox') {
                $ruleSet[] = 'boolean';
            } elseif ($field->type === 'multi-select') {
                $ruleSet[] = 'array';
                if (is_array($field->options)) {
                    $rules["data.{$field->name}.*"] = ['string', \Illuminate\Validation\Rule::in($field->options)];
                }
            } else {
                $ruleSet[] = 'string';
            }

            $rules["data.{$field->name}"] = $ruleSet;
        }

        // Validate the incoming request
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);
        
        if ($validator->fails()) {
            if (isset($lock)) $lock->release();
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();
        
        // 6. Handle File Uploads and Prepare JSON Data

        $finalData = [];
        if (isset($validatedData['data']) && is_array($validatedData['data'])) {
            foreach ($validatedData['data'] as $key => $value) {
                // Check if the value is an uploaded file
                if ($request->hasFile("data.{$key}")) {
                    $file = $request->file("data.{$key}");
                    $fileName = 'seller_req_' . $user->id . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    // Store file in public disk (storage/app/public/seller_requests)
                    $path = $file->storeAs('seller_requests', $fileName, 'public');
                    // Store the path string instead of the file object
                    $finalData[$key] = '/storage/' . $path;
                    
                    // Delete old file if exists
                    if (!empty($oldData[$key])) {
                        $oldPath = str_replace('/storage/', '', $oldData[$key]);
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                    }
                } else {
                    // Preserve old file path if no new file is uploaded
                    $fieldObj = collect($template->fields)->firstWhere('name', $key);
                    if ($fieldObj && $fieldObj->type === 'file' && empty($value) && !empty($oldData[$key])) {
                        $finalData[$key] = $oldData[$key];
                    } elseif (is_array($value)) {
                        // Handle multi-select array
                        $finalData[$key] = $value;
                    } else {
                        // For checkbox or regular text
                        $finalData[$key] = $value;
                    }
                }
            }
        }

        // 7. Create or Update the Request in Database
        $isDraft = $request->boolean('is_draft');
        
        if ($existingRequest) {
            $existingRequest->update([
                'form_template_id' => $template->id,
                'data' => $finalData,
                'status' => $isDraft ? 'draft' : 'pending',
                'admin_notes' => null
            ]);
        } else {
            \App\Models\SellerRequest::create([
                'user_id' => $user->id,
                'form_template_id' => $template->id,
                'data' => $finalData,
                'status' => $isDraft ? 'draft' : 'pending'
            ]);
        }

        if (isset($lock)) $lock->release();

        if ($isDraft) {
            return redirect()->back()->with('success', __('تم حفظ طلبك كمسودة بنجاح. يمكنك العودة لإكماله لاحقاً.'));
        }

        return redirect()->back()->with('success', __('Your request to become a seller has been submitted successfully and is awaiting admin approval.'));
    }
}
