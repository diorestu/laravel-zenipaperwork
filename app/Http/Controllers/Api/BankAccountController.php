<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBankAccountRequest;
use App\Http\Resources\BankAccountResource;
use App\Models\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BankAccountController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $accounts = BankAccount::forCompany($request->user()->company_id)
            ->when($request->boolean('active_only'), fn ($q) => $q->where('is_active', true))
            ->latest()
            ->get();

        return BankAccountResource::collection($accounts);
    }

    public function store(StoreBankAccountRequest $request): BankAccountResource
    {
        $companyId = $request->user()->company_id;
        $data = $request->validated();

        if (! empty($data['is_primary'])) {
            BankAccount::forCompany($companyId)->update(['is_primary' => false]);
        }

        $account = BankAccount::create($data + ['company_id' => $companyId]);

        return new BankAccountResource($account);
    }

    public function update(StoreBankAccountRequest $request, BankAccount $bankAccount): BankAccountResource
    {
        $this->authorize('update', $bankAccount);
        $data = $request->validated();

        if (! empty($data['is_primary'])) {
            BankAccount::forCompany($bankAccount->company_id)
                ->whereKeyNot($bankAccount->id)
                ->update(['is_primary' => false]);
        }

        $bankAccount->update($data);

        return new BankAccountResource($bankAccount->refresh());
    }
}
