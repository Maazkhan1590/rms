<?php

namespace App\Services;

use App\Models\PolicyVersion;
use App\Models\ScoringPolicy;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PolicyService
{
    /**
     * Create a new policy version
     *
     * @param string $versionNumber
     * @param int $year
     * @param string|null $description
     * @param int $createdBy
     * @return PolicyVersion
     */
    public function createVersion(
        string $versionNumber,
        int $year,
        ?string $description = null,
        int $createdBy
    ): PolicyVersion {
        DB::transaction(function () use ($versionNumber, $year, $description, $createdBy) {
            // Deactivate all existing versions
            PolicyVersion::where('is_active', true)->update(['is_active' => false]);
            
            // Create new version
            $version = PolicyVersion::create([
                'version_number' => $versionNumber,
                'year' => $year,
                'description' => $description,
                'is_active' => true,
                'created_by' => $createdBy,
            ]);
        });

        return PolicyVersion::where('version_number', $versionNumber)->first();
    }

    /**
     * Activate a policy version
     *
     * @param PolicyVersion $version
     * @return PolicyVersion
     */
    public function activateVersion(PolicyVersion $version): PolicyVersion
    {
        DB::transaction(function () use ($version) {
            // Deactivate all other versions
            PolicyVersion::where('id', '!=', $version->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
            
            // Activate this version
            $version->is_active = true;
            $version->save();
        });

        return $version->fresh();
    }

    /**
     * Create a scoring policy
     *
     * @param array $data
     * @return ScoringPolicy
     */
    public function createPolicy(array $data): ScoringPolicy
    {
        return ScoringPolicy::create($data);
    }

    /**
     * Update a scoring policy
     *
     * @param ScoringPolicy $policy
     * @param array $data
     * @return ScoringPolicy
     */
    public function updatePolicy(ScoringPolicy $policy, array $data): ScoringPolicy
    {
        $policy->update($data);
        return $policy->fresh();
    }

    /**
     * Deactivate a policy
     *
     * @param ScoringPolicy $policy
     * @return ScoringPolicy
     */
    public function deactivatePolicy(ScoringPolicy $policy): ScoringPolicy
    {
        $policy->is_active = false;
        $policy->save();
        return $policy->fresh();
    }

    /**
     * Get active policy version
     *
     * @return PolicyVersion|null
     */
    public function getActiveVersion(): ?PolicyVersion
    {
        return PolicyVersion::active()->first();
    }

    /**
     * Get policies for a specific type and date
     *
     * @param string $type
     * @param \DateTime|string|null $date
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPoliciesForType(string $type, $date = null)
    {
        return ScoringPolicy::active()
            ->ofType($type)
            ->effectiveOn($date)
            ->get();
    }
}

