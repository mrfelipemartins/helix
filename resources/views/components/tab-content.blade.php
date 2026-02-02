@props([
    "name",
])

<div x-show="active === '{{ $name }}'" x-cloak>
    {{ $slot }}
</div>
