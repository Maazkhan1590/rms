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
        
        // Find matching policy
        $policy = null;
        if ($policyVersion) {
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
        }

        // Use policy if found, otherwise use defaults
        if ($policy) {
            $points = $policy->points;
            $cap = $policy->cap;

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
            if ($cap && $points > $cap) {
                $points = $cap;
            }
        } else {
            // Use default values
            $defaults = $this->getDefaultPublicationPoints($publication);
            $points = $defaults['points'];
            $cap = $defaults['cap'];
            
            // Apply cap if specified
            if ($cap && $points > $cap) {
                $points = $cap;
            }
        }

        // Update publication
        $publication->points_allocated = $points;
        if ($policyVersion) {
            $publication->policy_version_id = $policyVersion->id;
        }
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
        
        // Find matching policy
        $policy = null;
        if ($policyVersion) {
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
        }

        // Use policy if found, otherwise use defaults
        if ($policy) {
            $points = $policy->points;
            $cap = $policy->cap;

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
            if ($cap && $points > $cap) {
                $points = $cap;
            }
        } else {
            // Use default values
            $points = $this->getDefaultGrantPoints($grant);
        }

        // Update grant
        $grant->points_allocated = $points;
        if ($policyVersion) {
            $grant->policy_version_id = $policyVersion->id;
        }
        $grant->save();

        return $points;
    }

    /**
     * Calculate points for RTN submission
     *
     * @param RtnSubmission $rtn
     * @param PolicyVersion|null $policyVersion
     * @return float
     */
    public function calculateRtnPoints(RtnSubmission $rtn, ?PolicyVersion $policyVersion = null): float
    {
        $policyVersion = $policyVersion ?? PolicyVersion::active()->first();
        
        // Find matching policy
        $policy = null;
        if ($policyVersion) {
            $policy = ScoringPolicy::active()
                ->ofType('rtn')
                ->effectiveOn($rtn->year ?? now()->year)
                ->where('category', $rtn->rtn_type)
                ->first();
        }

        // Use policy if found, otherwise use defaults
        if ($policy) {
            $points = $policy->points;
        } else {
            // Use default values: RTN-3 and RTN-4 both get 5 points
            $points = 5.0;
        }
        
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
        
        // Find matching policy
        $policy = null;
        if ($policyVersion) {
            $policy = ScoringPolicy::active()
                ->ofType('bonus')
                ->effectiveOn($bonus->year)
                ->where('category', $bonus->recognition_type)
                ->first();
        }

        // Use policy if found, otherwise use defaults
        if ($policy) {
            $points = $policy->points;
        } else {
            // Use default values
            $points = $this->getDefaultBonusPoints($bonus->recognition_type);
        }

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

    /**
     * Get default points for a publication based on its type and category
     *
     * @param Publication $publication
     * @return array ['points' => float, 'cap' => float|null]
     */
    private function getDefaultPublicationPoints(Publication $publication): array
    {
        // Default values based on publication type
        $publicationType = $publication->publication_type;
        $journalCategory = $publication->journal_category;

        // Journal (Indexed): 60 points (Cap: 120)
        if ($publicationType === 'journal_paper' && 
            in_array($journalCategory, ['scopus', 'international_refereed']) && 
            $publication->quartile) {
            return ['points' => 60.0, 'cap' => 120.0];
        }

        // Conference Paper: 15 points (Cap: 15)
        if ($publicationType === 'conference_paper') {
            return ['points' => 15.0, 'cap' => 15.0];
        }

        // Book/Chapter: 10 points
        if (in_array($publicationType, ['book', 'book_chapter'])) {
            return ['points' => 10.0, 'cap' => null];
        }

        // Non-indexed Journal: 5 points
        if ($publicationType === 'journal_paper' && 
            ($journalCategory === 'non_indexed' || !$publication->quartile)) {
            return ['points' => 5.0, 'cap' => null];
        }

        // Default fallback
        return ['points' => 5.0, 'cap' => null];
    }

    /**
     * Get default points for a grant based on its type and role
     *
     * @param Grant $grant
     * @return float
     */
    private function getDefaultGrantPoints(Grant $grant): float
    {
        $grantType = strtolower($grant->grant_type ?? '');
        $role = $grant->role ?? '';

        // External Grant (PI): 90 points
        if (($grantType === 'external_grant' || str_contains($grantType, 'external')) && 
            ($role === 'PI' || $role === 'Principal Investigator')) {
            return 90.0;
        }

        // Matching Grant (PI): 15 points
        if (($grantType === 'matching_grant' || str_contains($grantType, 'matching')) && 
            ($role === 'PI' || $role === 'Principal Investigator')) {
            return 15.0;
        }

        // GRG/URG (Advisor): 10 points
        if ((str_contains($grantType, 'grg') || str_contains($grantType, 'urg')) && 
            (str_contains(strtolower($role), 'advisor') || str_contains(strtolower($role), 'mentor'))) {
            return 10.0;
        }

        // Patent (SU-registered): 10 points
        if ((str_contains($grantType, 'patent') || str_contains($grantType, 'copyright')) && 
            $grant->patent_su_registered) {
            return 10.0;
        }

        // Grant Application: 5 points
        if (str_contains($grantType, 'application') || $role === 'Applicant') {
            return 5.0;
        }

        // Co-PI: 5 points
        if ($role === 'Co-PI' || $role === 'Co_PI' || str_contains($role, 'Co-PI')) {
            return 5.0;
        }

        // Co-I: 6 points
        if ($role === 'Co-I' || $role === 'Co_I' || str_contains($role, 'Co-I')) {
            return 6.0;
        }

        // Default fallback
        return 5.0;
    }

    /**
     * Get default points for a bonus recognition type
     *
     * @param string $recognitionType
     * @return float
     */
    private function getDefaultBonusPoints(string $recognitionType): float
    {
        $defaults = [
            'editorial_board' => 5.0,
            'external_examiner' => 6.0,
            'regulatory_body' => 7.0,
            'workshop_seminar' => 8.0,
            'keynote_plenary' => 9.0,
            'journal_reviewer' => 5.0,
        ];

        return $defaults[$recognitionType] ?? 5.0;
    }
}

