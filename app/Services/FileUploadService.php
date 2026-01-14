<?php

namespace App\Services;

use App\Models\EvidenceFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Upload an evidence file
     *
     * @param UploadedFile $file
     * @param string $submissionType
     * @param int $submissionId
     * @param int $uploadedBy
     * @param string $category
     * @return EvidenceFile
     */
    public function uploadEvidenceFile(
        UploadedFile $file,
        string $submissionType,
        int $submissionId,
        int $uploadedBy,
        string $category = 'other'
    ): EvidenceFile {
        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $originalName = $file->getClientOriginalName();
        
        // Store file
        $path = $file->storeAs(
            "evidence/{$submissionType}/" . date('Y/m'),
            $filename,
            'public'
        );

        // Create database record
        $evidenceFile = EvidenceFile::create([
            'submission_type' => $submissionType,
            'submission_id' => $submissionId,
            'file_path' => $path,
            'file_name' => $originalName,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'file_category' => $category,
            'uploaded_by' => $uploadedBy,
            'uploaded_at' => now(),
        ]);

        return $evidenceFile;
    }

    /**
     * Verify an evidence file
     *
     * @param EvidenceFile $evidenceFile
     * @param int $verifiedBy
     * @return EvidenceFile
     */
    public function verifyFile(EvidenceFile $evidenceFile, int $verifiedBy): EvidenceFile
    {
        $evidenceFile->is_verified = true;
        $evidenceFile->verified_by = $verifiedBy;
        $evidenceFile->verified_at = now();
        $evidenceFile->save();

        return $evidenceFile;
    }

    /**
     * Delete an evidence file
     *
     * @param EvidenceFile $evidenceFile
     * @return bool
     */
    public function deleteFile(EvidenceFile $evidenceFile): bool
    {
        // Delete from storage
        if (Storage::disk('public')->exists($evidenceFile->file_path)) {
            Storage::disk('public')->delete($evidenceFile->file_path);
        }

        // Delete database record
        return $evidenceFile->delete();
    }

    /**
     * Get file URL
     *
     * @param EvidenceFile $evidenceFile
     * @return string
     */
    public function getFileUrl(EvidenceFile $evidenceFile): string
    {
        return Storage::disk('public')->url($evidenceFile->file_path);
    }

    /**
     * Get file download path
     *
     * @param EvidenceFile $evidenceFile
     * @return string
     */
    public function getDownloadPath(EvidenceFile $evidenceFile): string
    {
        return Storage::disk('public')->path($evidenceFile->file_path);
    }
}

