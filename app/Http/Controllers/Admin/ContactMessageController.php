<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Contact;

class ContactMessageController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Contact::count(),
            'new' => Contact::where('is_read', false)->count(),
            'read' => Contact::where('is_read', true)->count(),
        ];
        return view('admin.contacts.index', compact('stats'));
    }

    public function getData(Request $request)
    {
        $query = Contact::query();

        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($request->status) {
            if ($request->status === 'new') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }

        $perPage = $request->per_page ?? 10;
        $messages = $query->latest()->paginate($perPage);

        $data = [];
        foreach ($messages as $msg) {
            $data[] = [
                'id' => $msg->id,
                'name' => $msg->name,
                'email' => $msg->email,
                'subject' => \Illuminate\Support\Str::limit($msg->subject, 50),
                'is_read' => (bool)$msg->is_read,
                'date' => $msg->created_at->format('Y-m-d H:i'),
                'view_url' => route('admin.contacts.show', $msg->id),
                'delete_url' => route('admin.contacts.destroy', $msg->id),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $messages->total(),
                'current_page' => $messages->currentPage(),
                'links' => $messages->linkCollection()->toArray()
            ]
        ]);
    }

    public function show(Contact $contact)
    {
        if (!$contact->is_read) {
            $contact->update(['is_read' => true, 'read_at' => now()]);
        }
        return view('admin.contacts.show', compact('contact'));
    }

    public function destroy(Contact $contact, Request $request)
    {
        $contact->delete();
        
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->route('admin.contacts.index')->with('success', __('Message deleted successfully.'));
    }
}
