@php
    $toasts = collect([
        session('success') ? ['type' => 'success', 'message' => session('success')] : null,
        session('error') ? ['type' => 'error', 'message' => session('error')] : null,
        session('warning') ? ['type' => 'warning', 'message' => session('warning')] : null,
        session('info') ? ['type' => 'info', 'message' => session('info')] : null,
        session('status') ? ['type' => 'success', 'message' => session('status')] : null,
        $errors->any() ? ['type' => 'error', 'message' => $errors->first()] : null,
    ])->filter()->values();
@endphp

@if ($toasts->isNotEmpty())
    <script type="application/json" data-toast-payload>
        {!! $toasts->toJson(JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
    </script>
@endif
