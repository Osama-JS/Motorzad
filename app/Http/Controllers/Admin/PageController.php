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
        return view('admin.pages.index');
    }

    /**
     * Get data for DataTables.
     */
    public function getData(Request $request)
    {
        $pages = Page::query()->orderBy('id', 'desc')->get();

        $data = $pages->map(function ($page) {
            $status = $page->is_active ? 'Active' : 'Inactive';
            $class = $page->is_active ? 'success' : 'danger';
            $activeBadge = '<span class="badge light badge-' . $class . '">' . __($status) . '</span>';

            $footerStatus = $page->show_in_footer ? 'Yes' : 'No';
            $footerClass = $page->show_in_footer ? 'info' : 'warning';
            $footerBadge = '<span class="badge light badge-' . $footerClass . '">' . __($footerStatus) . '</span>';

            $editUrl = route('admin.pages.edit', $page->id);
            $editBtn = '<a href="' . $editUrl . '" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></a>';
            $deleteBtn = '<button type="button" class="btn btn-danger shadow btn-xs sharp me-1" onclick="deletePage(' . $page->id . ')"><i class="fa fa-trash"></i></button>';

            return [
                'id' => $page->id,
                'title_ar' => $page->title_ar,
                'title_en' => $page->title_en,
                'slug' => $page->slug,
                'is_active' => $activeBadge,
                'show_in_footer' => $footerBadge,
                'actions' => '<div class="d-flex">' . $editBtn . $deleteBtn . '</div>',
            ];
        });

        return response()->json(['data' => $data]);
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

        return redirect()->route('admin.pages.index')->with('success', __('Page created successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
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
