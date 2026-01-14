<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExcelReaderService;
use Illuminate\Support\Facades\File;

class AnalyzeExcelStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'excel:analyze 
                            {file : Path to the Excel file}
                            {--sheet= : Analyze specific sheet only}
                            {--output= : Output file path for JSON report}
                            {--migrations : Generate migration suggestions}
                            {--debug : Show debug information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze Excel file structure and generate database migration suggestions';

    /**
     * Excel Reader Service
     *
     * @var ExcelReaderService
     */
    protected $excelReader;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ExcelReaderService $excelReader)
    {
        parent::__construct();
        $this->excelReader = $excelReader;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Increase memory limit for large Excel files
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', '300');
        
        $filePath = $this->argument('file');
        
        // Check if file exists
        if (!File::exists($filePath)) {
            // Try relative to base path
            $filePath = base_path($filePath);
            if (!File::exists($filePath)) {
                $this->error("Excel file not found: {$filePath}");
                return Command::FAILURE;
            }
        }

        $this->info("Analyzing Excel file: {$filePath}");
        
        if ($this->option('debug')) {
            $this->line("File exists: " . (file_exists($filePath) ? 'Yes' : 'No'));
            $this->line("File size: " . (file_exists($filePath) ? filesize($filePath) . ' bytes' : 'N/A'));
            $this->line("Base path: " . base_path());
            $this->line("Current working directory: " . getcwd());
        }
        
        $this->newLine();

        try {
            // Show progress
            $this->info("Reading Excel file...");
            
            $analysis = null;
            
            if ($this->option('sheet')) {
                // Analyze specific sheet
                $sheetName = $this->option('sheet');
                $this->info("Analyzing sheet: {$sheetName}");
                $sheetAnalysis = $this->excelReader->analyzeSheet($filePath, $sheetName);
                $this->displaySheetAnalysis($sheetAnalysis);
                
                // Create minimal analysis structure for other operations
                $analysis = [
                    'file_path' => $filePath,
                    'file_name' => basename($filePath),
                    'sheet_count' => 1,
                    'sheets' => [$sheetName => $sheetAnalysis],
                ];
            } else {
                // Analyze all sheets
                $this->info("Analyzing all sheets...");
                
                // Get sheet names first to show progress
                $sheetNames = $this->excelReader->getSheetNames($filePath);
                $this->info("Found " . count($sheetNames) . " sheet(s): " . implode(', ', $sheetNames));
                $this->newLine();
                
                $analysis = $this->excelReader->analyzeFile($filePath);
                $this->displayAnalysis($analysis);
            }

            // Generate output file if requested
            if ($this->option('output') && $analysis) {
                $this->info("Saving output...");
                $this->saveOutput($analysis);
            }

            // Generate migration suggestions if requested
            if ($this->option('migrations') && $analysis) {
                $this->generateMigrationSuggestions($analysis);
            }

            $this->newLine();
            $this->info("Analysis complete!");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error analyzing Excel file: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }

    /**
     * Analyze a specific sheet
     *
     * @param string $filePath
     * @param string $sheetName
     * @return void
     */
    protected function analyzeSheet(string $filePath, string $sheetName)
    {
        try {
            $sheetAnalysis = $this->excelReader->analyzeSheet($filePath, $sheetName);
            $this->displaySheetAnalysis($sheetAnalysis);
        } catch (\Exception $e) {
            $this->error("Error analyzing sheet '{$sheetName}': " . $e->getMessage());
        }
    }

    /**
     * Display complete analysis
     *
     * @param array $analysis
     * @return void
     */
    protected function displayAnalysis(array $analysis)
    {
        $this->info("File: {$analysis['file_name']}");
        $this->info("Total Sheets: {$analysis['sheet_count']}");
        $this->newLine();
        
        // Show sheet summary first
        $this->info("Sheet Summary:");
        foreach ($analysis['sheets'] as $sheetName => $sheetData) {
            $colCount = $sheetData['column_count'] ?? 0;
            $rowCount = $sheetData['row_count'] ?? 0;
            $status = isset($sheetData['error']) ? 'ERROR' : (isset($sheetData['warning']) ? 'WARNING' : 'OK');
            $this->line("  - {$sheetName}: {$colCount} columns, {$rowCount} rows [{$status}]");
        }
        $this->newLine();

        foreach ($analysis['sheets'] as $sheetName => $sheetData) {
            $this->displaySheetAnalysis($sheetData);
            $this->newLine();
        }
    }

    /**
     * Display sheet analysis
     *
     * @param array $sheetData
     * @return void
     */
    protected function displaySheetAnalysis(array $sheetData)
    {
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("Sheet: {$sheetData['sheet_name']}");
        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        
        // Check for errors
        if (isset($sheetData['error'])) {
            $this->error("Error: {$sheetData['error']}");
            $this->newLine();
            return;
        }
        
        // Check for warnings
        if (isset($sheetData['warning'])) {
            $this->warn("Warning: {$sheetData['warning']}");
            $this->newLine();
        }
        
        $this->line("Columns: {$sheetData['column_count']}");
        $this->line("Rows: {$sheetData['row_count']}");
        $this->newLine();

        // Display headers
        if (!empty($sheetData['headers'])) {
            $this->info("Column Headers:");
            $headers = $sheetData['headers'];
            foreach ($headers as $index => $header) {
                $type = $sheetData['column_types'][$header] ?? 'unknown';
                $this->line("  " . ($index + 1) . ". {$header} ({$type})");
            }
            $this->newLine();
        } else {
            $this->warn("No headers found in this sheet.");
            $this->newLine();
        }

        // Display sample data
        if (!empty($sheetData['sample_data'])) {
            $this->info("Sample Data (first " . count($sheetData['sample_data']) . " rows):");
            foreach ($sheetData['sample_data'] as $rowIndex => $row) {
                $this->line("  Row " . ($rowIndex + 1) . ":");
                foreach ($row as $header => $value) {
                    $displayValue = is_string($value) && strlen($value) > 50 
                        ? substr($value, 0, 50) . '...' 
                        : $value;
                    $this->line("    {$header}: {$displayValue}");
                }
                if ($rowIndex < count($sheetData['sample_data']) - 1) {
                    $this->newLine();
                }
            }
        }
    }

    /**
     * Save analysis to JSON file
     *
     * @param array $analysis
     * @return void
     */
    protected function saveOutput(array $analysis)
    {
        $outputPath = $this->option('output');
        $json = json_encode($analysis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        File::put($outputPath, $json);
        $this->info("Analysis saved to: {$outputPath}");
    }

    /**
     * Generate migration suggestions
     *
     * @param array $analysis
     * @return void
     */
    protected function generateMigrationSuggestions(array $analysis)
    {
        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("Migration Suggestions");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->newLine();

        foreach ($analysis['sheets'] as $sheetName => $sheetData) {
            $tableName = $this->sheetNameToTableName($sheetName);
            $this->info("Table: {$tableName}");
            $this->line("Migration: create_{$tableName}_table");
            $this->newLine();
            
            $this->line("Schema::create('{$tableName}', function (Blueprint \$table) {");
            $this->line("    \$table->id();");
            
            foreach ($sheetData['headers'] as $header) {
                $columnName = $this->headerToColumnName($header);
                $type = $this->mapTypeToMigration($sheetData['column_types'][$header] ?? 'string');
                $this->line("    \$table->{$type}('{$columnName}')->nullable();");
            }
            
            $this->line("    \$table->timestamps();");
            $this->line("    \$table->softDeletes();");
            $this->line("});");
            $this->newLine();
        }
    }

    /**
     * Convert sheet name to table name
     *
     * @param string $sheetName
     * @return string
     */
    protected function sheetNameToTableName(string $sheetName): string
    {
        // Convert to snake_case and pluralize
        $name = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $sheetName));
        $name = trim($name, '_');
        
        // Simple pluralization (can be enhanced)
        if (!str_ends_with($name, 's')) {
            $name .= 's';
        }
        
        return $name;
    }

    /**
     * Convert header to column name
     *
     * @param string $header
     * @return string
     */
    protected function headerToColumnName(string $header): string
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $header));
    }

    /**
     * Map inferred type to Laravel migration type
     *
     * @param string $type
     * @return string
     */
    protected function mapTypeToMigration(string $type): string
    {
        return match($type) {
            'integer' => 'integer',
            'decimal' => 'decimal(15,2)',
            'boolean' => 'boolean',
            'date' => 'date',
            'email' => 'string',
            'text' => 'text',
            'nullable' => 'string',
            default => 'string',
        };
    }
}

