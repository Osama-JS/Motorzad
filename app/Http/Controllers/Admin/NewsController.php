<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => News::count(),
            'active' => News::where('is_active', true)->count(),
            'inactive' => News::where('is_active', false)->count(),
        ];
        return view('admin.news.index', compact('stats'));
    }

    public function getData(Request $request)
    {
        $query = News::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title_ar', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('content_ar', 'like', "%{$search}%")
                  ->orWhere('content_en', 'like', "%{$search}%");
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
        $newsItems = $query->with('category')->latest()->paginate($perPage);

        $data = [];
        foreach ($newsItems as $news) {
            $data[] = [
                'id' => $news->id,
                'title_ar' => $news->title_ar,
                'title_en' => $news->title_en,
                'content_ar' => $news->content_ar,
                'content_en' => $news->content_en,
                'title' => app()->getLocale() == 'ar' ? $news->title_ar : $news->title_en,
                'content' => app()->getLocale() == 'ar' ? $news->content_ar : $news->content_en,
                'category_name' => $news->category ? $news->category->name : __('No Category'),
                'is_featured' => (bool)$news->is_featured,
                'image_url' => $news->image ? asset('storage/' . $news->image) : null,
                'is_active' => (bool)$news->is_active,
                'views_count' => $news->views_count ?? 0,
                'created_at' => $news->created_at->format('Y-m-d H:i'),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $newsItems->total(),
                'current_page' => $newsItems->currentPage(),
                'links' => $newsItems->linkCollection()->toArray()
            ]
        ]);
    }

    public function create()
    {
        $categories = \App\Models\NewsCategory::where('is_active', true)->get();
        return view('admin.news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'content_ar' => 'required|string',
            'content_en' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'category_id' => 'nullable|exists:news_categories,id',
            'tags_ar' => 'nullable|string',
            'tags_en' => 'nullable|string',
            'meta_title_ar' => 'nullable|string|max:255',
            'meta_title_en' => 'nullable|string|max:255',
            'meta_description_ar' => 'nullable|string',
            'meta_description_en' => 'nullable|string',
            'meta_keywords_ar' => 'nullable|string',
            'meta_keywords_en' => 'nullable|string',
        ]);

        $data = [
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'content_ar' => $request->content_ar,
            'content_en' => $request->content_en,
            'is_active' => $request->has('is_active') || $request->boolean('is_active'),
            'is_featured' => $request->has('is_featured') || $request->boolean('is_featured'),
            'category_id' => $request->category_id,
            'tags_ar' => $request->tags_ar,
            'tags_en' => $request->tags_en,
            'meta_title_ar' => $request->meta_title_ar,
            'meta_title_en' => $request->meta_title_en,
            'meta_description_ar' => $request->meta_description_ar,
            'meta_description_en' => $request->meta_description_en,
            'meta_keywords_ar' => $request->meta_keywords_ar,
            'meta_keywords_en' => $request->meta_keywords_en,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        News::create($data);

        return redirect()->route('admin.news.index')->with('success', __('News created successfully.'));
    }

    public function edit(News $news)
    {
        $categories = \App\Models\NewsCategory::where('is_active', true)->get();
        return view('admin.news.edit', compact('news', 'categories'));
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'content_ar' => 'required|string',
            'content_en' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'category_id' => 'nullable|exists:news_categories,id',
            'tags_ar' => 'nullable|string',
            'tags_en' => 'nullable|string',
            'meta_title_ar' => 'nullable|string|max:255',
            'meta_title_en' => 'nullable|string|max:255',
            'meta_description_ar' => 'nullable|string',
            'meta_description_en' => 'nullable|string',
            'meta_keywords_ar' => 'nullable|string',
            'meta_keywords_en' => 'nullable|string',
        ]);

        $data = [
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'content_ar' => $request->content_ar,
            'content_en' => $request->content_en,
            'is_active' => $request->has('is_active') || $request->boolean('is_active'),
            'is_featured' => $request->has('is_featured') || $request->boolean('is_featured'),
            'category_id' => $request->category_id,
            'tags_ar' => $request->tags_ar,
            'tags_en' => $request->tags_en,
            'meta_title_ar' => $request->meta_title_ar,
            'meta_title_en' => $request->meta_title_en,
            'meta_description_ar' => $request->meta_description_ar,
            'meta_description_en' => $request->meta_description_en,
            'meta_keywords_ar' => $request->meta_keywords_ar,
            'meta_keywords_en' => $request->meta_keywords_en,
        ];

        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        $news->update($data);

        return redirect()->route('admin.news.index')->with('success', __('News updated successfully.'));
    }

    public function destroy(News $news)
    {
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }
        
        $news->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => __('News deleted successfully.')]);
        }

        return redirect()->route('admin.news.index')->with('success', __('News deleted successfully.'));
    }

    public function toggleActive(News $news)
    {
        $news->update(['is_active' => !$news->is_active]);

        return response()->json([
            'success' => true,
            'message' => $news->is_active ? __('News activated successfully.') : __('News deactivated successfully.')
        ]);
    }
}
