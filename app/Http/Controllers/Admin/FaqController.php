<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        return view('admin.faqs.index');
    }

    public function getData()
    {
        $faqs = Faq::all();

        return response()->json([
            'data' => $faqs->map(function ($faq) {
                return [
                    'id' => $faq->id,
                    'question' => app()->getLocale() == 'ar' ? $faq->question_ar : $faq->question_en,
                    'is_active' => '<div class="form-check form-switch fs-4"><input class="form-check-input" type="checkbox" onchange="toggleStatus(' . $faq->id . ')" ' . ($faq->is_active ? 'checked' : '') . '></div>',
                    'actions' => '
                        <button onclick="editFaq(' . $faq->id . ', \'' . htmlspecialchars(addslashes($faq->question_ar)) . '\', \'' . htmlspecialchars(addslashes($faq->question_en)) . '\', \'' . htmlspecialchars(addslashes($faq->answer_ar)) . '\', \'' . htmlspecialchars(addslashes($faq->answer_en)) . '\', ' . $faq->is_active . ')" class="btn btn-sm btn-primary">' . __('Edit') . '</button>
                        <button onclick="deleteFaq(' . $faq->id . ')" class="btn btn-sm btn-danger">' . __('Delete') . '</button>
                    '
                ];
            })
        ]);
    }

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_ar' => 'required|string|max:255',
            'question_en' => 'required|string|max:255',
            'answer_ar' => 'required|string',
            'answer_en' => 'required|string',
            'is_active' => 'boolean',
        ]);

        Faq::create([
            'question_ar' => $request->question_ar,
            'question_en' => $request->question_en,
            'answer_ar' => $request->answer_ar,
            'answer_en' => $request->answer_en,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('FAQ created successfully.')]);
        }

        return redirect()->route('admin.faqs.index')->with('success', __('FAQ created successfully.'));
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'question_ar' => 'required|string|max:255',
            'question_en' => 'required|string|max:255',
            'answer_ar' => 'required|string',
            'answer_en' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $faq->update([
            'question_ar' => $request->question_ar,
            'question_en' => $request->question_en,
            'answer_ar' => $request->answer_ar,
            'answer_en' => $request->answer_en,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('FAQ updated successfully.')]);
        }

        return redirect()->route('admin.faqs.index')->with('success', __('FAQ updated successfully.'));
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.faqs.index')->with('success', __('FAQ deleted successfully.'));
    }

    public function toggleActive(Faq $faq)
    {
        $faq->update(['is_active' => !$faq->is_active]);

        return response()->json([
            'success' => true,
            'message' => $faq->is_active ? __('FAQ activated successfully.') : __('FAQ deactivated successfully.')
        ]);
    }
}
