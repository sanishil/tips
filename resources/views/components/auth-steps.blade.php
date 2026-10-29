@props(['steps', 'current'])

<ol class="auth-steps" aria-label="Steps">
    @foreach ($steps as $index => $label)
        @php($number = $index + 1)
        <li @class(['is-done' => $number < $current, 'is-current' => $number === $current]) @if ($number === $current) aria-current="step" @endif>
            <span class="auth-steps-number">
                @if ($number < $current)
                    <i class="fa-solid fa-check" aria-hidden="true"></i><span class="visually-hidden">Done:</span>
                @else
                    {{ $number }}
                @endif
            </span>
            {{ $label }}
        </li>
    @endforeach
</ol>
