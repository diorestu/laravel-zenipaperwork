<?php

namespace App\Http\Controllers;

use App\Services\DataImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DataManagementController extends Controller
{
    public function index()
    {
        return view('settings.data');
    }

    public function import(Request $request, DataImportService $importService): RedirectResponse
    {
        $request->validate([
            'json_file' => ['required', 'file', 'mimes:json,txt', 'max:10240'],
        ]);

        $file = $request->file('json_file');
        $jsonContent = file_get_contents($file->getRealPath());
        $data = json_decode($jsonContent, true);

        if (! is_array($data)) {
            return back()->with('error', 'Format file JSON tidak valid.');
        }

        $company = $request->user()->company;

        if (! $company) {
            return back()->with('error', 'Perusahaan tidak ditemukan.');
        }

        try {
            $stats = $importService->import($data, $company);

            $message = sprintf(
                'Data berhasil diimpor! (%d Produk, %d Klien, %d Penawaran, %d Invoice)',
                $stats['products'],
                $stats['clients'],
                $stats['quotations'],
                $stats['invoices']
            );

            return back()->with('success', $message);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengimpor data: '.$e->getMessage());
        }
    }

    public function export(Request $request, DataImportService $importService)
    {
        $user = $request->user();
        $company = $user->company;

        if (! $company) {
            return back()->with('error', 'Perusahaan tidak ditemukan.');
        }

        $data = $importService->export($company, $user);
        $fileName = 'paperwork-backup-'.now()->format('Y-m-d-His').'.json';

        return response()->streamDownload(function () use ($data): void {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }, $fileName, [
            'Content-Type' => 'application/json',
        ]);
    }
}
