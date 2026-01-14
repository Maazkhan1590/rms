<?php

namespace App\Services;

use App\Models\ScoringPolicy;
use App\Models\ScoringRule;
use App\Models\PolicyVersion;
use App\Models\Publication;
use App\Models\Grant;
use App\Models\RtnSubmission;
use App\Models\BonusRecognition;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScoringService
{
    /**
     * Calculate points for a publication based on scoring policies
     *
     * @param Publication $publication
     * @param PolicyVersion|null $policyVersion
     * @return float
     */
    public function calculatePublicationPoints(Publication $publication, ?PolicyVersion $policyVersion = null): float
    {
        if ($publication->points_locked) {
            return $publication->points_allocated;
        }

        $policyVersion = $policyVersion ?? PolicyVersion::active()->first();
        
        if (!$policyVersion) {
            Log::warning("No active policy version found for publication {$publication->id}");
            return 0;
        }

        // Find matching policy
        $policy = ScoringPolicy::active()
            ->ofType('publication')
            ->effectiveOn($publication->year ?? now()->year)
            ->where(function ($query) use ($publication) {
                $query->where('category', $publication->journal_category)
                    ->orWhereNull('category');
            })
            ->where(function ($query) use ($publication) {
                if ($publication->quartile) {
                    $query->where('subcategory', $publication->quartile)
                        ->orWhereNull('subcategory');
                } else {
                    $query->whereNull('subcategory');
                }
            })
            ->first();

        if (!$policy) {
            Log::warning("No matching policy found for publication {$publication->id}");
            return 0;
        }

        $points = $policy->points;

        // Apply rules if any
        $rules = ScoringRule::where('policy_id', $policy->id)
            ->active()
            ->orderedByPriority()
            ->get();

        foreach ($rules as $rule) {
            if ($this->ruleMatches($rule, $publication)) {
                $points = $rule->points;
                break; // First matching rule wins
            }
        }

        // Apply cap if specified
        if ($policy->cap && $points > $policy->cap) {
            $points = $policy->cap;
        }

        // Update publication
        $publication->points_allocated = $points;
        $publication->policy_version_id = $policyVersion->id;
        $publication->save();

        return $points;
    }

    /**
     * Calculate points for a grant based on scoring policies
     *
     * @param Grant $grant
     * @param PolicyVersion|null $policyVersion
     * @return float
     */
    public function calculateGrantPoints(Grant $grant, ?PolicyVersion $policyVersion = null): float
    {
        if ($grant->points_locked) {
            return $grant->points_allocated;
        }

        $policyVersion = $policyVersion ?? PolicyVersion::active()->first();
        
        if (!$policyVersion) {
            Log::warning("No active policy version found for grant {$grant->id}");
            return 0;
        }

        // Find matching policy
        $policy = ScoringPolicy::active()
            ->ofType('grant')
            ->effectiveOn($grant->award_year ?? now()->year)
            ->where(function ($query) use ($grant) {
                $query->where('category', $grant->grant_type)
                    ->orWhereNull('category');
            })
            ->where(function ($query) use ($grant) {
                if ($grant->role) {
                    $query->where('subcategory', $grant->role)
                        ->orWhereNull('subcategory');
                } else {
                    $query->whereNull('subcategory');
                }
            })
            ->first();

        if (!$policy) {
            Log::warning("No matching policy found for grant {$grant->id}");
            return 0;
        }

        $points = $policy->points;

        // Apply rules if any
        $rules = ScoringRule::where('policy_id', $policy->id)
            ->active()
            ->orderedByPriority()
            ->get();

        foreach ($rules as $rule) {
            if ($this->ruleMatches($rule, $grant)) {
                $points = $rule->points;
                break;
            }
        }

        // Apply cap if specified
        if ($policy->cap && $points > $policy->cap) {
            $points = $policy->cap;
        }

        // Update grant
        $grant->points_allocated = $points;
        $grant->policy_version_id = $policyVersion->id;
        $grant->save();

        return $points;
    }

    /**
     * Calculate points for RTN submission
     *
     * @param RtnSubmission $rtn
     * @return float
     */
    public function calculateRtnPoints(RtnSubmission $rtn): float
    {
        // RTN-3 and RTN-4 both get 5 points
        $points = 5.0;
        
        $rtn->points = $points;
        $rtn->save();

        return $points;
    }

    /**
     * Calculate points for bonus recognition
     *
     * @param BonusRecognition $bonus
     * @param PolicyVersion|null $policyVersion
     * @return float
     */
    public function calculateBonusPoints(BonusRecognition $bonus, ?PolicyVersion $policyVersion = null): float
    {
        $policyVersion = $policyVersion ?? PolicyVersion::active()->first();
        
        if (!$policyVersion) {
            Log::warning("No active policy version found for bonus recognition {$bonus->id}");
            return 0;
        }

        // Find matching policy
        $policy = ScoringPolicy::active()
            ->ofType('bonus')
            ->effectiveOn($bonus->year)
            ->where('category', $bonus->recognition_type)
            ->first();

        if (!$policy) {
            Log::warning("No matching policy found for bonus recognition {$bonus->id}");
            return 0;
        }

        $points = $policy->points;

        // Check yearly cap (25 points per user per year)
        $yearlyTotal = BonusRecognition::where('user_id', $bonus->user_id)
            ->where('year', $bonus->year)
            ->where('id', '!=', $bonus->id)
            ->sum('points');

        $remainingCap = 25 - $yearlyTotal;
        if ($points > $remainingCap) {
            $points = max(0, $remainingCap);
        }

        $bonus->points = $points;
        $bonus->save();

        return $points;
    }

    /**
     * Recalculate total points for a user
     *
     * @param int $userId
     * @param int|null $year
     * @return float
     */
    public function recalculateUserTotalPoints(int $userId, ?int $year = null): float
    {
        $year = $year ?? now()->year;

        $total = 0;

        // Publications
        $publicationPoints = Publication::where('primary_author_id', $userId)
            ->where('year', $year)
            ->where('status', 'approved')
            ->sum('points_allocated');

        // Grants
        $grantPoints = Grant::where('submitted_by', $userId)
            ->where('award_year', $year)
            ->where('status', 'approved')
            ->sum('points_allocated');

        // RTN
        $rtnPoints = RtnSubmission::where('user_id', $userId)
            ->where('year', $year)
            ->where('status', 'approved')
            ->sum('points');

        // Bonus (capped at 25)
        $bonusPoints = min(25, BonusRecognition::where('user_id', $userId)
            ->where('year', $year)
            ->where('status', 'approved')
            ->sum('points'));

        $total = $publicationPoints + $grantPoints + $rtnPoints + $bonusPoints;

        // Update user
        $user = \App\Models\User::find($userId);
        if ($user) {
            $user->total_research_points = $total;
            $user->last_points_calculation = now();
            $user->save();
        }

        return $total;
    }

    /**
     * Check if a rule matches the given model
     *
     * @param ScoringRule $rule
     * @param mixed $model
     * @return bool
     */
    private function ruleMatches(ScoringRule $rule, $model): bool
    {
        $conditions = $rule->conditions ?? [];

        if (empty($conditions)) {
            return true; // No conditions means always match
        }

        foreach ($conditions as $field => $value) {
            if (!isset($model->$field) || $model->$field != $value) {
                return false;
            }
        }

        return true;
    }
}

