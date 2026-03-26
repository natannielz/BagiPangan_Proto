@props(['status'])

@php
    $styles = [
        'available'             => 'bg-green-100 text-green-800',
        'claimed'               => 'bg-blue-100 text-blue-800',
        'awaiting_confirmation' => 'bg-amber-100 text-amber-800',
        'completed'             => 'bg-emerald-50 text-emerald-700',
        'cancelled'             => 'bg-red-100 text-red-700',
        'pending'               => 'bg-yellow-100 text-yellow-800',
        'rejected'              => 'bg-red-100 text-red-700',
        'approved'              => 'bg-green-100 text-green-800',
    ];
    $labels = [
        'available'             => 'Tersedia',
        'claimed'               => 'Diklaim',
        'awaiting_confirmation' => 'Menunggu Konfirmasi',
        'completed'             => 'Selesai',
        'cancelled'             => 'Dibatalkan',
        'pending'               => 'Menunggu Moderasi',
        'rejected'              => 'Ditolak',
        'approved'              => 'Disetujui',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ' . ($styles[$status] ?? 'bg-gray-100 text-gray-700')]) }}>
    {{ $labels[$status] ?? ucfirst($status) }}
</span>
