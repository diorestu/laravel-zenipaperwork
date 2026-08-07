<?php

namespace App\Http\Controllers;

use App\Models\BillingSubmission;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index(Request $request)
    {
        $totalUsers = User::where('role', '!=', 'super_admin')->count();
        $totalCompanies = Company::count();
        $confirmedSubmissions = BillingSubmission::where('status', 'confirmed')->get();
        $pendingSubmissions = BillingSubmission::where('status', 'pending')->get();
        $totalRevenue = $confirmedSubmissions->sum('amount');

        $activeSubscriptions = Company::whereNotNull('active_plan')->count();

        $stats = [
            'total_users' => $totalUsers,
            'total_companies' => $totalCompanies,
            'active_subscriptions' => $activeSubscriptions,
            'pending_submissions' => $pendingSubmissions->count(),
            'total_revenue' => $totalRevenue,
        ];

        $latestUsers = User::with('company')->where('role', '!=', 'super_admin')->latest()->take(8)->get();

        $submissionsQuery = BillingSubmission::with('company')->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $submissionsQuery->whereHas('company', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $submissionsQuery->where('status', $request->input('status'));
        }

        $recentSubmissions = $submissionsQuery->paginate(10)->withQueryString();

        return view('super-admin.index', compact('stats', 'latestUsers', 'recentSubmissions'));
    }

    public function users(Request $request)
    {
        $query = User::with('company')
            ->where('role', '!=', 'super_admin')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('company', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        $users = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => User::where('role', '!=', 'super_admin')->count(),
            'verified' => User::where('role', '!=', 'super_admin')->where('is_verified', true)->count(),
            'owners' => User::where('role', 'owner')->count(),
            'admins' => User::where('role', 'admin')->count(),
        ];

        return view('super-admin.users', compact('users', 'stats'));
    }

    public function reports(Request $request)
    {
        $query = BillingSubmission::with('company')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('package')) {
            $query->where('package', $request->input('package'));
        }

        $submissions = $query->paginate(20)->withQueryString();

        $confirmed = BillingSubmission::where('status', 'confirmed')->get();
        $totalRevenue = $confirmed->sum('amount');
        $monthlyRevenue = BillingSubmission::where('status', 'confirmed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $packageDistribution = [
            'starter' => Company::where('active_plan', 'starter')->count(),
            'business' => Company::where('active_plan', 'business')->count(),
            'enterprise' => Company::where('active_plan', 'enterprise')->count(),
        ];

        return view('super-admin.reports', compact('submissions', 'totalRevenue', 'monthlyRevenue', 'packageDistribution'));
    }

    public function grantBypass(Request $request, User $user)
    {
        $data = $request->validate([
            'active_plan' => ['required', 'in:starter,business,enterprise'],
            'subscription_ends_at' => ['required', 'date', 'after:today'],
        ]);

        $company = $user->company;
        if (! $company) {
            return back()->with('error', 'Pengguna belum memiliki perusahaan terhubung.');
        }

        $endsAt = \Carbon\Carbon::parse($data['subscription_ends_at'])->endOfDay();

        $company->update([
            'active_plan' => $data['active_plan'],
            'subscription_ends_at' => $endsAt,
        ]);

        BillingSubmission::create([
            'company_id' => $company->id,
            'package' => $data['active_plan'],
            'billing_period' => 'custom_bypass',
            'amount' => 0,
            'payment_method' => 'admin_whitelist',
            'status' => 'confirmed',
        ]);

        return back()->with('success', "Akses Whitelist paket ".str($data['active_plan'])->headline()." berhasil diberikan ke {$user->name} ({$company->name}) hingga ".$endsAt->format('d M Y').'.');
    }

    public function revokeBypass(User $user)
    {
        $company = $user->company;
        if ($company) {
            $company->update([
                'active_plan' => null,
                'subscription_ends_at' => null,
            ]);
        }

        return back()->with('success', "Akses Whitelist untuk {$user->name} berhasil dicabut.");
    }

    public function confirmBilling(BillingSubmission $billingSubmission)
    {
        return $this->activateBilling($billingSubmission);
    }

    public function activateBilling(BillingSubmission $billingSubmission)
    {
        BillingSubmission::forCompany($billingSubmission->company_id)
            ->whereKeyNot($billingSubmission->id)
            ->where('status', 'confirmed')
            ->update(['status' => 'stopped']);

        $billingSubmission->update(['status' => 'confirmed']);

        $company = $billingSubmission->company;
        $endsAt = $billingSubmission->billing_period === 'yearly' ? now()->addYear() : now()->addMonth();

        $company->update([
            'active_plan' => $billingSubmission->package,
            'subscription_ends_at' => $endsAt,
        ]);

        return back()->with('success', 'Billing user berhasil diaktifkan.');
    }

    public function stopBilling(BillingSubmission $billingSubmission)
    {
        $billingSubmission->update(['status' => 'stopped']);

        $billingSubmission->company->update([
            'active_plan' => null,
            'subscription_ends_at' => null,
        ]);

        return back()->with('success', 'Billing user berhasil dihentikan.');
    }

    public function destroyUser(User $user)
    {
        if ($user->isSuperAdmin() || $user->id === auth()->id()) {
            return back()->with('error', 'Akun Super Admin tidak dapat dihapus.');
        }

        $userName = $user->name;
        $company = $user->company;

        $user->delete();

        if ($company && $company->users()->count() === 0) {
            $company->delete();
        }

        return back()->with('success', "Pengguna {$userName} berhasil dihapus dari sistem.");
    }

    public function destroyBilling(BillingSubmission $billingSubmission)
    {
        $id = $billingSubmission->id;
        $billingSubmission->delete();

        return back()->with('success', "Pengajuan billing #{$id} berhasil dihapus.");
    }

    public function settings()
    {
        $activeGateway = \App\Models\SystemSetting::get('active_payment_gateway', config('services.payment_gateway.active', 'pakasir'));
        $sumopodBaseUrl = \App\Models\SystemSetting::get('sumopod_base_url', config('services.sumopod.base_url', 'https://api-pay-sandbox.sumopod.com/api/v1'));
        $sumopodApiKey = \App\Models\SystemSetting::get('sumopod_api_key', config('services.sumopod.api_key', ''));

        $pakasirProject = \App\Models\SystemSetting::get('pakasir_project', config('services.pakasir.project', 'paperwork'));
        $pakasirApiKey = \App\Models\SystemSetting::get('pakasir_api_key', config('services.pakasir.api_key', ''));

        return view('super-admin.settings', compact(
            'activeGateway',
            'sumopodBaseUrl',
            'sumopodApiKey',
            'pakasirProject',
            'pakasirApiKey'
        ));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->validate([
            'active_payment_gateway' => ['required', 'in:pakasir,sumopod'],
            'sumopod_base_url' => ['nullable', 'string'],
            'sumopod_api_key' => ['nullable', 'string'],
            'pakasir_project' => ['nullable', 'string'],
            'pakasir_api_key' => ['nullable', 'string'],
        ]);

        \App\Models\SystemSetting::set('active_payment_gateway', $data['active_payment_gateway']);
        \App\Models\SystemSetting::set('sumopod_base_url', $data['sumopod_base_url'] ?? 'https://api-pay-sandbox.sumopod.com/api/v1');
        \App\Models\SystemSetting::set('sumopod_api_key', $data['sumopod_api_key'] ?? '');
        \App\Models\SystemSetting::set('pakasir_project', $data['pakasir_project'] ?? '');
        \App\Models\SystemSetting::set('pakasir_api_key', $data['pakasir_api_key'] ?? '');

        return back()->with('success', 'Pengaturan Payment Gateway Superadmin berhasil disimpan.');
    }
}
