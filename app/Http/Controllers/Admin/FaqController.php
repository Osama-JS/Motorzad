<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Faq::count(),
            'active' => Faq::where('is_active', true)->count(),
            'inactive' => Faq::where('is_active', false)->count(),
        ];
        return view('admin.faqs.index', compact('stats'));
    }

    public function getData(Request $request)
    {
        $query = Faq::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('question_ar', 'like', "%{$search}%")
                  ->orWhere('question_en', 'like', "%{$search}%")
                  ->orWhere('answer_ar', 'like', "%{$search}%")
                  ->orWhere('answer_en', 'like', "%{$search}%");
            });
        }

        if ($request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $perPage = $request->per_page ?? 10;
        $faqs = $query->paginate($perPage);

        $data = [];
        foreach ($faqs as $faq) {
            $data[] = [
                'id' => $faq->id,
                'question_ar' => $faq->question_ar,
                'question_en' => $faq->question_en,
                'answer_ar' => $faq->answer_ar,
                'answer_en' => $faq->answer_en,
                'question' => app()->getLocale() == 'ar' ? $faq->question_ar : $faq->question_en,
                'answer' => app()->getLocale() == 'ar' ? $faq->answer_ar : $faq->answer_en,
                'is_active' => (bool)$faq->is_active,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $faqs->total(),
                'current_page' => $faqs->currentPage(),
                'links' => $faqs->linkCollection()->toArray()
            ]
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
            'is_active' => $request->has('is_active') || $request->boolean('is_active'),
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
            'is_active' => $request->has('is_active') || $request->boolean('is_active'),
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
            return response()->json(['success' => true, 'message' => __('FAQ deleted successfully.')]);
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
