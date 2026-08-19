<x-mail::message>
    {{-- Greeting --}}
    @if (! empty($greeting))
        # {{ $greeting }}
    @else
        @if ($level === 'error')
            # @lang('Whoops!')
        @else
            # @lang('Hello!')
        @endif
    @endif

    {{-- Intro Lines --}}
    @foreach ($introLines as $line)
        {{ $line }}

    @endforeach

    {{-- استخراج وعرض الرمز (Token) مباشرة بشكل نصي واضح --}}
    @isset($actionUrl)
        @php
            parse_str(parse_url($actionUrl, PHP_URL_QUERY), $urlParams);
            $extractedToken = $urlParams['token'] ?? null;
        @endphp

        @if($extractedToken)
            <x-mail::panel>
                **رمز إعادة التعيين الخاص بك هو:**
                <code style="font-size: 16px; font-weight: bold; color: #d97706;">{{ $extractedToken }}</code>
            </x-mail::panel>
        @endif
    @endisset

    {{-- Action Button --}}
    @isset($actionText)
            <?php
            $color = match ($level) {
                'success', 'error' => $level,
                default => 'primary',
            };
            ?>
        <x-mail::button :url="$actionUrl" :color="$color">
            {{ $actionText }}
        </x-mail::button>
    @endisset

    {{-- Outro Lines --}}
    @foreach ($outroLines as $line)
        {{ $line }}

    @endforeach

    {{-- Salutation --}}
    @if (! empty($salutation))
        {{ $salutation }}
    @else
        @lang('Regards,')<br>
        {{ config('app.name') }}
    @endif

    {{-- Subcopy --}}
    @isset($actionText)
        <x-slot:subcopy>
            @lang(
                "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
                'into your web browser:',
                [
                    'actionText' => $actionText,
                ]
            ) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
        </x-slot:subcopy>
    @endisset
</x-mail::message>
