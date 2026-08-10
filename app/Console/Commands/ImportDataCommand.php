<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use App\Services\DataImportService;
use Illuminate\Console\Command;

class ImportDataCommand extends Command
{
    protected $signature = 'paperwork:import {file : Path to the JSON backup file} {--user-id= : Target User ID} {--company-id= : Target Company ID}';

    protected $description = 'Import JSON data backup into a user/company database account';

    public function handle(DataImportService $importService): int
    {
        $filePath = $this->argument('file');

        if (! file_exists($filePath)) {
            $this->error("File not found: {$filePath}");

            return self::FAILURE;
        }

        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);

        if (! is_array($data)) {
            $this->error('Invalid JSON file format.');

            return self::FAILURE;
        }

        $companyId = $this->option('company-id');
        $userId = $this->option('user-id');

        $company = null;

        if ($companyId) {
            $company = Company::find($companyId);
        } elseif ($userId) {
            $user = User::find($userId);
            $company = $user?->company;
        }

        if (! $company) {
            // Default to first company
            $company = Company::first();
        }

        if (! $company) {
            $this->error('No company found in database.');

            return self::FAILURE;
        }

        $this->info("Importing data into Company: {$company->name} (ID: {$company->id})...");

        $result = $importService->import($data, $company);

        $this->info('Import completed successfully!');
        $this->table(['Entity', 'Imported Count'], [
            ['Products', $result['products']],
            ['Clients', $result['clients']],
            ['Quotations', $result['quotations']],
            ['Invoices', $result['invoices']],
        ]);

        return self::SUCCESS;
    }
}
