<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FormTemplateController extends Controller
{
    /**
     * Display a listing of the templates.
     */
    public function index(Request $request)
    {
        $query = FormTemplate::withCount(['fields', 'sellerRequests'])
            ->orderBy('id', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active' ? true : false;
            $query->where('is_active', $isActive);
        }

        $templates = $query->paginate(10)->withQueryString();

        // Statistics
        $stats = [
            'total' => FormTemplate::count(),
            'active' => FormTemplate::where('is_active', true)->count(),
            'inactive' => FormTemplate::where('is_active', false)->count(),
            'total_requests' => \App\Models\SellerRequest::count(),
        ];
            
        return view('admin.form_templates.index', compact('templates', 'stats'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        return view('admin.form_templates.create');
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|string|in:text,textarea,file,select,checkbox,date,radio,multi-select,html,step-divider',
            'fields.*.name' => 'nullable|string',
            'fields.*.description' => 'nullable|string',
            'fields.*.is_required' => 'boolean',
            'fields.*.options' => 'nullable|string',
            'fields.*.depends_on_field_name' => 'nullable|string',
            'fields.*.depends_on_value' => 'nullable|string',
        ], [
            'fields.required' => 'يجب إضافة حقل واحد على الأقل للقالب.',
        ]);

        try {
            DB::beginTransaction();

            // Create the Template
            $template = FormTemplate::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => true,
            ]);

            // Create Fields
            $order = 0;
            foreach ($request->fields as $fieldData) {
                // Use frontend generated name or generate a safe programmatic name
                $name = !empty($fieldData['name']) ? $fieldData['name'] : 'field_' . Str::random(6); 

                $optionsArray = null;
                if (in_array($fieldData['type'], ['select', 'radio', 'multi-select']) && !empty($fieldData['options'])) {
                    // split by comma, trim, filter empty
                    $optionsArray = array_values(array_filter(array_map('trim', explode(',', $fieldData['options'])), function($val) {
                        return $val !== '';
                    }));
                }

                $template->fields()->create([
                    'name' => $name,
                    'label' => $fieldData['label'],
                    'description' => $fieldData['description'] ?? null,
                    'type' => $fieldData['type'],
                    'is_required' => isset($fieldData['is_required']) ? true : false,
                    'options' => $optionsArray,
                    'order' => $order++,
                    'depends_on_field_name' => $fieldData['depends_on_field_name'] ?? null,
                    'depends_on_value' => $fieldData['depends_on_value'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('admin.form-templates.index')->with('success', 'تم إنشاء قالب النماذج بنجاح!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ القالب: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(FormTemplate $formTemplate)
    {
        $formTemplate->load('fields');
        
        // Prepare options string for Alpine JS
        foreach ($formTemplate->fields as $field) {
            if (is_array($field->options)) {
                $field->options_string = implode(', ', $field->options);
            } else {
                $field->options_string = '';
            }
        }
        
        return view('admin.form_templates.edit', compact('formTemplate'));
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, FormTemplate $formTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.type' => 'required|string|in:text,textarea,file,select,checkbox,date,radio,multi-select,html,step-divider',
            'fields.*.is_required' => 'boolean',
            'fields.*.options' => 'nullable|string',
            'fields.*.description' => 'nullable|string',
            'fields.*.name' => 'nullable|string', // existing programmatic name
            'fields.*.depends_on_field_name' => 'nullable|string',
            'fields.*.depends_on_value' => 'nullable|string',
        ], [
            'fields.required' => 'يجب إبقاء حقل واحد على الأقل للقالب.',
        ]);

        try {
            DB::beginTransaction();

            $formTemplate->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $existingFields = $formTemplate->fields->keyBy('name');
            $newFieldNames = collect($request->fields)->pluck('name')->filter()->toArray();

            // Delete fields that are no longer in the request
            $fieldsToDelete = $existingFields->keys()->diff($newFieldNames);
            if ($fieldsToDelete->count() > 0) {
                $formTemplate->fields()->whereIn('name', $fieldsToDelete)->delete();
            }

            // Update or Create Fields
            $order = 0;
            foreach ($request->fields as $fieldData) {
                $optionsArray = null;
                if (in_array($fieldData['type'], ['select', 'radio', 'multi-select']) && !empty($fieldData['options'])) {
                    $optionsArray = array_values(array_filter(array_map('trim', explode(',', $fieldData['options'])), function($val) {
                        return $val !== '';
                    }));
                }

                $name = !empty($fieldData['name']) ? $fieldData['name'] : 'field_' . Str::random(6);

                $formTemplate->fields()->updateOrCreate(
                    ['name' => $name],
                    [
                        'label' => $fieldData['label'],
                        'description' => $fieldData['description'] ?? null,
                        'type' => $fieldData['type'],
                        'is_required' => isset($fieldData['is_required']) ? true : false,
                        'options' => $optionsArray,
                        'order' => $order++,
                        'depends_on_field_name' => $fieldData['depends_on_field_name'] ?? null,
                        'depends_on_value' => $fieldData['depends_on_value'] ?? null,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('admin.form-templates.index')->with('success', 'تم تحديث القالب بنجاح!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء تحديث القالب: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy(FormTemplate $formTemplate)
    {
        // Check if it's the active template in settings
        $activeTemplateId = \App\Models\Setting::get('seller_request_template_id');
        
        if ($activeTemplateId == $formTemplate->id) {
            return back()->with('error', 'لا يمكن حذف القالب لأنه محدد كالقالب الافتراضي في إعدادات النظام. يرجى تغيير الإعدادات أولاً.');
        }

        $formTemplate->delete();
        
        return redirect()->route('admin.form-templates.index')->with('success', 'تم حذف القالب بنجاح.');
    }

    /**
     * Toggle the active status of the template.
     */
    public function toggleStatus(FormTemplate $formTemplate)
    {
        $formTemplate->update([
            'is_active' => !$formTemplate->is_active
        ]);

        $statusText = $formTemplate->is_active ? 'تفعيل' : 'تعطيل';
        return back()->with('success', "تم {$statusText} القالب بنجاح.");
    }

    /**
     * Clone the specified template.
     */
    public function clone(FormTemplate $formTemplate)
    {
        try {
            DB::beginTransaction();

            $newTemplate = $formTemplate->replicate();
            $newTemplate->name = $newTemplate->name . ' - ' . __('نسخة');
            $newTemplate->is_active = false; // Usually clones start inactive
            $newTemplate->save();

            // Clone relationships
            $formTemplate->load('fields');
            foreach ($formTemplate->fields as $field) {
                $newField = $field->replicate();
                $newField->form_template_id = $newTemplate->id;
                // We keep the original field name to preserve conditional logic mappings (depends_on_field_name)
                $newField->save();
            }

            DB::commit();
            return redirect()->route('admin.form-templates.index')->with('success', __('تم استنساخ القالب بنجاح. يمكنك الآن تعديله.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('حدث خطأ أثناء نسخ القالب: ') . $e->getMessage());
        }
    }
}
