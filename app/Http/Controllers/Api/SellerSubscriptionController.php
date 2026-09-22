<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class SellerSubscriptionController extends Controller
{
    use ApiResponse;

    /**
     * Get Seller Subscription Status
     * 
     * Returns the current status of the user's seller subscription request.
     */
    #[OA\Get(
        path: '/api/seller-subscription/status',
        summary: 'Get Seller Subscription Status',
        description: 'Returns the current status of the user\'s seller subscription request (seller, pending, rejected, or none).',
        security: [['bearerAuth' => []]],
        tags: ['Seller'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Current status retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'data', type: 'object', properties: [
                            new OA\Property(property: 'status', type: 'string', example: 'pending', description: 'Can be: seller, pending, rejected, action_required, under_review, draft, none'),
                            new OA\Property(property: 'admin_notes', type: 'string', nullable: true, example: 'Identity proof is not clear.', description: 'Present if status is rejected or action_required'),
                            new OA\Property(
                                property: 'template', 
                                type: 'object', 
                                nullable: true, 
                                description: 'The dynamic form template to render if status is none, draft, action_required or rejected',
                                example: [
                                    'id' => 1,
                                    'name' => 'Seller Request V1',
                                    'fields' => [
                                        [
                                            'name' => 'full_name',
                                            'type' => 'text',
                                            'is_required' => true,
                                            'ui_hint' => ['keyboard' => 'default']
                                        ],
                                        [
                                            'name' => 'city',
                                            'type' => 'select',
                                            'is_required' => true,
                                            'options' => [],
                                            'ui_hint' => ['keyboard' => 'default', 'widget' => 'bottom_sheet_picker'],
                                            'data_source_url' => 'http://localhost/Motorzad/public/api/locations/cities'
                                        ]
                                    ]
                                ]
                            ),
                            new OA\Property(
                                property: 'draft_data',
                                type: 'object',
                                nullable: true,
                                description: 'Previously saved or rejected data to pre-fill the form',
                                example: ['full_name' => 'أحمد محمد']
                            ),
                            new OA\Property(
                                property: 'timeline',
                                type: 'array',
                                description: 'A stepped progress bar indicating the user\'s journey to become a seller',
                                items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: 'step', type: 'integer', example: 1),
                                        new OA\Property(property: 'title', type: 'string', example: 'إنشاء الحساب'),
                                        new OA\Property(property: 'status', type: 'string', example: 'completed', description: 'Can be: completed, current, locked, pending, under_review, action_required, rejected')
                                    ]
                                )
                            )
                        ])
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function status(Request $request): JsonResponse
    {
        $user = auth()->user();

        // Determine current status
        $status = 'none';
        $adminNotes = null;
        $draftData = null;
        $template = null;

        if ($user->hasRole('seller')) {
            $status = 'seller';
        } else {
            $latestRequest = \App\Models\SellerRequest::where('user_id', $user->id)->latest()->first();
            if ($latestRequest) {
                $status = $latestRequest->status;
                $adminNotes = $latestRequest->admin_notes;
                if (in_array($status, ['draft', 'rejected', 'action_required'])) {
                    $draftData = $latestRequest->data;
                }
            }
            
            // Only fetch template if we're not pending or under_review
            if (!in_array($status, ['pending', 'under_review', 'seller'])) {
                $template = \App\Models\FormTemplate::with('fields')->where('is_active', true)->first();
            }
        }

        // Build Timeline Progress Bar
        $timeline = [
            [
                'step' => 1,
                'title' => __('إنشاء الحساب'),
                'status' => 'completed'
            ],
            [
                'step' => 2,
                'title' => __('توثيق الهوية (KYC)'),
                'status' => $user->status === 'approved' ? 'completed' : 'current'
            ]
        ];

        $step3Status = 'locked';
        if ($user->status === 'approved') {
            if ($status === 'seller') {
                $step3Status = 'completed';
            } elseif (in_array($status, ['pending', 'under_review', 'action_required', 'rejected'])) {
                $step3Status = $status;
            } else {
                // none or draft
                $step3Status = 'current';
            }
        }

        $timeline[] = [
            'step' => 3,
            'title' => __('تعبئة طلب البائع'),
            'status' => $step3Status
        ];

        $timeline[] = [
            'step' => 4,
            'title' => __('اعتماد الحساب'),
            'status' => $status === 'seller' ? 'completed' : 'locked'
        ];

        return $this->successResponse([
            'status' => $status,
            'admin_notes' => $adminNotes,
            'template' => $template,
            'draft_data' => $draftData,
            'timeline' => $timeline
        ]);
    }

    /**
     * Upgrade user to a seller.
     */
    #[OA\Post(
        path: '/api/seller-subscription/subscribe',
        summary: 'Submit Seller Subscription Request',
        description: 'Submits a request to upgrade the authenticated user to a seller role. Supports flattened JSON payloads with Base64 files, or traditional multipart/form-data. Admin approval is required.',
        security: [['bearerAuth' => []]],
        tags: ['Seller'],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(
                        type: 'object',
                        example: [
                            'template_id' => 1,
                            'is_draft' => false,
                            'full_name' => 'أحمد محمد',
                            'cities' => ['الرياض', 'جدة'],
                            'national_id_image' => 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQ...'
                        ]
                    )
                ),
                new OA\MediaType(
                    mediaType: 'multipart/form-data',
                    schema: new OA\Schema(
                        required: ['template_id'],
                        properties: [
                            new OA\Property(property: 'template_id', type: 'integer', description: 'ID of the form template being submitted'),
                            new OA\Property(property: 'is_draft', type: 'boolean', description: 'Set to true to bypass required validations and save as draft'),
                            new OA\Property(
                                property: 'data',
                                type: 'object',
                                description: 'Dynamic key-value pairs matching the template fields. e.g. data[national_id]=1234567890. For files, send as binary data.'
                            )
                        ]
                    )
                )
            ]
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successfully submitted the request',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Your request to become a seller has been submitted successfully and is awaiting admin approval.')
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden - KYC not approved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Please complete identity verification to become a seller.'),
                        new OA\Property(property: 'error_code', type: 'string', example: 'KYC_REQUIRED')
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation Errors',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'الحقل الاسم الكامل مطلوب.'),
                        new OA\Property(property: 'error_code', type: 'string', example: 'VALIDATION_FAILED'),
                        new OA\Property(
                            property: 'errors', 
                            type: 'object',
                            example: ['full_name' => ['الحقل الاسم الكامل مطلوب.']]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Bad Request (e.g. Already a seller, or request already pending)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'طلبك لتصبح بائعاً ينتظر الموافقة بالفعل.'),
                        new OA\Property(property: 'error_code', type: 'string', example: 'ALREADY_PENDING')
                    ]
                )
            ),
            new OA\Response(
                response: 429,
                description: 'Rate Limit - Request already processing',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Your request is already being processed.'),
                        new OA\Property(property: 'error_code', type: 'string', example: 'RATE_LIMIT_EXCEEDED')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();

        // 1. Check if they are already a seller
        if ($user->hasRole('seller')) {
            return $this->errorResponse(__('You are already a seller.'), 400);
        }

        // 2. KYC Verification Check
        if (!$user->status || $user->status !== 'approved') {
            return $this->errorResponse(__('Please complete identity verification to become a seller.'), 403, null, 'KYC_REQUIRED');
        }
        
        // 3. Prevent Double Submit / Race Condition
        $lock = \Illuminate\Support\Facades\Cache::lock('seller_subscribe_api_' . $user->id, 5);
        if (!$lock->get()) {
            return $this->errorResponse(__('Your request is already being processed.'), 429, null, 'RATE_LIMIT_EXCEEDED');
        }

        // 4. Check if there is already a pending or under review request
        $pendingRequest = \App\Models\SellerRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'under_review'])
            ->first();
            
        if ($pendingRequest) {
            $lock->release();
            return $this->errorResponse(__('Your request to become a seller is already pending approval.'), 400, null, 'ALREADY_PENDING');
        }

        // 5. Dynamic Validation based on Template
        $templateId = $request->input('template_id');
        
        // Fallback if template_id is missing
        if (!$templateId) {
            $templateId = \App\Models\Setting::get('seller_request_template_id');
            if (!$templateId) {
                $fallback = \App\Models\FormTemplate::where('is_active', true)->orderBy('id', 'asc')->first();
                $templateId = $fallback ? $fallback->id : null;
            }
        }

        $template = \App\Models\FormTemplate::with('fields')->where('is_active', true)->find($templateId);

        if (!$template) {
            $lock->release();
            return $this->errorResponse(__('Form template not found or is currently inactive.'), 404, null, 'TEMPLATE_NOT_FOUND');
        }

        $existingRequest = \App\Models\SellerRequest::where('user_id', $user->id)
            ->whereIn('status', ['draft', 'action_required'])
            ->first();
        $oldData = $existingRequest ? ($existingRequest->data ?? []) : [];

        $rules = [];
        $messages = [];
        $attributes = [];
        $hasDataWrapper = is_array($request->input('data'));
        $prefix = $hasDataWrapper ? 'data.' : '';
        $submittedData = $hasDataWrapper ? $request->input('data') : $request->all();

        foreach ($template->fields as $field) {
            if (in_array($field->type, ['html', 'step-divider'])) {
                continue;
            }

            // Check conditional logic: if hidden, skip validation and don't save it
            if (!empty($field->depends_on_field_name)) {
                $actualValue = $submittedData[$field->depends_on_field_name] ?? null;
                if (is_array($actualValue) && in_array($field->depends_on_value, $actualValue)) {
                    // condition met
                } else if (!is_array($actualValue) && $actualValue == $field->depends_on_value) {
                    // condition met
                } else {
                    continue; // Skip this field completely
                }
            }

            $ruleSet = [];
            $isDraft = filter_var($request->input('is_draft', false), FILTER_VALIDATE_BOOLEAN);
            $inputValue = $request->input("{$prefix}{$field->name}");
            
            $hasOldFile = $field->type === 'file' && !empty($oldData[$field->name]);
            $isNewFileUpload = $field->type === 'file' && $request->hasFile("{$prefix}{$field->name}");
            $isBase64Upload = $field->type === 'file' && !$isNewFileUpload && !empty($inputValue) && is_string($inputValue) && str_starts_with($inputValue, 'data:');
            
            if ($field->is_required && !$isDraft) {
                if ($field->type === 'file') {
                    if (!$hasOldFile && !$isNewFileUpload && !$isBase64Upload) {
                        $ruleSet[] = 'required';
                    }
                } else {
                    $ruleSet[] = 'required';
                }
                $messages["{$prefix}{$field->name}.required"] = __('الحقل :label مطلوب.', ['label' => $field->label]);
            } else {
                $ruleSet[] = 'nullable';
            }

            // Type specific rules
            if ($field->type === 'file') {
                if ($isNewFileUpload) {
                    $ruleSet[] = 'file';
                    $ruleSet[] = 'max:10240'; // 10MB max
                    $ruleSet[] = 'mimes:jpeg,png,jpg,pdf';
                    $messages["{$prefix}{$field->name}.mimes"] = __('يجب أن يكون الملف من نوع صورة أو PDF.');
                    $messages["{$prefix}{$field->name}.max"] = __('حجم الملف يجب ألا يتجاوز 10 ميجابايت.');
                } elseif ($isBase64Upload) {
                    $ruleSet[] = function ($attribute, $value, $fail) {
                        if (preg_match('/^data:(image\/[a-zA-Z\+\-\.]+|application\/pdf);base64,/', $value)) {
                            $data = substr($value, strpos($value, ',') + 1);
                            $data = base64_decode($data);
                            if ($data === false) {
                                $fail(__('الملف المرفق غير صالح.'));
                                return;
                            }
                            if (strlen($data) > 10485760) {
                                $fail(__('حجم الملف يجب ألا يتجاوز 10 ميجابايت.'));
                            }
                        } else {
                            $fail(__('صيغة الملف غير مدعومة. يسمح بصور أو PDF فقط (Base64).'));
                        }
                    };
                }
            } elseif ($field->type === 'checkbox') {
                $ruleSet[] = 'boolean';
            } elseif ($field->type === 'multi-select') {
                $ruleSet[] = 'array';
                // Only enforce static options validation if options actually exist (skip for Remote URLs)
                if (is_array($field->options) && count($field->options) > 0) {
                    $rules["{$prefix}{$field->name}.*"] = ['string', \Illuminate\Validation\Rule::in($field->options)];
                } else {
                    $rules["{$prefix}{$field->name}.*"] = ['string'];
                }
            } else {
                $ruleSet[] = 'string';
            }

            $rules["{$prefix}{$field->name}"] = $ruleSet;
            $attributes["{$prefix}{$field->name}"] = $field->label;
            if ($field->type === 'multi-select') {
                $attributes["{$prefix}{$field->name}.*"] = $field->label;
            }
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages, $attributes);
        if ($validator->fails()) {
            if (isset($lock)) $lock->release();
            
            $cleanErrors = [];
            foreach ($validator->errors()->toArray() as $key => $messagesList) {
                // Remove 'data.' prefix so mobile devs can auto-bind errors to fields
                $cleanKey = preg_replace('/^data\./', '', $key);
                $cleanErrors[$cleanKey] = $messagesList;
            }
            
            return $this->errorResponse($validator->errors()->first(), 422, $cleanErrors, 'VALIDATION_FAILED');
        }

        $validatedData = $validator->validated();
        
        // 6. Handle File Uploads and Prepare JSON Data

        $finalData = [];
        $validatedDataArray = $hasDataWrapper ? ($validatedData['data'] ?? []) : $validatedData;
        
        foreach ($validatedDataArray as $key => $value) {
            // Ignore non-template keys if flattened
            if (!$hasDataWrapper && in_array($key, ['template_id', 'is_draft'])) continue;
            
            // Check if the value is an uploaded file
            if ($request->hasFile("{$prefix}{$key}")) {
                $file = $request->file("{$prefix}{$key}");
                    $fileName = 'seller_req_' . $user->id . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('seller_requests', $fileName, 'public');
                    $finalData[$key] = '/storage/' . $path;
                    
                    // Delete old file if exists
                    if (!empty($oldData[$key])) {
                        $oldPath = str_replace('/storage/', '', $oldData[$key]);
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                    }
                } elseif (is_string($value) && str_starts_with($value, 'data:')) {
                    // Handle Base64 file upload
                    $extension = 'jpg'; // default
                    if (preg_match('/^data:image\/(jpeg|jpg|png|gif|webp);base64,/', $value, $matches)) {
                        $extension = $matches[1];
                    } elseif (preg_match('/^data:application\/pdf;base64,/', $value)) {
                        $extension = 'pdf';
                    }
                    
                    $data = substr($value, strpos($value, ',') + 1);
                    $data = base64_decode($data);
                    
                    $fileName = 'seller_req_' . $user->id . '_' . \Illuminate\Support\Str::random(10) . '.' . $extension;
                    \Illuminate\Support\Facades\Storage::disk('public')->put('seller_requests/' . $fileName, $data);
                    $finalData[$key] = '/storage/seller_requests/' . $fileName;
                    
                    // Delete old file if exists
                    if (!empty($oldData[$key])) {
                        $oldPath = str_replace('/storage/', '', $oldData[$key]);
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                    }
                } else {
                    $fieldObj = collect($template->fields)->firstWhere('name', $key);
                    if ($fieldObj && $fieldObj->type === 'file' && empty($value) && !empty($oldData[$key])) {
                        $finalData[$key] = $oldData[$key];
                    } elseif (is_array($value)) {
                        $finalData[$key] = $value;
                    } else {
                        $finalData[$key] = $value;
                    }
                }
            }

        // 7. Create or Update the Request in Database
        $isDraft = filter_var($request->input('is_draft', false), FILTER_VALIDATE_BOOLEAN);
        
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
            return $this->successResponse(null, __('تم حفظ طلبك كمسودة بنجاح. يمكنك العودة لإكماله لاحقاً.'));
        }

        return $this->successResponse(null, __('Your request to become a seller has been submitted successfully and is awaiting admin approval.'));
    }
}
