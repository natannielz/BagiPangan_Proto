@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-200 focus:border-brand-600 focus:ring-brand-600 rounded-xl shadow-sm']) }}>
