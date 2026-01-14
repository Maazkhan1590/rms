<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Department;
use Illuminate\Http\JsonResponse;

class CollegeController extends Controller
{
    /**
     * Get departments for a college
     * For registration, we show all departments (active and inactive) so users can select
     */
    public function departments($college): JsonResponse
    {
        try {
            // Handle route parameter (should be numeric ID)
            $collegeId = (int) $college;
            
            // Verify college exists (check both active and inactive for registration)
            $collegeExists = College::where('id', $collegeId)->exists();
            
            if (!$collegeExists) {
                return response()->json(['error' => 'College not found'], 404);
            }
            
            // Get all departments (both active and inactive) for registration
            // Inactive departments will be marked in the response
            $departments = Department::where('college_id', $collegeId)
                ->orderBy('is_active', 'desc') // Active first
                ->orderBy('name')
                ->get(['id', 'name', 'is_active']);

            // Format response to include active status
            $formattedDepartments = $departments->map(function ($dept) {
                return [
                    'id' => $dept->id,
                    'name' => $dept->name . ($dept->is_active ? '' : ' (Inactive)'),
                    'is_active' => $dept->is_active
                ];
            });

            return response()->json($formattedDepartments);
        } catch (\Exception $e) {
            \Log::error('Error loading departments: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load departments'], 500);
        }
    }
}

