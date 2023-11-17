@php

    $size = (!empty($size)) ? $size : 'large';
    $telegram_login = (!empty($telegram_login) && is_string($telegram_login))
        ? $telegram_login
        : config('telegram.widgets.login.bot_name');

@endphp

<script
    async
    src="{{ config('telegram.widgets.login.src') }}"
    data-telegram-login="{{ $telegram_login }}"
    data-size="{{ $size }}"

    {{-- Отображение фотографии пользователя --}}
    @if(!isset($userpic) || $userpic === false)
        data-userpic="false"
    @endif

    {{-- Радиус угла кнопки --}}
    @if(!empty($radius) && is_int($radius))
        data-radius="{{ $radius }}"
    @endisset

    {{-- Тип авторизации --}}
    @if(isset($url) && $url === false)
        data-onauth="onTelegramAuth(user)"
    @elseif(isset($url) && is_string($url))
        data-auth-url="{{ $url }}"
    @else
        @if(app('router')->has('telegram.bot.oauth'))
            data-auth-url="{{ route('telegram.bot.oauth', [ 'bot_name' => $telegram_login ]) }}"
        @else
            data-onauth="onTelegramAuth(user)"
        @endif
    @endif

    {{-- Запрос доступа для отправки сообщений с бота --}}
    @if(empty($request_access) || $request_access === true)
        data-request-access="write"
    @endisset
></script>

<script type="text/javascript">
    function onTelegramAuth(user) {

        // Создание и запуск события
        document.dispatchEvent(new CustomEvent('on_telegram_oauth', { 'detail': user }));
    }
</script>
