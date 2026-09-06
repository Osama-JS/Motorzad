<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\KycRequest;

class KycDocumentController extends Controller
{
    public function show(Request $request, $path)
    {
        // Allow access if the URL has a valid signature (used for API/Mobile)
        if ($request->hasValidSignature()) {
            return $this->serveFile($path);
        }

        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        // Allow access for Admins
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return $this->serveFile($path);
        }

        // Check if the current user owns this document
        $kycRequest = KycRequest::where('user_id', $user->id)
            ->where(function($query) use ($path) {
                $query->where('id_front_image', $path)
                      ->orWhere('id_back_image', $path)
                      ->orWhere('passport_image', $path)
                      ->orWhere('selfie_image', $path);
            })->first();

        if ($kycRequest) {
            return $this->serveFile($path);
        }

        abort(403, 'Unauthorized access to KYC document.');
    }

    private function serveFile($path)
    {
        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Document not found.');
        }

        return Storage::disk('local')->response($path);
    }
}
