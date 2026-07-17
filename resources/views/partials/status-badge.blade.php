@php
    $map = [
        'completed' => 'badge-completed',
        'pending' => 'badge-pending',
        'rejected' => 'badge-rejected',
        'open' => 'badge-open',
        'closed' => 'badge-closed',
    ];
@endphp
<span class="badge {{ $map[$status] ?? 'badge-role' }}">{{ $label }}</span>
