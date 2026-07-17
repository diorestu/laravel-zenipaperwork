@props(['status'])
@php
    $classes = [
        'paid' => 'bg-success-50 text-success-700 border-success-200',
        'partial' => 'bg-warning-50 text-warning-700 border-warning-200',
        'approved' => 'bg-success-50 text-success-700 border-success-200',
        'rejected' => 'bg-error-50 text-error-700 border-error-200',
        'pending' => 'bg-warning-50 text-warning-700 border-warning-200',
        'overdue' => 'bg-error-50 text-error-700 border-error-200',
        'void' => 'bg-gray-100 text-gray-500 border-gray-200',
        'draft' => 'bg-gray-50 text-gray-600 border-gray-200',
        'sent' => 'bg-brand-50 text-brand-700 border-brand-200',
        'applied' => 'bg-brand-50 text-brand-700 border-brand-200',
        'converted' => 'bg-success-50 text-success-700 border-success-200',
    ][$status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
@endphp
<span class="inline-flex rounded-full border px-2 py-1 text-xs font-medium {{ $classes }}">{{ str($status)->headline() }}</span>
