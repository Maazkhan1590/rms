<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\Log;

class ExcelReaderService
{
    /**
     * Read Excel file and return spreadsheet object
     *
     * @param string $filePath
     * @return Spreadsheet
     * @throws \Exception
     */
    public function readFile(string $filePath, bool $readOnly = true): Spreadsheet
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Excel file not found: {$filePath}");
        }

        try {
            // Use read-only mode to reduce memory usage for large files
            $reader = IOFactory::createReader(IOFactory::identify($filePath));
            $reader->setReadDataOnly($readOnly);
            $reader->setReadEmptyCells(false);
            
            $spreadsheet = $reader->load($filePath);
            return $spreadsheet;
        } catch (\Exception $e) {
            Log::error("Error reading Excel file: " . $e->getMessage());
            throw new \Exception("Failed to read Excel file: " . $e->getMessage());
        }
    }

    /**
     * Get all sheet names from Excel file
     *
     * @param string $filePath
     * @return array
     */
    public function getSheetNames(string $filePath): array
    {
        try {
            // Use read-only mode to reduce memory usage
            $spreadsheet = $this->readFile($filePath, true);
            $sheetNames = $spreadsheet->getSheetNames();
            
            if (empty($sheetNames)) {
                throw new \Exception("No sheets found in Excel file");
            }
            
            return $sheetNames;
        } catch (\Exception $e) {
            Log::error("Error getting sheet names: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get sheet by name
     *
     * @param string $filePath
     * @param string $sheetName
     * @return Worksheet
     * @throws \Exception
     */
    public function getSheet(string $filePath, string $sheetName): Worksheet
    {
        // Use read-only mode to reduce memory usage
        $spreadsheet = $this->readFile($filePath, true);
        $sheet = $spreadsheet->getSheetByName($sheetName);
        
        if (!$sheet) {
            throw new \Exception("Sheet '{$sheetName}' not found in Excel file");
        }
        
        return $sheet;
    }

    /**
     * Get cell value safely (handles RichText objects)
     *
     * @param Worksheet $sheet
     * @param string $cellCoordinate
     * @return mixed
     */
    public function getCellValue(Worksheet $sheet, string $cellCoordinate)
    {
        $cell = $sheet->getCell($cellCoordinate);
        $value = $cell->getValue();
        
        // Handle RichText objects
        if ($value instanceof \PhpOffice\PhpSpreadsheet\RichText\RichText) {
            return $value->getPlainText();
        }
        
        return $value;
    }

    /**
     * Get headers from a sheet (first row)
     *
     * @param Worksheet $sheet
     * @param int $headerRow
     * @return array
     */
    public function getHeaders(Worksheet $sheet, int $headerRow = 1): array
    {
        $headers = [];
        
        // Limit to reasonable number of columns to avoid memory issues
        $maxColumns = 200;
        
        // Try to get highest column, but limit it
        try {
            $highestColumn = $sheet->getHighestColumn();
            $highestColumnIndex = min(
                Coordinate::columnIndexFromString($highestColumn),
                $maxColumns
            );
        } catch (\Exception $e) {
            // If we can't determine highest column, use a safe limit
            $highestColumnIndex = $maxColumns;
        }

        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $cellCoordinate = $columnLetter . $headerRow;
            
            try {
                $cellValue = $this->getCellValue($sheet, $cellCoordinate);
                if ($cellValue !== null && trim((string)$cellValue) !== '') {
                    $headers[] = trim((string)$cellValue);
                } else {
                    // Stop at first empty column if we've found at least one header
                    if (!empty($headers)) {
                        break;
                    }
                }
            } catch (\Exception $e) {
                // Skip problematic cells
                if (!empty($headers)) {
                    break;
                }
            }
        }

        return $headers;
    }

    /**
     * Get sample data rows from a sheet
     *
     * @param Worksheet $sheet
     * @param int $headerRow
     * @param int $sampleSize
     * @return array
     */
    public function getSampleData(Worksheet $sheet, int $headerRow = 1, int $sampleSize = 5): array
    {
        $headers = $this->getHeaders($sheet, $headerRow);
        $highestRow = $sheet->getHighestRow();
        $dataRows = [];

        $startRow = $headerRow + 1;
        $endRow = min($startRow + $sampleSize - 1, $highestRow);

        for ($row = $startRow; $row <= $endRow; $row++) {
            $rowData = [];
            foreach ($headers as $index => $header) {
                $col = $index + 1;
                $columnLetter = Coordinate::stringFromColumnIndex($col);
                $cellCoordinate = $columnLetter . $row;
                $cellValue = $this->getCellValue($sheet, $cellCoordinate);
                $rowData[$header] = $cellValue;
            }
            // Only add non-empty rows
            if (!empty(array_filter($rowData, fn($val) => $val !== null && trim($val) !== ''))) {
                $dataRows[] = $rowData;
            }
        }

        return $dataRows;
    }

    /**
     * Infer data type from cell value
     *
     * @param mixed $value
     * @return string
     */
    public function inferDataType($value): string
    {
        if ($value === null || $value === '') {
            return 'nullable';
        }

        if (is_numeric($value)) {
            if (is_int($value) || (is_string($value) && ctype_digit($value))) {
                return 'integer';
            }
            return 'decimal';
        }

        if (is_bool($value) || in_array(strtolower($value), ['true', 'false', 'yes', 'no', '1', '0'])) {
            return 'boolean';
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value) || preg_match('/^\d{2}\/\d{2}\/\d{4}/', $value)) {
            return 'date';
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        if (strlen($value) > 255) {
            return 'text';
        }

        return 'string';
    }

    /**
     * Analyze column data types
     *
     * @param Worksheet $sheet
     * @param int $headerRow
     * @param int $sampleSize
     * @return array
     */
    public function analyzeColumnTypes(Worksheet $sheet, int $headerRow = 1, int $sampleSize = 10): array
    {
        $headers = $this->getHeaders($sheet, $headerRow);
        $highestRow = $sheet->getHighestRow();
        $columnTypes = [];

        $startRow = $headerRow + 1;
        $endRow = min($startRow + $sampleSize - 1, $highestRow);

        foreach ($headers as $index => $header) {
            $col = $index + 1;
            $columnLetter = Coordinate::stringFromColumnIndex($col);
            $types = [];
            
            for ($row = $startRow; $row <= $endRow; $row++) {
                $cellCoordinate = $columnLetter . $row;
                $cellValue = $this->getCellValue($sheet, $cellCoordinate);
                if ($cellValue !== null && trim((string)$cellValue) !== '') {
                    $types[] = $this->inferDataType($cellValue);
                }
            }

            // Determine most common type
            if (empty($types)) {
                $columnTypes[$header] = 'nullable';
            } else {
                $typeCounts = array_count_values($types);
                arsort($typeCounts);
                $columnTypes[$header] = array_key_first($typeCounts);
            }
        }

        return $columnTypes;
    }

    /**
     * Get total row count (excluding header)
     *
     * @param Worksheet $sheet
     * @param int $headerRow
     * @return int
     */
    public function getRowCount(Worksheet $sheet, int $headerRow = 1): int
    {
        try {
            $highestRow = $sheet->getHighestRow();
            // Limit to reasonable number to avoid memory issues
            $maxRows = 100000;
            $actualRowCount = min($highestRow, $maxRows);
            return max(0, $actualRowCount - $headerRow);
        } catch (\Exception $e) {
            // If we can't determine row count, return 0
            return 0;
        }
    }

    /**
     * Analyze complete sheet structure
     *
     * @param string $filePath
     * @param string $sheetName
     * @return array
     */
    public function analyzeSheet(string $filePath, string $sheetName): array
    {
        try {
            // Load sheet with read-only mode
            $sheet = $this->getSheet($filePath, $sheetName);
            $headers = $this->getHeaders($sheet);
            
            if (empty($headers)) {
                return [
                    'sheet_name' => $sheetName,
                    'headers' => [],
                    'column_count' => 0,
                    'row_count' => 0,
                    'column_types' => [],
                    'sample_data' => [],
                    'warning' => 'No headers found in sheet',
                ];
            }
            
            // Limit sample data to avoid memory issues
            $sampleData = $this->getSampleData($sheet, 1, 3); // Only 3 sample rows
            $columnTypes = $this->analyzeColumnTypes($sheet, 1, 10); // Only analyze 10 rows
            $rowCount = $this->getRowCount($sheet);

            return [
                'sheet_name' => $sheetName,
                'headers' => $headers,
                'column_count' => count($headers),
                'row_count' => $rowCount,
                'column_types' => $columnTypes,
                'sample_data' => $sampleData,
            ];
        } catch (\Exception $e) {
            Log::error("Error analyzing sheet '{$sheetName}': " . $e->getMessage());
            throw new \Exception("Failed to analyze sheet '{$sheetName}': " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Analyze entire Excel file
     *
     * @param string $filePath
     * @return array
     */
    public function analyzeFile(string $filePath): array
    {
        try {
            // Get sheet names first (this loads the file but doesn't process all cells)
            $sheetNames = $this->getSheetNames($filePath);
            $analysis = [
                'file_path' => $filePath,
                'file_name' => basename($filePath),
                'sheet_count' => count($sheetNames),
                'sheets' => [],
            ];

            // Process each sheet individually to avoid memory issues
            foreach ($sheetNames as $index => $sheetName) {
                try {
                    // Clear memory between sheets
                    if ($index > 0 && $index % 5 == 0) {
                        gc_collect_cycles();
                    }
                    
                    $analysis['sheets'][$sheetName] = $this->analyzeSheet($filePath, $sheetName);
                } catch (\Exception $e) {
                    Log::error("Error analyzing sheet '{$sheetName}': " . $e->getMessage());
                    // Continue with other sheets even if one fails
                    $analysis['sheets'][$sheetName] = [
                        'sheet_name' => $sheetName,
                        'error' => $e->getMessage(),
                        'headers' => [],
                        'column_count' => 0,
                        'row_count' => 0,
                        'column_types' => [],
                        'sample_data' => [],
                    ];
                }
            }

            return $analysis;
        } catch (\Exception $e) {
            Log::error("Error analyzing Excel file: " . $e->getMessage());
            throw $e;
        }
    }
}

