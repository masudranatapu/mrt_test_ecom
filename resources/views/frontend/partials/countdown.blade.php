{{-- Live countdown to $target (Carbon). JS in frontend.js updates the numbers every second. --}}
<div class="countdown {{ $class ?? '' }}" data-countdown="{{ $target->toIso8601String() }}">
    @foreach (['days' => 'Days', 'hours' => 'Hrs', 'minutes' => 'Min', 'seconds' => 'Sec'] as $unit => $label)
        <div class="cd-box">
            <span class="cd-num" data-{{ $unit }}>00</span>
            <span class="cd-label">{{ $label }}</span>
        </div>
    @endforeach
</div>
