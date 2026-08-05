<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BillingSubmissionResource;
use App\Models\BillingSubmission;
use App\Services\PakasirPaymentGateway;
use App\Support\BillingPlans;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class BillingController extends Controller
{
    public function plans(Request $request)
    {
        $activePlan = BillingSubmission::forCompany($request->user()->company_id)
            ->where('status', 'confirmed')
            ->latest()
            ->value('package');

        return response()->json([
            'data' => collect(BillingPlans::all())->map(fn (array $plan): array => [
                ...$plan,
                'yearly_amount' => BillingPlans::amountFor($plan, 'yearly'),
                'is_active' => $activePlan === $plan['slug'],
            ])->values(),
        ]);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $submissions = BillingSubmission::query()
            ->forCompany($request->user()->company_id)
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->query('status')))
            ->latest()
            ->paginate(min(max((int) $request->query('per_page', 15), 1), 100));

        return BillingSubmissionResource::collection($submissions);
    }

    public function store(Request $request, PakasirPaymentGateway $pakasir): BillingSubmissionResource
    {
        $data = $request->validate([
            'package' => ['required', 'string', Rule::in(collect(BillingPlans::all())->pluck('slug')->all())],
            'billing_period' => ['required', 'string', 'in:monthly,yearly'],
            'payment_method' => ['required', 'string', 'in:qris,manual_transfer'],
            'proof' => ['nullable', 'file', 'max:4096'],
            'notes' => ['nullable', 'string'],
        ]);

        $plan = BillingPlans::find($data['package']);
        abort_unless($plan, 422);

        $data['amount'] = BillingPlans::amountFor($plan, $data['billing_period']);

        if ($request->hasFile('proof')) {
            $data['proof_path'] = $request->file('proof')->store('billing-proofs', 'public');
        }
        unset($data['proof']);

        $submission = BillingSubmission::create($data + [
            'company_id' => $request->user()->company_id,
            'payment_gateway' => $data['payment_method'] === 'qris' ? 'pakasir' : null,
            'status' => 'pending',
        ]);

        if ($submission->payment_method === 'qris') {
            $submission->update(['payment_order_id' => 'BILL-'.$submission->id]);
            $payload = $pakasir->createQris($submission->refresh());
            $submission->update([
                'payment_number' => $payload['payment_number'] ?? $payload['number'] ?? data_get($payload, 'payment.payment_number'),
                'payment_url' => $payload['payment_url'] ?? $payload['checkout_url'] ?? $payload['url'] ?? null,
                'payment_payload' => $payload,
            ]);
        }

        return new BillingSubmissionResource($submission->refresh());
    }

    public function show(Request $request, BillingSubmission $billingSubmission): BillingSubmissionResource
    {
        $this->authorize('view', $billingSubmission);

        return new BillingSubmissionResource($billingSubmission);
    }
}
