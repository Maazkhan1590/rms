<?php

namespace App\Services;

use App\Models\User;
use App\Models\Publication;
use App\Models\Grant;
use App\Models\RtnSubmission;
use App\Models\BonusRecognition;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Generate CV data for a user
     *
     * @param User $user
     * @param int|null $year
     * @return array
     */
    public function generateCVData(User $user, ?int $year = null): array
    {
        $year = $year ?? now()->year;

        return [
            'user' => $user,
            'publications' => $this->getUserPublications($user, $year),
            'grants' => $this->getUserGrants($user, $year),
            'rtn_submissions' => $this->getUserRtnSubmissions($user, $year),
            'bonus_recognitions' => $this->getUserBonusRecognitions($user, $year),
            'total_points' => $user->total_research_points,
            'year' => $year,
        ];
    }

    /**
     * Generate institutional summary report
     *
     * @param int|null $year
     * @param string|null $college
     * @param string|null $department
     * @return array
     */
    public function generateInstitutionalReport(?int $year = null, ?string $college = null, ?string $department = null): array
    {
        $year = $year ?? now()->year;

        $query = User::query();

        if ($college) {
            $query->whereHas('college', function ($q) use ($college) {
                $q->where('name', $college);
            });
        }

        if ($department) {
            $query->whereHas('department', function ($q) use ($department) {
                $q->where('name', $department);
            });
        }

        $users = $query->get();

        $totalPublications = Publication::where('year', $year)
            ->where('status', 'approved')
            ->when($college, function ($q) use ($college) {
                $q->where('college', $college);
            })
            ->when($department, function ($q) use ($department) {
                $q->where('department', $department);
            })
            ->count();

        $totalGrants = Grant::where('award_year', $year)
            ->where('status', 'approved')
            ->count();

        $totalPoints = $users->sum('total_research_points');

        return [
            'year' => $year,
            'college' => $college,
            'department' => $department,
            'total_users' => $users->count(),
            'total_publications' => $totalPublications,
            'total_grants' => $totalGrants,
            'total_points' => $totalPoints,
            'users' => $users->map(function ($user) use ($year) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'total_points' => $user->total_research_points,
                    'publications_count' => $user->publications()->where('year', $year)->count(),
                    'grants_count' => $user->grants()->where('award_year', $year)->count(),
                ];
            }),
        ];
    }

    /**
     * Get user publications
     *
     * @param User $user
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getUserPublications(User $user, int $year)
    {
        return Publication::where('primary_author_id', $user->id)
            ->where('year', $year)
            ->where('status', 'approved')
            ->orderBy('year', 'desc')
            ->get();
    }

    /**
     * Get user grants
     *
     * @param User $user
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getUserGrants(User $user, int $year)
    {
        return Grant::where('submitted_by', $user->id)
            ->where('award_year', $year)
            ->where('status', 'approved')
            ->orderBy('award_year', 'desc')
            ->get();
    }

    /**
     * Get user RTN submissions
     *
     * @param User $user
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getUserRtnSubmissions(User $user, int $year)
    {
        return RtnSubmission::where('user_id', $user->id)
            ->where('year', $year)
            ->where('status', 'approved')
            ->get();
    }

    /**
     * Get user bonus recognitions
     *
     * @param User $user
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getUserBonusRecognitions(User $user, int $year)
    {
        return BonusRecognition::where('user_id', $user->id)
            ->where('year', $year)
            ->where('status', 'approved')
            ->get();
    }
}

