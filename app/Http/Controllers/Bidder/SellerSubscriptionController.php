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
            ->where('status', 'pending')
            ->first();
            
        $rejectedRequest = \App\Models\SellerRequest::where('user_id', $user->id)
            ->where('status', 'rejected')
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
        
        return view('bidder.seller-subscription.index', compact('user', 'isSeller', 'pendingRequest', 'rejectedRequest', 'template'));
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
        
        // 3. Check if there is already a pending request
        $pendingRequest = \App\Models\SellerRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();
            
        if ($pendingRequest) {
            return redirect()->back()->with('info', __('Your request to become a seller is already pending approval.'));
        }

        // 4. Dynamic Validation based on Template
        $templateId = $request->input('template_id');
        $template = FormTemplate::with('fields')->find($templateId);

        if (!$template) {
            return redirect()->back()->with('error', __('Form template not found.'));
        }

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
                } else if ($actualValue != $field->depends_on_value) {
                    continue; // Skip this field completely
                }
            }

            $ruleSet = [];
            
            if ($field->is_required) {
                $ruleSet[] = 'required';
                $messages["data.{$field->name}.required"] = __('الحقل :label مطلوب.', ['label' => $field->label]);
            } else {
                $ruleSet[] = 'nullable';
            }

            // Type specific rules
            if ($field->type === 'file') {
                $ruleSet[] = 'file';
                $ruleSet[] = 'max:10240'; // 10MB max
                $ruleSet[] = 'mimes:jpeg,png,jpg,pdf';
                $messages["data.{$field->name}.mimes"] = __('يجب أن يكون الملف من نوع صورة أو PDF.');
                $messages["data.{$field->name}.max"] = __('حجم الملف يجب ألا يتجاوز 10 ميجابايت.');
            } elseif ($field->type === 'checkbox') {
                $ruleSet[] = 'boolean';
            } elseif ($field->type === 'multi-select') {
                $ruleSet[] = 'array';
            } else {
                $ruleSet[] = 'string';
            }

            $rules["data.{$field->name}"] = $ruleSet;
        }

        // Validate the incoming request
        $validatedData = $request->validate($rules, $messages);
        
        // 5. Handle File Uploads and Prepare JSON Data
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
                } elseif (is_array($value)) {
                    // Handle multi-select array
                    $finalData[$key] = $value;
                } else {
                    // For checkbox or regular text
                    $finalData[$key] = $value;
                }
            }
        }

        // 6. Create the Request in Database
        \App\Models\SellerRequest::create([
            'user_id' => $user->id,
            'form_template_id' => $template->id,
            'data' => $finalData,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', __('Your request to become a seller has been submitted successfully and is awaiting admin approval.'));
    }
}
