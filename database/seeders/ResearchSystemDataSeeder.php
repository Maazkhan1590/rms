<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ExcelReaderService;
use App\Services\ExcelImportService;
use App\Models\User;
use App\Models\Publication;
use App\Models\Grant;
use App\Models\College;
use App\Models\Department;
use App\Models\RtnSubmission;
use App\Models\BonusRecognition;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ResearchSystemDataSeeder extends Seeder
{
    protected ExcelReaderService $excelReader;
    protected ExcelImportService $excelImport;
    protected string $filePath;

    public function __construct()
    {
        $this->filePath = base_path('Research System.xlsx');
        $this->excelReader = new ExcelReaderService();
        $this->excelImport = new ExcelImportService($this->excelReader);
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!file_exists($this->filePath)) {
            $this->command->error("Excel file not found: {$this->filePath}");
            return;
        }

        $this->command->info("Starting data import from Excel file...");

        try {
            // Get all sheet names
            $sheetNames = $this->excelReader->getSheetNames($this->filePath);
            $this->command->info("Found " . count($sheetNames) . " sheets: " . implode(', ', $sheetNames));

            // Process each sheet
            foreach ($sheetNames as $sheetName) {
                $this->command->info("\nProcessing sheet: {$sheetName}");
                $this->processSheet($sheetName);
            }

            $this->command->info("\nData import completed successfully!");
        } catch (\Exception $e) {
            $this->command->error("Error during import: " . $e->getMessage());
            Log::error("Excel import error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process a specific sheet based on its name
     */
    protected function processSheet(string $sheetName): void
    {
        try {
            $sheet = $this->excelReader->getSheet($this->filePath, $sheetName);
            
            // Try to find headers in multiple rows (1, 2, or 3)
            $headers = $this->findHeaders($sheet);
            $headerRow = $headers['row'];
            $headers = $headers['headers'];
            
            if (empty($headers)) {
                $this->command->warn("  No headers found in sheet '{$sheetName}', skipping...");
                return;
            }

            $this->command->info("  Headers (row {$headerRow}): " . implode(', ', array_slice($headers, 0, 10)) . (count($headers) > 10 ? '...' : ''));

            // Route to appropriate import method based on sheet name
            $sheetNameLower = strtolower($sheetName);
            
            if (str_contains($sheetNameLower, 'user') || str_contains($sheetNameLower, 'staff') || str_contains($sheetNameLower, 'master')) {
                $this->importUsers($sheetName, $sheet, $headers, $headerRow);
            } elseif (str_contains($sheetNameLower, 'college')) {
                $this->importColleges($sheetName, $sheet, $headers, $headerRow);
            } elseif (str_contains($sheetNameLower, 'department')) {
                $this->importDepartments($sheetName, $sheet, $headers, $headerRow);
            } elseif (str_contains($sheetNameLower, 'publication')) {
                $this->importPublications($sheetName, $sheet, $headers, $headerRow);
            } elseif (str_contains($sheetNameLower, 'grant') || str_contains($sheetNameLower, 'funding') || str_contains($sheetNameLower, 'consultancy') || str_contains($sheetNameLower, 'internal_funding') || str_contains($sheetNameLower, 'internal_grant')) {
                $this->importGrants($sheetName, $sheet, $headers, $headerRow);
            } elseif (str_contains($sheetNameLower, 'rtn')) {
                $this->importRTNs($sheetName, $sheet, $headers, $headerRow);
            } elseif (str_contains($sheetNameLower, 'bonus') || str_contains($sheetNameLower, 'recognition') || str_contains($sheetNameLower, 'award')) {
                $this->importBonusRecognitions($sheetName, $sheet, $headers, $headerRow);
            } elseif (str_contains($sheetNameLower, 'editorial')) {
                $this->importEditorialAppointments($sheetName, $sheet, $headers, $headerRow);
            } elseif (str_contains($sheetNameLower, 'supervision') || str_contains($sheetNameLower, 'exam')) {
                $this->importSupervisionExams($sheetName, $sheet, $headers, $headerRow);
            } elseif (str_contains($sheetNameLower, 'research') && str_contains($sheetNameLower, 'investment')) {
                $this->importResearchInvestments($sheetName, $sheet, $headers, $headerRow);
            } elseif (str_contains($sheetNameLower, 'sdg') && str_contains($sheetNameLower, 'contribution')) {
                $this->importSdgContributions($sheetName, $sheet, $headers, $headerRow);
            } elseif (str_contains($sheetNameLower, 'conference')) {
                $this->importConferenceActivities($sheetName, $sheet, $headers, $headerRow);
            } elseif (str_contains($sheetNameLower, 'partnership') || str_contains($sheetNameLower, 'mou')) {
                $this->importPartnerships($sheetName, $sheet, $headers, $headerRow);
            } elseif (str_contains($sheetNameLower, 'commercialization')) {
                $this->importCommercializations($sheetName, $sheet, $headers, $headerRow);
            } elseif (str_contains($sheetNameLower, 'consultanc') && (str_contains($sheetNameLower, 'kt') || str_contains($sheetNameLower, 'knowledge'))) {
                $this->importConsultanciesKt($sheetName, $sheet, $headers, $headerRow);
            } elseif (str_contains($sheetNameLower, 'research_fellow') || str_contains($sheetNameLower, 'adjunct')) {
                $this->importResearchFellows($sheetName, $sheet, $headers, $headerRow);
            } elseif (str_contains($sheetNameLower, 'home') || str_contains($sheetNameLower, 'summary') || str_contains($sheetNameLower, 'dashboard') || str_contains($sheetNameLower, 'helper') || str_contains($sheetNameLower, 'config') || str_contains($sheetNameLower, 'kpi') || str_contains($sheetNameLower, 'table') || str_contains($sheetNameLower, 'sdg_list') || str_contains($sheetNameLower, 'sdg_mapping')) {
                // Skip summary/dashboard/helper sheets
                $this->command->info("  Skipping summary/dashboard sheet '{$sheetName}'");
            } else {
                $this->command->warn("  Unknown sheet type '{$sheetName}', attempting generic import...");
                $this->importGeneric($sheetName, $sheet, $headers);
            }
        } catch (\Exception $e) {
            $this->command->error("  Error processing sheet '{$sheetName}': " . $e->getMessage());
            Log::error("Error processing sheet '{$sheetName}': " . $e->getMessage());
        }
    }

    /**
     * Import users/staff from sheet
     */
    protected function importUsers(string $sheetName, $sheet, array $headers, int $headerRow = 1): void
    {
        $imported = 0;
        $failed = 0;
        $skipped = 0;
        $highestRow = $sheet->getHighestRow();

        DB::beginTransaction();
        try {
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    $rowData = $this->getRowData($sheet, $headers, $row, $headerRow);
                    
                    if (empty(array_filter($rowData))) {
                        continue;
                    }

                    // Map Excel columns to database fields
                    $data = [];
                    
                    // Try different possible column names
                    $name = $this->getValue($rowData, ['Name', 'name', 'staff_name', 'Staff Name', 'Author Name', 'author_name']);
                    $email = $this->getValue($rowData, ['Email', 'email', 'Email Address']);
                    $employeeId = $this->getValue($rowData, ['Employee ID', 'employee_id', 'Employee ID', 'ID']);
                    $designation = $this->getValue($rowData, ['Designation', 'designation', 'Position', 'position']);
                    $phone = $this->getValue($rowData, ['Phone', 'phone', 'Phone Number', 'Mobile']);
                    $collegeName = $this->getValue($rowData, ['College', 'college', 'Faculty', 'faculty', 'sohar Affiliation', 'Affiliation']);
                    $departmentName = $this->getValue($rowData, ['Department', 'department', 'Dept']);
                    
                    // Research profile fields
                    $googleScholar = $this->getValue($rowData, ['Google scholar link', 'Google Scholar', 'google_scholar', 'Google scholar']);
                    $orcid = $this->getValue($rowData, ['ORCID', 'ORCID Connected', 'orcid', 'ORCID ID']);
                    $scopusScholar = $this->getValue($rowData, ['Scopus scholar link', 'Scopus', 'scopus', 'Scopus Link']);
                    $citationNumber = $this->getValue($rowData, ['Citation number', 'Citation', 'Citations', 'citation']);
                    $hIndex = $this->getValue($rowData, ['H-index', 'H-index', 'h_index', 'H Index']);

                    if (empty($name) && empty($email)) {
                        continue;
                    }

                    // Find existing user by email or employee_id
                    $user = null;
                    if ($email) {
                        $user = User::where('email', $email)->first();
                    }
                    if (!$user && $employeeId) {
                        $user = User::where('employee_id', $employeeId)->first();
                    }

                    // Prepare data for update/create
                    $updateData = [];
                    $createData = [];

                    // Only set fields that are provided in Excel
                    if ($name) $updateData['name'] = $createData['name'] = $name;
                    if ($email) $updateData['email'] = $createData['email'] = $email;
                    if ($employeeId) $updateData['employee_id'] = $createData['employee_id'] = $employeeId;
                    if ($designation) $updateData['designation'] = $createData['designation'] = $designation;
                    if ($phone) $updateData['phone'] = $createData['phone'] = $phone;
                    if ($googleScholar) $updateData['google_scholar'] = $createData['google_scholar'] = $googleScholar;
                    if ($orcid) $updateData['orcid'] = $createData['orcid'] = $orcid;
                    if ($scopusScholar) $updateData['research_gate'] = $createData['research_gate'] = $scopusScholar;

                    // Resolve college
                    if ($collegeName) {
                        $college = College::where('name', 'like', "%{$collegeName}%")->first();
                        if ($college) {
                            $updateData['college_id'] = $createData['college_id'] = $college->id;
                        }
                    }

                    // Resolve department
                    if ($departmentName) {
                        $collegeId = $updateData['college_id'] ?? $user?->college_id;
                        if ($collegeId) {
                            $department = Department::where('name', 'like', "%{$departmentName}%")
                                ->where('college_id', $collegeId)
                                ->first();
                            if ($department) {
                                $updateData['department_id'] = $createData['department_id'] = $department->id;
                            }
                        }
                    }

                    if ($user) {
                        // User exists - only update if it's not a system user (admin, etc.)
                        // Check if user is a system user by checking if they have roles or specific emails
                        $isSystemUser = in_array($user->email, ['admin@admin.com', 'admin@example.com', 'dean@example.com', 'coordinator@example.com', 'faculty@example.com'])
                            || $user->roles()->exists();
                        
                        if ($isSystemUser) {
                            // Don't update system users, just skip
                            $skipped++;
                            continue;
                        }
                        
                        // Update existing user but preserve password and status if already set
                        if (!isset($updateData['status'])) {
                            $updateData['status'] = $user->status ?: 'active';
                        }
                        // Don't update password if user already has one
                        if (!$user->password) {
                            $updateData['password'] = bcrypt('password');
                        }
                        
                        $user->update($updateData);
                        $imported++;
                    } else {
                        // Create new user
                        if (empty($createData['email'])) {
                            if ($employeeId) {
                                $createData['email'] = $employeeId . '@example.com';
                            } else {
                                $skipped++;
                                continue; // Can't create user without email or employee_id
                            }
                        }
                        
                        // Check if email already exists (double check)
                        if (User::where('email', $createData['email'])->exists()) {
                            $skipped++;
                            continue;
                        }
                        
                        // Set defaults for new user
                        $createData['name'] = $createData['name'] ?? 'Unknown';
                        $createData['status'] = 'active';
                        $createData['password'] = bcrypt('password');
                        
                        User::create($createData);
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $failed++;
                    if ($failed <= 5) {
                        $this->command->warn("    Row {$row}: " . $e->getMessage());
                    }
                }
            }

            DB::commit();
            $skipReason = $skipped > 0 ? " (skipped: {$skipped} existing/system users preserved)" : "";
            $this->command->info("  Imported: {$imported}, Failed: {$failed}{$skipReason}");
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import colleges from sheet
     */
    protected function importColleges(string $sheetName, $sheet, array $headers, int $headerRow = 1): void
    {
        $imported = 0;
        $failed = 0;
        $highestRow = $sheet->getHighestRow();

        DB::beginTransaction();
        try {
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    $rowData = $this->getRowData($sheet, $headers, $row, $headerRow);
                    
                    if (empty(array_filter($rowData))) {
                        continue;
                    }

                    $name = $this->getValue($rowData, ['Name', 'name', 'College Name', 'College']);
                    $code = $this->getValue($rowData, ['Code', 'code', 'College Code']);

                    if (empty($name)) {
                        continue;
                    }

                    $data = [
                        'name' => $name,
                        'code' => $code ?: strtoupper(substr($name, 0, 3)),
                        'is_active' => true,
                    ];

                    College::firstOrCreate(
                        ['code' => $data['code']],
                        $data
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    if ($failed <= 5) {
                        $this->command->warn("    Row {$row}: " . $e->getMessage());
                    }
                }
            }

            DB::commit();
            $this->command->info("  Imported: {$imported}, Failed: {$failed}");
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import departments from sheet
     */
    protected function importDepartments(string $sheetName, $sheet, array $headers, int $headerRow = 1): void
    {
        $imported = 0;
        $failed = 0;
        $highestRow = $sheet->getHighestRow();

        DB::beginTransaction();
        try {
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    $rowData = $this->getRowData($sheet, $headers, $row, $headerRow);
                    
                    if (empty(array_filter($rowData))) {
                        continue;
                    }

                    $name = $this->getValue($rowData, ['Name', 'name', 'Department Name', 'Department']);
                    $code = $this->getValue($rowData, ['Code', 'code', 'Department Code']);
                    $collegeName = $this->getValue($rowData, ['College', 'college', 'College Name', 'Faculty']);

                    if (empty($name)) {
                        continue;
                    }

                    // Find college
                    $college = null;
                    if ($collegeName) {
                        $college = College::where('name', 'like', "%{$collegeName}%")->first();
                    }

                    if (!$college) {
                        continue;
                    }

                    $data = [
                        'college_id' => $college->id,
                        'name' => $name,
                        'code' => $code ?: strtoupper(substr($name, 0, 3)),
                        'is_active' => true,
                    ];

                    Department::firstOrCreate(
                        ['college_id' => $data['college_id'], 'code' => $data['code']],
                        $data
                    );

                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    if ($failed <= 5) {
                        $this->command->warn("    Row {$row}: " . $e->getMessage());
                    }
                }
            }

            DB::commit();
            $this->command->info("  Imported: {$imported}, Failed: {$failed}");
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import publications from sheet
     */
    protected function importPublications(string $sheetName, $sheet, array $headers, int $headerRow = 1): void
    {
        $imported = 0;
        $failed = 0;
        $highestRow = $sheet->getHighestRow();

        DB::beginTransaction();
        try {
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    $rowData = $this->getRowData($sheet, $headers, $row, $headerRow);
                    
                    if (empty(array_filter($rowData))) {
                        continue;
                    }

                    $title = $this->getValue($rowData, ['Title', 'title', 'research_title', 'Research Title', 'Publication Title']);
                    $authorName = $this->getValue($rowData, ['Author', 'author', 'author_name', 'Author Name', 'Staff Name']);
                    $type = $this->getValue($rowData, ['Type', 'type', 'Publication Type']);
                    $indexed = $this->getValue($rowData, ['Indexed', 'indexed_', 'Indexed?', 'Journal Category']);
                    $quartile = $this->getValue($rowData, ['Quartile', 'quartile', 'Q']);
                    $year = $this->getValue($rowData, ['Year', 'year', 'Publication Year']);
                    $journal = $this->getValue($rowData, ['Journal', 'journal', 'Journal Name']);
                    $doi = $this->getValue($rowData, ['DOI', 'doi']);

                    if (empty($title)) {
                        continue;
                    }

                    // Find author user
                    $author = null;
                    if ($authorName) {
                        $author = User::where('name', 'like', "%{$authorName}%")->first();
                    }

                    // Map publication type
                    $publicationType = $this->mapPublicationType($type);
                    
                    // Map journal category
                    $journalCategory = $this->mapJournalCategory($indexed);

                    // Validate and map quartile (only Q1-Q4 are valid)
                    $validQuartile = $this->mapQuartile($quartile);

                    $data = [
                        'title' => $title,
                        'slug' => \Illuminate\Support\Str::slug($title) . '-' . uniqid(),
                        'publication_type' => $publicationType,
                        'journal_category' => $journalCategory,
                        'quartile' => $validQuartile,
                        'year' => $year ? (int)$year : null,
                        'publication_year' => $year ? (int)$year : null,
                        'journal_name' => $journal,
                        'doi' => $doi,
                        'status' => 'approved',
                        'primary_author_id' => $author?->id,
                        'submitted_by' => $author?->id,
                    ];

                    // Create authors array
                    if ($author) {
                        $data['authors'] = json_encode([[
                            'name' => $author->name,
                            'email' => $author->email,
                            'is_primary' => true,
                        ]]);
                    }

                    Publication::create($data);
                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    if ($failed <= 5) {
                        $this->command->warn("    Row {$row}: " . $e->getMessage());
                    }
                }
            }

            DB::commit();
            $this->command->info("  Imported: {$imported}, Failed: {$failed}");
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import grants from sheet
     */
    protected function importGrants(string $sheetName, $sheet, array $headers, int $headerRow = 1): void
    {
        $imported = 0;
        $failed = 0;
        $highestRow = $sheet->getHighestRow();

        DB::beginTransaction();
        try {
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    $rowData = $this->getRowData($sheet, $headers, $row, $headerRow);
                    
                    if (empty(array_filter($rowData))) {
                        continue;
                    }

                    $title = $this->getValue($rowData, ['Title', 'title', 'project_title', 'Project Title', 'Grant Title', 'Application Type']);
                    $staffName = $this->getValue($rowData, ['Staff Name', 'staff_name', 'StaffName', 'Name', 'Principal Investigator']);
                    $role = $this->getValue($rowData, ['Role', 'role']);
                    $amountOmr = $this->getValue($rowData, ['Amount OMR', 'amount_omr_', 'Amount (OMR)', 'Amount', 'amount']);
                    $sponsor = $this->getValue($rowData, ['Sponsor', 'sponsor', 'Sponsor Name', 'Client / Sponsor']);
                    $startDate = $this->getValue($rowData, ['Start Date', 'start_date', 'Start', 'Date']);
                    $endDate = $this->getValue($rowData, ['End Date', 'end_date', 'End']);
                    $status = $this->getValue($rowData, ['Status', 'status', 'Status (Ongoing/Closed)', 'Status (Submitted/Accepted/Rejected)']);

                    // For Internal_Funding, use Application Type as title if title is empty
                    if (empty($title)) {
                        $title = $this->getValue($rowData, ['Application Type', 'Type (RG/GRG/URG)', 'Type']);
                    }

                    if (empty($title) && empty($staffName)) {
                        continue;
                    }

                    // If no title, create one from staff name and type
                    if (empty($title)) {
                        $title = "Grant Application - {$staffName}";
                    }

                    // Find staff user
                    $user = null;
                    if ($staffName) {
                        $user = User::where('name', 'like', "%{$staffName}%")->first();
                    }

                    // Determine grant type from sheet name
                    $grantType = $this->mapGrantType($sheetName, $rowData);

                    // Map status
                    $mappedStatus = 'approved';
                    if ($status) {
                        $statusLower = strtolower($status);
                        if (str_contains($statusLower, 'reject')) {
                            $mappedStatus = 'rejected';
                        } elseif (str_contains($statusLower, 'pending') || str_contains($statusLower, 'submit')) {
                            $mappedStatus = 'pending';
                        } elseif (str_contains($statusLower, 'accept') || str_contains($statusLower, 'approve')) {
                            $mappedStatus = 'approved';
                        } elseif (str_contains($statusLower, 'ongoing')) {
                            $mappedStatus = 'ongoing';
                        } elseif (str_contains($statusLower, 'closed')) {
                            $mappedStatus = 'closed';
                        }
                    }

                    $data = [
                        'title' => $title,
                        'slug' => \Illuminate\Support\Str::slug($title) . '-' . uniqid(),
                        'grant_type' => $grantType,
                        'role' => $this->mapGrantRole($role),
                        'amount_omr' => $amountOmr ? (float)$amountOmr : null,
                        'units' => $amountOmr ? (int)ceil((float)$amountOmr / 10000) : 1,
                        'sponsor' => $sponsor,
                        'sponsor_name' => $sponsor,
                        'start_date' => $this->parseDate($startDate),
                        'end_date' => $this->parseDate($endDate),
                        'status' => $mappedStatus,
                        'submitted_by' => $user?->id,
                    ];

                    Grant::create($data);
                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    if ($failed <= 5) {
                        $this->command->warn("    Row {$row}: " . $e->getMessage());
                    }
                }
            }

            DB::commit();
            $this->command->info("  Imported: {$imported}, Failed: {$failed}");
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import RTN submissions from sheet
     */
    protected function importRTNs(string $sheetName, $sheet, array $headers, int $headerRow = 1): void
    {
        $imported = 0;
        $failed = 0;
        $skipped = 0;
        $highestRow = $sheet->getHighestRow();

        DB::beginTransaction();
        try {
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    $rowData = $this->getRowData($sheet, $headers, $row, $headerRow);
                    
                    if (empty(array_filter($rowData))) {
                        continue;
                    }

                    $staffName = $this->getValue($rowData, ['Staff Name', 'staff_name', 'Staff Name', 'Name']);
                    $faculty = $this->getValue($rowData, ['Faculty', 'faculty']);
                    $units = $this->getValue($rowData, ['Units', 'units']);
                    $points = $this->getValue($rowData, ['Points', 'points']);
                    $amountOmr = $this->getValue($rowData, ['Amount OMR', 'amount_omr_', 'Amount (OMR)', 'Amount']);
                    $evidenceLink = $this->getValue($rowData, ['Evidence Link', 'evidence_link', 'Evidence']);
                    $totalRtn = $this->getValue($rowData, ['Total RTN', 'total_rtn', 'Total']);

                    if (empty($staffName)) {
                        continue;
                    }

                    // Find user
                    $user = null;
                    if ($staffName) {
                        $user = User::where('name', $staffName)->first();
                        if (!$user) {
                            $user = User::whereRaw('LOWER(name) = ?', [strtolower($staffName)])->first();
                        }
                        if (!$user) {
                            $user = User::where('name', 'like', "%{$staffName}%")->first();
                        }
                    }
                    
                    if (!$user) {
                        $skipped++;
                        continue;
                    }

                    // Calculate points from units if not provided
                    $calculatedPoints = $points ? (float)$points : ($units ? (float)$units * 10 : 0);

                    $data = [
                        'user_id' => $user->id,
                        'rtn_type' => 'other',
                        'title' => 'RTN Submission',
                        'description' => "Units: {$units}, Amount: {$amountOmr} OMR",
                        'points' => $calculatedPoints,
                        'year' => date('Y'),
                        'status' => 'approved',
                        'submitted_at' => now(),
                        'approved_at' => now(),
                    ];

                    RtnSubmission::create($data);
                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    if ($failed <= 5) {
                        $this->command->warn("    Row {$row}: " . $e->getMessage());
                    }
                }
            }

            DB::commit();
            $this->command->info("  Imported: {$imported}, Failed: {$failed}, Skipped (no user): {$skipped}");
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import bonus recognitions from sheet
     */
    protected function importBonusRecognitions(string $sheetName, $sheet, array $headers, int $headerRow = 1): void
    {
        $imported = 0;
        $failed = 0;
        $skipped = 0;
        $highestRow = $sheet->getHighestRow();

        DB::beginTransaction();
        try {
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    $rowData = $this->getRowData($sheet, $headers, $row, $headerRow);
                    
                    if (empty(array_filter($rowData))) {
                        continue;
                    }

                    $staffName = $this->getValue($rowData, ['Staff Name', 'staff_name', 'Staff Name', 'Name']);
                    $recognitionType = $this->getValue($rowData, ['Recognition Type', 'recognition_type', 'Type', 'Recognition Type']);
                    $points = $this->getValue($rowData, ['Points', 'points']);

                    if (empty($staffName)) {
                        continue;
                    }

                    // Find user
                    $user = null;
                    if ($staffName) {
                        $user = User::where('name', $staffName)->first();
                        if (!$user) {
                            $user = User::whereRaw('LOWER(name) = ?', [strtolower($staffName)])->first();
                        }
                        if (!$user) {
                            $user = User::where('name', 'like', "%{$staffName}%")->first();
                        }
                    }
                    
                    if (!$user) {
                        $skipped++;
                        continue;
                    }

                    $data = [
                        'user_id' => $user->id,
                        'recognition_type' => $this->mapRecognitionType($recognitionType),
                        'title' => $recognitionType ?: 'Recognition',
                        'points' => $points ? (float)$points : 0,
                        'year' => date('Y'),
                        'status' => 'approved',
                        'submitted_at' => now(),
                        'approved_at' => now(),
                    ];

                    BonusRecognition::create($data);
                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    if ($failed <= 5) {
                        $this->command->warn("    Row {$row}: " . $e->getMessage());
                    }
                }
            }

            DB::commit();
            $this->command->info("  Imported: {$imported}, Failed: {$failed}, Skipped (no user): {$skipped}");
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import editorial appointments from sheet
     */
    protected function importEditorialAppointments(string $sheetName, $sheet, array $headers, int $headerRow = 1): void
    {
        $imported = 0;
        $failed = 0;
        $skipped = 0;
        $highestRow = $sheet->getHighestRow();

        DB::beginTransaction();
        try {
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    $rowData = $this->getRowData($sheet, $headers, $row, $headerRow);
                    
                    if (empty(array_filter($rowData))) {
                        continue;
                    }

                    $staffName = $this->getValue($rowData, ['StaffName', 'Staff Name', 'staff_name', 'Name']);
                    $journalConference = $this->getValue($rowData, ['Journal/Conference', 'Journal', 'Journal', 'Conference']);
                    $role = $this->getValue($rowData, ['Role', 'role']);

                    if (empty($staffName)) {
                        continue;
                    }

                    $user = null;
                    if ($staffName) {
                        $user = User::where('name', $staffName)->first();
                        if (!$user) {
                            $user = User::whereRaw('LOWER(name) = ?', [strtolower($staffName)])->first();
                        }
                        if (!$user) {
                            $user = User::where('name', 'like', "%{$staffName}%")->first();
                        }
                    }
                    
                    if (!$user) {
                        $skipped++;
                        continue;
                    }

                    $data = [
                        'user_id' => $user->id,
                        'recognition_type' => 'editorial_board',
                        'title' => "Editorial Role: {$role}",
                        'organization' => $journalConference,
                        'role_description' => $role,
                        'journal_conference_name' => $journalConference,
                        'year' => date('Y'),
                        'status' => 'approved',
                        'submitted_at' => now(),
                        'approved_at' => now(),
                    ];

                    BonusRecognition::create($data);
                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    if ($failed <= 5) {
                        $this->command->warn("    Row {$row}: " . $e->getMessage());
                    }
                }
            }

            DB::commit();
            if ($imported == 0 && $highestRow > $headerRow) {
                // Show sample data for debugging
                $sample = $this->getRowData($sheet, $headers, $headerRow + 1, $headerRow);
                $this->command->warn("  No data imported. Sample row: " . json_encode(array_slice($sample, 0, 5)));
            }
            $this->command->info("  Imported: {$imported}, Failed: {$failed}");
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import supervision exams from sheet
     */
    protected function importSupervisionExams(string $sheetName, $sheet, array $headers, int $headerRow = 1): void
    {
        $imported = 0;
        $failed = 0;
        $skipped = 0;
        $highestRow = $sheet->getHighestRow();

        DB::beginTransaction();
        try {
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    $rowData = $this->getRowData($sheet, $headers, $row, $headerRow);
                    
                    if (empty(array_filter($rowData))) {
                        continue;
                    }

                    $staffName = $this->getValue($rowData, ['Staff Name', 'StaffName', 'staff_name', 'Name']);
                    $role = $this->getValue($rowData, ['Role (Main/Co/External Examiner)', 'Role', 'role']);
                    $degree = $this->getValue($rowData, ['Degree (MSc/PhD)', 'Degree', 'degree']);
                    $university = $this->getValue($rowData, ['University', 'university']);
                    $studentName = $this->getValue($rowData, ['Student Name', 'student_name', 'Student']);

                    if (empty($staffName)) {
                        continue;
                    }

                    $user = null;
                    if ($staffName) {
                        $user = User::where('name', $staffName)->first();
                        if (!$user) {
                            $user = User::whereRaw('LOWER(name) = ?', [strtolower($staffName)])->first();
                        }
                        if (!$user) {
                            $user = User::where('name', 'like', "%{$staffName}%")->first();
                        }
                    }
                    
                    if (!$user) {
                        $skipped++;
                        continue;
                    }

                    // Only import external examiner roles as bonus recognition
                    if (str_contains(strtolower($role), 'external')) {
                        $data = [
                            'user_id' => $user->id,
                            'recognition_type' => 'external_examiner',
                            'title' => "External Examiner: {$degree} - {$studentName}",
                            'organization' => $university,
                            'role_description' => "{$role} for {$degree} student: {$studentName}",
                            'year' => date('Y'),
                            'status' => 'approved',
                            'submitted_at' => now(),
                            'approved_at' => now(),
                        ];

                        BonusRecognition::create($data);
                        $imported++;
                    }
                } catch (\Exception $e) {
                    $failed++;
                    if ($failed <= 5) {
                        $this->command->warn("    Row {$row}: " . $e->getMessage());
                    }
                }
            }

            DB::commit();
            if ($imported == 0 && $highestRow > $headerRow) {
                $sample = $this->getRowData($sheet, $headers, $headerRow + 1, $headerRow);
                $this->command->warn("  No data imported. Sample row: " . json_encode(array_slice($sample, 0, 5)));
            }
            $this->command->info("  Imported: {$imported}, Failed: {$failed}, Skipped: {$skipped}");
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import research investments from sheet
     */
    protected function importResearchInvestments(string $sheetName, $sheet, array $headers, int $headerRow = 1): void
    {
        $imported = 0;
        $failed = 0;
        $highestRow = $sheet->getHighestRow();

        DB::beginTransaction();
        try {
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    $rowData = $this->getRowData($sheet, $headers, $row, $headerRow);
                    
                    if (empty(array_filter($rowData))) {
                        continue;
                    }

                    $staffName = $this->getValue($rowData, ['Staff name', 'Staff Name', 'staff_name', 'Name']);
                    $item = $this->getValue($rowData, ['Item', 'item']);
                    $category = $this->getValue($rowData, ['Category (Equipment/Software/APC/Travel/Training)', 'Category', 'category']);
                    $date = $this->getValue($rowData, ['Date', 'date']);
                    $amountOmr = $this->getValue($rowData, ['Amount (OMR)', 'Amount OMR', 'amount_omr_', 'Amount']);
                    $fundingSource = $this->getValue($rowData, ['Funding Source', 'funding_source', 'Funding']);

                    if (empty($item)) {
                        continue;
                    }

                    $user = null;
                    if ($staffName) {
                        $user = User::where('name', 'like', "%{$staffName}%")->first();
                    }

                    $categoryMap = [
                        'equipment' => 'equipment',
                        'software' => 'software',
                        'apc' => 'apc',
                        'travel' => 'travel',
                        'training' => 'training',
                    ];
                    $mappedCategory = null;
                    foreach ($categoryMap as $key => $value) {
                        if (str_contains(strtolower($category), $key)) {
                            $mappedCategory = $value;
                            break;
                        }
                    }

                    $data = [
                        'user_id' => $user?->id,
                        'staff_name' => $staffName,
                        'item' => $item,
                        'category' => $mappedCategory,
                        'date' => $this->parseDate($date),
                        'amount_omr' => $amountOmr ? (float)$amountOmr : null,
                        'funding_source' => $fundingSource,
                        'year' => $date ? (int)date('Y', strtotime($this->parseDate($date) ?: 'now')) : date('Y'),
                    ];

                    DB::table('research_investments')->insert($data);
                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    if ($failed <= 5) {
                        $this->command->warn("    Row {$row}: " . $e->getMessage());
                    }
                }
            }

            DB::commit();
            $this->command->info("  Imported: {$imported}, Failed: {$failed}");
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import SDG contributions from sheet
     */
    protected function importSdgContributions(string $sheetName, $sheet, array $headers, int $headerRow = 1): void
    {
        $imported = 0;
        $failed = 0;
        $highestRow = $sheet->getHighestRow();

        DB::beginTransaction();
        try {
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    $rowData = $this->getRowData($sheet, $headers, $row, $headerRow);
                    
                    if (empty(array_filter($rowData))) {
                        continue;
                    }

                    $staffName = $this->getValue($rowData, ['StaffName', 'Staff Name', 'staff_name', 'Name']);
                    $type = $this->getValue($rowData, ['Type (Paper/Project/Talk)', 'Type', 'type']);
                    $title = $this->getValue($rowData, ['Title', 'title']);
                    $sdg = $this->getValue($rowData, ['SDG', 'sdg']);
                    $date = $this->getValue($rowData, ['Date', 'date']);

                    if (empty($title) || empty($sdg)) {
                        continue;
                    }

                    $user = null;
                    if ($staffName) {
                        $user = User::where('name', 'like', "%{$staffName}%")->first();
                    }

                    $typeMap = [
                        'paper' => 'paper',
                        'project' => 'project',
                        'talk' => 'talk',
                    ];
                    $mappedType = 'other';
                    foreach ($typeMap as $key => $value) {
                        if (str_contains(strtolower($type), $key)) {
                            $mappedType = $value;
                            break;
                        }
                    }

                    $sdgNumber = (int)$sdg;
                    if ($sdgNumber < 1 || $sdgNumber > 17) {
                        continue; // Invalid SDG number
                    }

                    $data = [
                        'user_id' => $user?->id,
                        'staffname' => $staffName,
                        'type' => $mappedType,
                        'title' => $title,
                        'sdg' => $sdgNumber,
                        'date' => $this->parseDate($date),
                    ];

                    DB::table('sdg_contributions')->insert($data);
                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    if ($failed <= 5) {
                        $this->command->warn("    Row {$row}: " . $e->getMessage());
                    }
                }
            }

            DB::commit();
            $this->command->info("  Imported: {$imported}, Failed: {$failed}");
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import conference activities from sheet
     */
    protected function importConferenceActivities(string $sheetName, $sheet, array $headers, int $headerRow = 1): void
    {
        $imported = 0;
        $failed = 0;
        $skipped = 0;
        $highestRow = $sheet->getHighestRow();

        DB::beginTransaction();
        try {
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    $rowData = $this->getRowData($sheet, $headers, $row, $headerRow);
                    
                    if (empty(array_filter($rowData))) {
                        continue;
                    }

                    $staffName = $this->getValue($rowData, ['Staff Name', 'StaffName', 'staff_name', 'Name']);
                    $activityType = $this->getValue($rowData, ['Activity Type (Keynote/Invited/Regular)', 'Activity Type', 'Type', 'type']);
                    $conference = $this->getValue($rowData, ['Conference', 'conference']);
                    $country = $this->getValue($rowData, ['Country', 'country']);
                    $date = $this->getValue($rowData, ['Date', 'date']);

                    if (empty($staffName) || empty($conference)) {
                        continue;
                    }

                    $user = null;
                    if ($staffName) {
                        $user = User::where('name', $staffName)->first();
                        if (!$user) {
                            $user = User::whereRaw('LOWER(name) = ?', [strtolower($staffName)])->first();
                        }
                        if (!$user) {
                            $user = User::where('name', 'like', "%{$staffName}%")->first();
                        }
                    }
                    
                    if (!$user) {
                        $skipped++;
                        continue;
                    }

                    $activityTypeMap = [
                        'keynote' => 'keynote',
                        'invited' => 'invited',
                        'regular' => 'regular',
                    ];
                    $mappedActivityType = 'regular';
                    foreach ($activityTypeMap as $key => $value) {
                        if (str_contains(strtolower($activityType), $key)) {
                            $mappedActivityType = $value;
                            break;
                        }
                    }

                    $data = [
                        'user_id' => $user->id,
                        'activity_type' => $mappedActivityType,
                        'conference_name' => $conference,
                        'country' => $country,
                        'date' => $this->parseDate($date),
                        'year' => $date ? (int)date('Y', strtotime($this->parseDate($date) ?: 'now')) : date('Y'),
                    ];

                    DB::table('conference_activities')->insert($data);
                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    if ($failed <= 5) {
                        $this->command->warn("    Row {$row}: " . $e->getMessage());
                    }
                }
            }

            DB::commit();
            $this->command->info("  Imported: {$imported}, Failed: {$failed}");
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import partnerships/MoUs from sheet
     */
    protected function importPartnerships(string $sheetName, $sheet, array $headers, int $headerRow = 1): void
    {
        $imported = 0;
        $failed = 0;
        $highestRow = $sheet->getHighestRow();

        DB::beginTransaction();
        try {
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    $rowData = $this->getRowData($sheet, $headers, $row, $headerRow);
                    
                    if (empty(array_filter($rowData))) {
                        continue;
                    }

                    $partnerOrg = $this->getValue($rowData, ['Partner Organization', 'Partner', 'partner', 'Organization']);
                    $type = $this->getValue($rowData, ['Type (MoU/MoA/Project/Industry)', 'Type', 'type']);
                    $dateSigned = $this->getValue($rowData, ['Date Signed', 'date_signed', 'Date']);
                    $leadStaff = $this->getValue($rowData, ['Lead Staff', 'lead_staff', 'Staff']);

                    if (empty($partnerOrg)) {
                        continue;
                    }

                    $user = null;
                    if ($leadStaff) {
                        $user = User::where('name', 'like', "%{$leadStaff}%")->first();
                    }

                    $data = [
                        'partner_organization' => $partnerOrg,
                        'type' => $type ?: 'other',
                        'date_signed' => $this->parseDate($dateSigned),
                        'lead_staff_id' => $user?->id,
                        'lead_staff' => $leadStaff,
                        'status' => 'active',
                    ];

                    DB::table('partnerships_mous')->insert($data);
                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    if ($failed <= 5) {
                        $this->command->warn("    Row {$row}: " . $e->getMessage());
                    }
                }
            }

            DB::commit();
            $this->command->info("  Imported: {$imported}, Failed: {$failed}");
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import commercializations from sheet
     */
    protected function importCommercializations(string $sheetName, $sheet, array $headers, int $headerRow = 1): void
    {
        $imported = 0;
        $failed = 0;
        $highestRow = $sheet->getHighestRow();

        DB::beginTransaction();
        try {
            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                try {
                    $rowData = $this->getRowData($sheet, $headers, $row, $headerRow);
                    
                    if (empty(array_filter($rowData))) {
                        continue;
                    }

                    $productServiceName = $this->getValue($rowData, ['Product/Service Name', 'Product', 'Service', 'Name']);
                    $ownerTeam = $this->getValue($rowData, ['Owner / Team', 'Owner', 'Team', 'owner_team']);
                    $type = $this->getValue($rowData, ['Type (Product/Service)', 'Type', 'type']);
                    $stage = $this->getValue($rowData, ['Stage (Prototype/Pilot/Launched)', 'Stage', 'stage']);
                    $launchDate = $this->getValue($rowData, ['Launch Date', 'launch_date', 'Date']);
                    $revenueOmr = $this->getValue($rowData, ['Revenue (OMR)', 'Revenue', 'revenue']);

                    if (empty($productServiceName)) {
                        continue;
                    }

                    $user = null;
                    if ($ownerTeam) {
                        $user = User::where('name', 'like', "%{$ownerTeam}%")->first();
                    }

                    $data = [
                        'product_service_name' => $productServiceName,
                        'owner_team_id' => $user?->id,
                        'owner_team' => $ownerTeam,
                        'type' => strtolower($type) === 'service' ? 'service' : 'product',
                        'stage' => $this->mapStage($stage),
                        'launch_date' => $this->parseDate($launchDate),
                        'revenue_omr' => $revenueOmr ? (float)$revenueOmr : null,
                        'year' => $launchDate ? (int)date('Y', strtotime($this->parseDate($launchDate) ?: 'now')) : date('Y'),
                    ];

                    DB::table('commercializations')->insert($data);
                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    if ($failed <= 5) {
                        $this->command->warn("    Row {$row}: " . $e->getMessage());
                    }
                }
            }

            DB::commit();
            $this->command->info("  Imported: {$imported}, Failed: {$failed}");
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import consultancies/KT from sheet
     */
    protected function importConsultanciesKt(string $sheetName, $sheet, array $headers, int $headerRow = 1): void
    {
        // Map to grants table with grant_type = 'external_consultancy'
        $this->importGrants($sheetName, $sheet, $headers, $headerRow);
    }

    /**
     * Import research fellows publications from sheet
     */
    protected function importResearchFellows(string $sheetName, $sheet, array $headers, int $headerRow = 1): void
    {
        // Map to publications table
        $this->importPublications($sheetName, $sheet, $headers, $headerRow);
    }

    /**
     * Map stage for commercializations
     */
    protected function mapStage(?string $stage): ?string
    {
        if (empty($stage)) {
            return null;
        }

        $stageLower = strtolower($stage);
        
        if (str_contains($stageLower, 'prototype')) {
            return 'prototype';
        } elseif (str_contains($stageLower, 'pilot')) {
            return 'pilot';
        } elseif (str_contains($stageLower, 'launch')) {
            return 'launched';
        }
        
        return null;
    }

    /**
     * Generic import for unknown sheet types
     */
    protected function importGeneric(string $sheetName, $sheet, array $headers): void
    {
        $this->command->warn("  Generic import not implemented for sheet '{$sheetName}'");
    }

    /**
     * Find headers in sheet (try rows 1, 2, 3)
     */
    protected function findHeaders($sheet): array
    {
        for ($row = 1; $row <= 3; $row++) {
            $headers = $this->excelReader->getHeaders($sheet, $row);
            if (!empty($headers) && count($headers) >= 2) {
                // Check if this looks like a header row (has text, not just numbers)
                $textCount = 0;
                foreach ($headers as $header) {
                    if (!is_numeric($header) && strlen(trim($header)) > 0) {
                        $textCount++;
                    }
                }
                if ($textCount >= 2) {
                    return ['row' => $row, 'headers' => $headers];
                }
            }
        }
        return ['row' => 1, 'headers' => []];
    }

    /**
     * Get row data from sheet
     */
    protected function getRowData($sheet, array $headers, int $row, int $headerRow = 1): array
    {
        $rowData = [];
        foreach ($headers as $index => $header) {
            $col = $index + 1;
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $cellCoordinate = $columnLetter . $row;
            $cellValue = $this->excelReader->getCellValue($sheet, $cellCoordinate);
            $rowData[$header] = $cellValue;
        }
        return $rowData;
    }

    /**
     * Get value from row data using multiple possible keys (case-insensitive and partial matching)
     */
    protected function getValue(array $rowData, array $keys): ?string
    {
        // First try exact match
        foreach ($keys as $key) {
            if (isset($rowData[$key]) && !empty(trim((string)$rowData[$key]))) {
                return trim((string)$rowData[$key]);
            }
        }
        
        // Then try case-insensitive match
        $rowDataLower = array_change_key_case($rowData, CASE_LOWER);
        foreach ($keys as $key) {
            $keyLower = strtolower($key);
            foreach ($rowDataLower as $header => $value) {
                if ($header === $keyLower && !empty(trim((string)$value))) {
                    return trim((string)$value);
                }
            }
        }
        
        // Then try partial match
        foreach ($keys as $key) {
            $keyLower = strtolower($key);
            foreach ($rowData as $header => $value) {
                if (stripos($header, $keyLower) !== false && !empty(trim((string)$value))) {
                    return trim((string)$value);
                }
            }
        }
        
        return null;
    }

    /**
     * Map publication type
     */
    protected function mapPublicationType(?string $type): ?string
    {
        if (empty($type)) {
            return null;
        }

        $typeLower = strtolower($type);
        
        if (str_contains($typeLower, 'journal')) {
            return 'journal_paper';
        } elseif (str_contains($typeLower, 'conference')) {
            return 'conference_paper';
        } elseif (str_contains($typeLower, 'book')) {
            return str_contains($typeLower, 'chapter') ? 'book_chapter' : 'book';
        }
        
        return 'journal_paper'; // Default
    }

    /**
     * Map journal category
     */
    protected function mapJournalCategory(?string $indexed): ?string
    {
        if (empty($indexed)) {
            return null;
        }

        $indexedLower = strtolower($indexed);
        
        if (str_contains($indexedLower, 'scopus')) {
            return 'scopus';
        } elseif (str_contains($indexedLower, 'international') || str_contains($indexedLower, 'refereed')) {
            return 'international_refereed';
        } elseif (str_contains($indexedLower, 'arabic')) {
            return 'su_approved_arabic';
        }
        
        return 'non_indexed';
    }

    /**
     * Map and validate quartile (only Q1-Q4 are valid)
     */
    protected function mapQuartile(?string $quartile): ?string
    {
        if (empty($quartile)) {
            return null;
        }

        $quartileUpper = strtoupper(trim($quartile));
        
        // Handle invalid values like "NOT YET ASSIGNED", "N/A", etc.
        if (in_array($quartileUpper, ['NOT YET ASSIGNED', 'N/A', 'NA', 'NULL', 'NONE', 'TBD', 'PENDING'])) {
            return null;
        }

        // Extract Q1, Q2, Q3, or Q4 from the string
        if (preg_match('/Q([1-4])/', $quartileUpper, $matches)) {
            return 'Q' . $matches[1];
        }
        
        // If it's just a number 1-4
        if (in_array($quartileUpper, ['1', '2', '3', '4'])) {
            return 'Q' . $quartileUpper;
        }
        
        // Invalid quartile value
        return null;
    }

    /**
     * Map recognition type
     */
    protected function mapRecognitionType(?string $type): string
    {
        if (empty($type)) {
            return 'other';
        }

        $typeLower = strtolower($type);
        
        if (str_contains($typeLower, 'editorial') || str_contains($typeLower, 'editor')) {
            return 'editorial_board';
        } elseif (str_contains($typeLower, 'examiner') || str_contains($typeLower, 'exam')) {
            return 'external_examiner';
        } elseif (str_contains($typeLower, 'award')) {
            return 'award';
        } elseif (str_contains($typeLower, 'recognition')) {
            return 'recognition';
        }
        
        return 'other';
    }

    /**
     * Map grant type
     */
    protected function mapGrantType(string $sheetName, array $rowData): ?string
    {
        $sheetNameLower = strtolower($sheetName);
        
        if (str_contains($sheetNameLower, 'consultancy') || str_contains($sheetNameLower, 'kt')) {
            return 'external_consultancy';
        } elseif (str_contains($sheetNameLower, 'grg') || str_contains($sheetNameLower, 'urg')) {
            return 'grg_urg';
        } elseif (str_contains($sheetNameLower, 'matching')) {
            return 'matching_grant';
        } elseif (str_contains($sheetNameLower, 'patent')) {
            return 'patent_copyright';
        }
        
        return 'external_grant'; // Default
    }

    /**
     * Map grant role
     */
    protected function mapGrantRole(?string $role): ?string
    {
        if (empty($role)) {
            return null;
        }

        $roleUpper = strtoupper($role);
        
        if (str_contains($roleUpper, 'PI') || str_contains($roleUpper, 'PRINCIPAL')) {
            return 'PI';
        } elseif (str_contains($roleUpper, 'CO-PI') || str_contains($roleUpper, 'COPI')) {
            return 'Co_PI';
        } elseif (str_contains($roleUpper, 'CO-I') || str_contains($roleUpper, 'COI')) {
            return 'Co_I';
        } elseif (str_contains($roleUpper, 'ADVISOR') || str_contains($roleUpper, 'MENTOR')) {
            return 'Advisor_Mentor';
        }
        
        return 'PI'; // Default
    }

    /**
     * Parse date from various formats
     */
    protected function parseDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        // Try to parse as Excel date (numeric)
        if (is_numeric($date)) {
            try {
                $dateTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$date);
                return $dateTime->format('Y-m-d');
            } catch (\Exception $e) {
                // Not an Excel date
            }
        }

        // Try standard date formats
        try {
            $parsed = \Carbon\Carbon::parse($date);
            return $parsed->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
