@props([
    'text' => null,
    'empty' => 'No bio yet.',
])

@php
    $bio = trim((string) $text);
    $pattern = '~(https?://[^\s<>"\']+|www\.[^\s<>"\']+)~i';
    $parts = $bio === '' ? [] : preg_split($pattern, $bio, -1, PREG_SPLIT_DELIM_CAPTURE);
@endphp

@if($bio === '')
    <span>{{ $empty }}</span>
@else
    @foreach($parts as $part)
        @if(preg_match($pattern, $part))
            @php
                $label = $part;
                $trailing = '';

                if (preg_match('/[.,!?;:)]$/', $label)) {
                    $trailing = substr($label, -1);
                    $label = substr($label, 0, -1);
                }

                $href = \Illuminate\Support\Str::startsWith(strtolower($label), ['http://', 'https://'])
                    ? $label
                    : 'https://' . $label;
            @endphp

            <a href="{{ $href }}" target="_blank" rel="noopener noreferrer nofollow" class="text-violet-300 hover:text-violet-200 hover:underline break-all">{{ $label }}</a>{{ $trailing }}
        @else
            {{ $part }}
        @endif
    @endforeach
@endif
