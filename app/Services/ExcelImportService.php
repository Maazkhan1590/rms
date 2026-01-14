<?php

namespace App\Services;

use App\Services\ExcelReaderService;
use App\Models\User;
use App\Models\Publication;
use App\Models\Grant;
use App\Models\College;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelImportService
{
    protected ExcelReaderService $excelReader;

    public function __construct(ExcelReaderService $excelReader)
    {
        $this->excelReader = $excelReader;
    }

    /**
     * Import data from Excel file
     *
     * @param string $filePath
     * @param string $sheetName
     * @param array $mapping Column mapping: ['excel_column' => 'database_field']
     * @param string $modelClass Model class to import into
     * @param callable|null $transformer Function to transform row data
     * @return array ['imported' => int, 'failed' => int, 'errors' => array]
     */
    public function importSheet(
        string $filePath,
        string $sheetName,
        array $mapping,
        string $modelClass,
        ?callable $transformer = null
    ): array {
        $imported = 0;
        $failed = 0;
        $errors = [];

        try {
            $sheet = $this->excelReader->getSheet($filePath, $sheetName);
            $headers = $this->excelReader->getHeaders($sheet);
            $highestRow = $sheet->getHighestRow();

            DB::beginTransaction();

            for ($row = 2; $row <= $highestRow; $row++) {
                try {
                    $rowData = $this->getRowData($sheet, $headers, $row);
                    
                    if (empty(array_filter($rowData))) {
                        continue; // Skip empty rows
                    }

                    $data = $this->mapRowData($rowData, $mapping);

                    if ($transformer) {
                        $data = $transformer($data, $rowData);
                    }

                    $modelClass::create($data);
                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Row {$row}: " . $e->getMessage();
                    Log::error("Import error on row {$row}: " . $e->getMessage());
                }
            }

            DB::commit();

            return [
                'imported' => $imported,
                'failed' => $failed,
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Import users from Excel
     *
     * @param string $filePath
     * @param string $sheetName
     * @return array
     */
    public function importUsers(string $filePath, string $sheetName = 'Users'): array
    {
        $mapping = [
            'Name' => 'name',
            'Email' => 'email',
            'Employee ID' => 'employee_id',
            'Designation' => 'designation',
            'Phone' => 'phone',
            'College' => 'college_name', // Will be resolved
            'Department' => 'department_name', // Will be resolved
        ];

        return $this->importSheet($filePath, $sheetName, $mapping, User::class, function ($data, $rowData) {
            // Resolve college
            if (isset($data['college_name'])) {
                $college = College::where('name', $data['college_name'])->first();
                if ($college) {
                    $data['college_id'] = $college->id;
                }
                unset($data['college_name']);
            }

            // Resolve department
            if (isset($data['department_name']) && isset($data['college_id'])) {
                $department = Department::where('name', $data['department_name'])
                    ->where('college_id', $data['college_id'])
                    ->first();
                if ($department) {
                    $data['department_id'] = $department->id;
                }
                unset($data['department_name']);
            }

            // Set default password if not provided
            if (!isset($data['password'])) {
                $data['password'] = bcrypt('password'); // Should be changed on first login
            }

            // Set default status
            if (!isset($data['status'])) {
                $data['status'] = 'pending';
            }

            return $data;
        });
    }

    /**
     * Import publications from Excel
     *
     * @param string $filePath
     * @param string $sheetName
     * @return array
     */
    public function importPublications(string $filePath, string $sheetName = 'Publications'): array
    {
        $mapping = [
            'Title' => 'title',
            'Authors' => 'authors',
            'Journal' => 'journal_name',
            'Year' => 'year',
            'DOI' => 'doi',
            'Type' => 'publication_type',
            'Category' => 'journal_category',
            'Quartile' => 'quartile',
        ];

        return $this->importSheet($filePath, $sheetName, $mapping, Publication::class, function ($data, $rowData) {
            // Generate slug
            if (isset($data['title'])) {
                $data['slug'] = \Illuminate\Support\Str::slug($data['title']);
            }

            // Set default status
            if (!isset($data['status'])) {
                $data['status'] = 'draft';
            }

            // Parse authors if string
            if (isset($data['authors']) && is_string($data['authors'])) {
                $data['authors'] = json_encode(explode(',', $data['authors']));
            }

            return $data;
        });
    }

    /**
     * Get row data from sheet
     *
     * @param Worksheet $sheet
     * @param array $headers
     * @param int $row
     * @return array
     */
    private function getRowData(Worksheet $sheet, array $headers, int $row): array
    {
        $rowData = [];
        foreach ($headers as $index => $header) {
            $col = $index + 1;
            $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $cellCoordinate = $columnLetter . $row;
            $cellValue = $this->excelReader->getCellValue($sheet, $cellCoordinate);
            $rowData[$header] = $cellValue;
        }
        return $rowData;
    }

    /**
     * Map row data using mapping array
     *
     * @param array $rowData
     * @param array $mapping
     * @return array
     */
    private function mapRowData(array $rowData, array $mapping): array
    {
        $mapped = [];
        foreach ($mapping as $excelColumn => $dbField) {
            if (isset($rowData[$excelColumn])) {
                $mapped[$dbField] = $rowData[$excelColumn];
            }
        }
        return $mapped;
    }
}

