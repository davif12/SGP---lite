<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class AttachmentController extends Controller
{
    /**
     * Upload attachment
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // 10MB max
            'attachable_type' => 'required|string',
            'attachable_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('file');
        
        // Validate file type
        $allowedTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'application/zip'
        ];

        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de arquivo não permitido.'
            ], 422);
        }

        // Generate unique filename
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        // Store file
        $path = $file->storeAs('attachments', $filename, 'public');

        // Create attachment record
        $attachment = Attachment::create([
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
            'attachable_type' => $request->attachable_type,
            'attachable_id' => $request->attachable_id,
            'uploaded_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'attachment' => [
                'id' => $attachment->id,
                'original_name' => $attachment->original_name,
                'size' => $attachment->human_size,
                'mime_type' => $attachment->mime_type,
                'is_image' => $attachment->is_image,
                'icon' => $attachment->icon,
                'color' => $attachment->color,
                'url' => $attachment->url,
                'thumbnail_url' => $attachment->thumbnail_url,
                'uploaded_by' => $attachment->uploader->name,
                'created_at' => $attachment->created_at->format('d/m/Y H:i'),
            ]
        ]);
    }

    /**
     * List attachments for a specific attachable
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attachable_type' => 'required|string',
            'attachable_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $attachments = Attachment::forAttachable(
            $request->attachable_type,
            $request->attachable_id
        )
        ->with('uploader')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($attachment) {
            return [
                'id' => $attachment->id,
                'original_name' => $attachment->original_name,
                'size' => $attachment->human_size,
                'mime_type' => $attachment->mime_type,
                'is_image' => $attachment->is_image,
                'icon' => $attachment->icon,
                'color' => $attachment->color,
                'url' => $attachment->url,
                'thumbnail_url' => $attachment->thumbnail_url,
                'uploaded_by' => $attachment->uploader->name,
                'created_at' => $attachment->created_at->format('d/m/Y H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'attachments' => $attachments
        ]);
    }

    /**
     * Download attachment
     */
    public function download(Attachment $attachment)
    {
        // Check if user has access to this attachment
        if (!$this->canAccessAttachment($attachment)) {
            abort(403, 'Acesso negado.');
        }

        if (!Storage::disk('public')->exists($attachment->path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::disk('public')->download(
            $attachment->path,
            $attachment->original_name
        );
    }

    /**
     * Get thumbnail for image
     */
    public function thumbnail(Attachment $attachment)
    {
        // Check if user has access to this attachment
        if (!$this->canAccessAttachment($attachment)) {
            abort(403, 'Acesso negado.');
        }

        if (!$attachment->is_image) {
            abort(404, 'Não é uma imagem.');
        }

        if (!Storage::disk('public')->exists($attachment->path)) {
            abort(404, 'Arquivo não encontrado.');
        }

        $thumbnailPath = 'thumbnails/' . $attachment->filename;

        // Generate thumbnail if it doesn't exist
        if (!Storage::disk('public')->exists($thumbnailPath)) {
            $this->generateThumbnail($attachment, $thumbnailPath);
        }

        return response()->file(
            Storage::disk('public')->path($thumbnailPath)
        );
    }

    /**
     * Delete attachment
     */
    public function destroy(Attachment $attachment)
    {
        // Check if user can delete this attachment
        if (!$this->canDeleteAttachment($attachment)) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para excluir este anexo.'
            ], 403);
        }

        $attachment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Anexo excluído com sucesso.'
        ]);
    }

    /**
     * Check if user can access attachment
     */
    private function canAccessAttachment(Attachment $attachment): bool
    {
        $attachable = $attachment->attachable;

        if (!$attachable) {
            return false;
        }

        // For tasks and comments, check project access
        if ($attachable instanceof \App\Models\Task) {
            return auth()->user()->accessibleProjects()
                ->where('projects.id', $attachable->epic->project_id)
                ->exists();
        }

        if ($attachable instanceof \App\Models\Comment) {
            return auth()->user()->accessibleProjects()
                ->where('projects.id', $attachable->task->epic->project_id)
                ->exists();
        }

        return false;
    }

    /**
     * Check if user can delete attachment
     */
    private function canDeleteAttachment(Attachment $attachment): bool
    {
        // User can delete their own attachments
        if ($attachment->uploaded_by === auth()->id()) {
            return true;
        }

        // Project owner can delete any attachment in their project
        $attachable = $attachment->attachable;
        
        if ($attachable instanceof \App\Models\Task) {
            return $attachable->epic->project->owner_id === auth()->id();
        }

        if ($attachable instanceof \App\Models\Comment) {
            return $attachable->task->epic->project->owner_id === auth()->id();
        }

        return false;
    }

    /**
     * Generate thumbnail for image
     */
    private function generateThumbnail(Attachment $attachment, string $thumbnailPath)
    {
        try {
            $image = Image::make(Storage::disk('public')->path($attachment->path));
            $image->fit(200, 200, function ($constraint) {
                $constraint->upsize();
            });
            
            Storage::disk('public')->put(
                $thumbnailPath,
                $image->encode('jpg', 80)->__toString()
            );
        } catch (\Exception $e) {
            // If thumbnail generation fails, copy original
            Storage::disk('public')->copy($attachment->path, $thumbnailPath);
        }
    }
}
