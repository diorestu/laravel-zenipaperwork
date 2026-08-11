<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBankAccountRequest;
use App\Http\Requests\UpdateCompanyProfileRequest;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function company()
    {
        return view('settings.company', ['company' => auth()->user()->company]);
    }

    public function mobileCompany()
    {
        return view('mobile.profile', ['company' => auth()->user()->company]);
    }

    public function updateCompany(UpdateCompanyProfileRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('logo')) {
            if ($request->user()->company->logo_path) {
                Storage::disk('public')->delete($request->user()->company->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('company-logos', 'public');
        }
        unset($data['logo']);

        $request->user()->company->update($data);

        if ($request->boolean('from_mobile') || str_contains($request->header('referer', ''), '/mobile')) {
            return redirect()->route('mobile.profile')->with('success', 'Profil perusahaan diperbarui.');
        }

        return redirect()->route('settings.company')->with('success', 'Profil perusahaan diperbarui.');
    }

    public function bankAccounts(Request $request)
    {
        $query = BankAccount::forCompany($request->user()->company_id)->latest();

        if ($request->filled('bank')) {
            $query->where('bank_name', $request->string('bank'));
        }

        if ($request->filled('currency')) {
            $query->where('currency', $request->string('currency'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status')->toString() === 'active');
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('bank_name', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        $bankAccounts = $query->paginate(15)->withQueryString();
        $banks = BankAccount::forCompany($request->user()->company_id)->orderBy('bank_name')->distinct()->pluck('bank_name');
        $currencies = BankAccount::forCompany($request->user()->company_id)->orderBy('currency')->distinct()->pluck('currency');

        return view('settings.bank-accounts', compact('bankAccounts', 'banks', 'currencies'));
    }

    public function storeBankAccount(StoreBankAccountRequest $request)
    {
        BankAccount::create($request->validated() + [
            'company_id' => $request->user()->company_id,
            'is_primary' => $request->boolean('is_primary'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->boolean('from_mobile') || str_contains($request->header('referer', ''), '/mobile')) {
            return redirect()->route('mobile.app')->with('success', 'Rekening bank ditambahkan.');
        }

        return redirect()->route('settings.bank-accounts')->with('success', 'Rekening bank ditambahkan.');
    }

    public function updateBankAccount(StoreBankAccountRequest $request, BankAccount $bankAccount)
    {
        $this->authorize('update', $bankAccount);
        $bankAccount->update($request->validated() + [
            'is_primary' => $request->boolean('is_primary'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('settings.bank-accounts')->with('success', 'Rekening bank diperbarui.');
    }

    public function security(Request $request)
    {
        $user = $request->user();
        $tokens = $user->tokens()->latest()->get();

        return view('settings.security', compact('user', 'tokens'));
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $rules = [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        if ($user->password) {
            $rules['current_password'] = ['required', 'string'];
        }

        $request->validate($rules);

        if ($user->password && ! \Illuminate\Support\Facades\Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->input('password')),
        ]);

        \App\Support\ActivityNotifier::record($user, 'Kata Sandi Diperbarui', 'Kata sandi akun Anda telah berhasil diperbarui.');

        return redirect()->route('settings.security')->with('success', 'Kata sandi Anda berhasil diperbarui.');
    }

    public function revokeToken(Request $request, $tokenId)
    {
        $user = $request->user();
        $token = $user->tokens()->find($tokenId);

        if ($token) {
            $token->delete();
            return redirect()->route('settings.security')->with('success', 'Akses perangkat berhasil dicabut.');
        }

        return redirect()->route('settings.security')->with('error', 'Token tidak ditemukan.');
    }
}
