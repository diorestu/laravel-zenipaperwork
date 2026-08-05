<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCompanyProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function update(UpdateCompanyProfileRequest $request): JsonResponse
    {
        $company = $request->user()->company;
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($company->logo_path) {
                Storage::disk('public')->delete($company->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('company-logos', 'public');
        }
        unset($data['logo']);

        $company->update($data);

        return response()->json([
            'message' => 'Profil perusahaan berhasil diperbarui.',
            'company' => $company->refresh(),
        ]);
    }
}
