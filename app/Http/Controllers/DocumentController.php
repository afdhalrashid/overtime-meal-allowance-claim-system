<?php

namespace App\Http\Controllers;

use App\Models\ClaimDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function view(ClaimDocument $document)
    {
        // Check if user has permission to view this document
        $user = Auth::user();
        $claim = $document->claim;

        // Allow access if:
        // 1. User owns the claim
        // 2. User is an approver and claim is in their approval chain
        // 3. User is HR Admin or Payroll
        if (!($claim->user_id === $user->id ||
              $user->role === 'hr_admin' ||
              $user->role === 'payroll' ||
              ($user->role === 'approver' && $claim->approver_id === $user->id))) {
            abort(403, 'Unauthorized access to document.');
        }

        // Check if file exists
        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Document not found.');
        }

        $filePath = Storage::disk('local')->path($document->file_path);

        // Get mime type using a more reliable method
        $mimeType = mime_content_type($filePath);
        if (!$mimeType) {
            $extension = pathinfo($document->original_name, PATHINFO_EXTENSION);
            $mimeType = match(strtolower($extension)) {
                'pdf' => 'application/pdf',
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'doc' => 'application/msword',
                default => 'application/octet-stream'
            };
        }

        // For images and PDFs, display inline. For others, force download
        $disposition = in_array($mimeType, [
            'application/pdf',
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif'
        ]) ? 'inline' : 'attachment';

        return Response::file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $disposition . '; filename="' . $document->original_name . '"'
        ]);
    }
}
