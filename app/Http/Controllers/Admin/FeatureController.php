<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Feature::count(),
            'active' => Feature::where('is_active', true)->count(),
            'inactive' => Feature::where('is_active', false)->count(),
        ];
        return view('admin.features.index', compact('stats'));
    }

    public function getData(Request $request)
    {
        $query = Feature::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title_ar', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('description_ar', 'like', "%{$search}%")
                  ->orWhere('description_en', 'like', "%{$search}%");
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
        $features = $query->orderBy('sort_order', 'asc')->paginate($perPage);

        $data = [];
        foreach ($features as $feature) {
            $data[] = [
                'id' => $feature->id,
                'title_ar' => $feature->title_ar,
                'title_en' => $feature->title_en,
                'description_ar' => $feature->description_ar,
                'description_en' => $feature->description_en,
                'title' => app()->getLocale() == 'ar' ? $feature->title_ar : $feature->title_en,
                'description' => app()->getLocale() == 'ar' ? $feature->description_ar : $feature->description_en,
                'icon' => $feature->icon,
                'color_class' => $feature->color_class,
                'sort_order' => $feature->sort_order,
                'is_active' => (bool)$feature->is_active,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $features->total(),
                'current_page' => $features->currentPage(),
                'links' => $features->linkCollection()->toArray()
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'icon' => 'required|string',
            'color_class' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        Feature::create([
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
            'icon' => $request->icon,
            'color_class' => $request->color_class,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') || $request->boolean('is_active'),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Feature created successfully.')]);
        }

        return redirect()->route('admin.features.index')->with('success', __('Feature created successfully.'));
    }

    public function update(Request $request, Feature $feature)
    {
        $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'icon' => 'required|string',
            'color_class' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $feature->update([
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
            'icon' => $request->icon,
            'color_class' => $request->color_class,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') || $request->boolean('is_active'),
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => __('Feature updated successfully.')]);
        }

        return redirect()->route('admin.features.index')->with('success', __('Feature updated successfully.'));
    }

    public function destroy(Feature $feature)
    {
        $feature->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('Feature deleted successfully.')]);
        }

        return redirect()->route('admin.features.index')->with('success', __('Feature deleted successfully.'));
    }

    public function toggleActive(Feature $feature)
    {
        $feature->update(['is_active' => !$feature->is_active]);

        return response()->json([
            'success' => true,
            'message' => $feature->is_active ? __('Feature activated successfully.') : __('Feature deactivated successfully.')
        ]);
    }
}
