<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stats = [
            'total' => Page::count(),
            'active' => Page::where('is_active', true)->count(),
            'inactive' => Page::where('is_active', false)->count(),
            'footer' => Page::where('show_in_footer', true)->count(),
        ];
        return view('admin.pages.index', compact('stats'));
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        $query = Page::query()->orderBy('id', 'desc');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title_ar', 'like', "%{$request->search}%")
                  ->orWhere('title_en', 'like', "%{$request->search}%")
                  ->orWhere('slug', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $perPage = $request->per_page ?? 10;
        $pages = $query->paginate($perPage);

        $data = [];
        foreach ($pages as $page) {
            $statusBadge = $page->is_active 
                ? '<span class="badge bg-success text-white px-3 py-2 rounded-pill"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>' . __('Active') . '</span>'
                : '<span class="badge bg-danger text-white px-3 py-2 rounded-pill"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>' . __('Inactive') . '</span>';

            $footerBadge = $page->show_in_footer 
                ? '<span class="badge bg-info text-white px-2 py-1"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><polyline points="20 6 9 17 4 12"></polyline></svg>' . __('Yes') . '</span>'
                : '<span class="badge bg-secondary text-white px-2 py-1"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>' . __('No') . '</span>';

            $actions = "<div class=\"dropdown action-dropdown\">
                <button class=\"btn btn-sm btn-icon border-0 shadow-none dropdown-toggle\" type=\"button\" data-bs-toggle=\"dropdown\">
                    <svg width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"text-muted\"><circle cx=\"12\" cy=\"12\" r=\"1\"></circle><circle cx=\"12\" cy=\"5\" r=\"1\"></circle><circle cx=\"12\" cy=\"19\" r=\"1\"></circle></svg>
                </button>
                <ul class=\"dropdown-menu dropdown-menu-end border-0 shadow-sm py-2\">
                    <li><a class=\"dropdown-item text-success\" href=\"" . url('/page/' . $page->slug) . "\" target=\"_blank\"><svg width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"me-2\"><path d=\"M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z\"></path><circle cx=\"12\" cy=\"12\" r=\"3\"></circle></svg>" . __('Preview') . "</a></li>
                    <li><a class=\"dropdown-item text-primary\" href=\"javascript:void(0)\" onclick=\"editPage({$page->id})\"><svg width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"me-2\"><path d=\"M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7\"></path><path d=\"M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z\"></path></svg>" . __('Edit') . "</a></li>
                    <li><hr class=\"dropdown-divider\"></li>
                    <li><a class=\"dropdown-item text-danger\" href=\"javascript:void(0)\" onclick=\"deletePage({$page->id})\"><svg width=\"16\" height=\"16\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\" class=\"me-2\"><polyline points=\"3 6 5 6 21 6\"></polyline><path d=\"M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2\"></path></svg>" . __('Delete') . "</a></li>
                </ul>
            </div>";

            $data[] = [
                'id' => $page->id,
                'title_ar' => "<strong>{$page->title_ar}</strong>",
                'title_en' => $page->title_en,
                'slug' => "<span dir=\"ltr\" class=\"text-muted\">{$page->slug}</span>",
                'is_active' => $statusBadge,
                'show_in_footer' => $footerBadge,
                'actions' => $actions,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $pages->total(),
                'current_page' => $pages->currentPage(),
                'links' => $pages->linkCollection()->toArray()
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required|unique:pages,slug',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'content_ar' => 'required|string',
            'content_en' => 'required|string',
            'is_active' => 'boolean',
            'show_in_footer' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->boolean('is_active');
        $data['show_in_footer'] = $request->boolean('show_in_footer');

        Page::create($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Page created successfully.')
            ]);
        }

        return redirect()->route('admin.pages.index')->with('success', __('Page created successfully.'));
    }

    /**
     * Show the specified resource.
     */
    public function show(Page $page)
    {
        return response()->json([
            'success' => true,
            'page' => $page
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        return redirect()->route('admin.pages.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Page $page)
    {
        $request->validate([
            'slug' => 'required|unique:pages,slug,' . $page->id,
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'content_ar' => 'required|string',
            'content_en' => 'required|string',
            'is_active' => 'boolean',
            'show_in_footer' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->boolean('is_active');
        $data['show_in_footer'] = $request->boolean('show_in_footer');

        $page->update($data);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Page updated successfully.')
            ]);
        }

        return redirect()->route('admin.pages.index')->with('success', __('Page updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        $page->delete();
        return response()->json(['success' => true, 'message' => __('Page deleted successfully.')]);
    }
}
