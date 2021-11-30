<a href="{{ $link }}" {{ $attributes->merge(['class' => 'btn-secondary rounded f-15']) }}>
    @if ($icon != '')
        <i class="fa fa-{{ $icon }} mr-1"></i>
    @endif
    {{ $slot }}
</a>
