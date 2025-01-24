<?php
    $settingLogo = \App\Models\SettingWeb::first();
?>
<img src="{{ $settingLogo->settingWebLogo ?? asset('images/logo.svg') }}" {{ $attributes }} width="140" height="140">


