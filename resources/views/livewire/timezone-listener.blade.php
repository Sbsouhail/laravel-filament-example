<?php

use Livewire\Volt\Component;

new class () extends Component {
    public string $timezone;

    public function updatedTimezone(string $tz): void
    {
        if (empty(session('user_timezone')) || session('user_timezone') !== $tz) {
            session(['user_timezone' => $tz]);
        }
    }
}; ?>

<div style="display:none"></div>
@script
    <script>
        document.addEventListener('livewire:initialized', function() {
            const tz = Intl.DateTimeFormat().resolvedOptions().timeZone;

            // Only send if not already set (avoid extra requests)
            if (!@json(session()->has('user_timezone'))) {
                $wire.$set('timezone', tz);
            }
        });
    </script>
@endscript
