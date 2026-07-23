<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Testimonial::count(),
            'active' => Testimonial::where('is_active', true)->count(),
            'inactive' => Testimonial::where('is_active', false)->count(),
        ];
        return view('admin.testimonials.index', compact('stats'));
    }

    public function getData(Request $request)
    {
        $query = Testimonial::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('role_ar', 'like', "%{$search}%")
                  ->orWhere('role_en', 'like', "%{$search}%")
                  ->orWhere('text_ar', 'like', "%{$search}%")
                  ->orWhere('text_en', 'like', "%{$search}%");
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
        $testimonials = $query->paginate($perPage);

        $data = [];
        foreach ($testimonials as $testimonial) {
            $data[] = [
                'id' => $testimonial->id,
                'name_ar' => $testimonial->name_ar,
                'name_en' => $testimonial->name_en,
                'role_ar' => $testimonial->role_ar,
                'role_en' => $testimonial->role_en,
                'text_ar' => $testimonial->text_ar,
                'text_en' => $testimonial->text_en,
                'avatar_init' => $testimonial->avatar_init,
                'avatar_init_en' => $testimonial->avatar_init_en,
                'name' => app()->getLocale() == 'ar' ? $testimonial->name_ar : $testimonial->name_en,
                'role' => app()->getLocale() == 'ar' ? $testimonial->role_ar : $testimonial->role_en,
                'text' => app()->getLocale() == 'ar' ? $testimonial->text_ar : $testimonial->text_en,
                'is_active' => (bool)$testimonial->is_active,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $testimonials->total(),
                'current_page' => $testimonials->currentPage(),
                'links' => $testimonials->linkCollection()->toArray()
            ]
        ]);
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'role_ar' => 'required|string|max:255',
            'role_en' => 'required|string|max:255',
            'text_ar' => 'required|string',
            'text_en' => 'required|string',
            'avatar_init' => 'nullable|string|max:5',
            'avatar_init_en' => 'nullable|string|max:5',
            'is_active' => 'boolean',
        ]);

        Testimonial::create([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'role_ar' => $request->role_ar,
            'role_en' => $request->role_en,
            'text_ar' => $request->text_ar,
            'text_en' => $request->text_en,
            'avatar_init' => $request->avatar_init,
            'avatar_init_en' => $request->avatar_init_en,
            'is_active' => $request->has('is_active') || $request->boolean('is_active'),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Testimonial created successfully.')]);
        }

        return redirect()->route('admin.testimonials.index')->with('success', __('Testimonial created successfully.'));
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'role_ar' => 'required|string|max:255',
            'role_en' => 'required|string|max:255',
            'text_ar' => 'required|string',
            'text_en' => 'required|string',
            'avatar_init' => 'nullable|string|max:5',
            'avatar_init_en' => 'nullable|string|max:5',
            'is_active' => 'boolean',
        ]);

        $testimonial->update([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'role_ar' => $request->role_ar,
            'role_en' => $request->role_en,
            'text_ar' => $request->text_ar,
            'text_en' => $request->text_en,
            'avatar_init' => $request->avatar_init,
            'avatar_init_en' => $request->avatar_init_en,
            'is_active' => $request->has('is_active') || $request->boolean('is_active'),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Testimonial updated successfully.')]);
        }

        return redirect()->route('admin.testimonials.index')->with('success', __('Testimonial updated successfully.'));
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('Testimonial deleted successfully.')]);
        }

        return redirect()->route('admin.testimonials.index')->with('success', __('Testimonial deleted successfully.'));
    }

    public function toggleActive(Testimonial $testimonial)
    {
        $testimonial->update(['is_active' => !$testimonial->is_active]);

        return response()->json([
            'success' => true,
            'message' => $testimonial->is_active ? __('Testimonial activated successfully.') : __('Testimonial deactivated successfully.')
        ]);
    }
}
