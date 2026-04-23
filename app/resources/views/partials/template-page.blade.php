@php
    $templatePath = base_path('../template/' . $template);
    $html = is_file($templatePath) ? file_get_contents($templatePath) : '<h1>Template not found</h1>';

    $routeMap = [
        'login.html' => route('login'),
        'register.html' => route('register'),
        'forgot.html' => route('forgot-password'),
        'default.html' => route('feed.index'),
        'default-member.html' => route('members.index'),
        'author-page.html' => auth()->check() ? route('profile.show', auth()->id()) : route('login'),
        'user-page.html' => auth()->check() ? route('profile.edit') : route('login'),
        'default-settings.html' => auth()->check() ? route('settings.index') : route('login'),
        'default-notification.html' => auth()->check() ? route('notifications.index') : route('login'),
        'default-message.html' => auth()->check() ? route('messages.index') : route('login'),
        'default-group.html' => auth()->check() ? route('groups.index') : route('login'),
        'group-page.html' => auth()->check() ? route('groups.show', 1) : route('login'),
        '404.html' => auth()->check() ? route('feed.index') : route('login'),
    ];

    foreach ($routeMap as $file => $url) {
        $html = str_replace('href="' . $file . '"', 'href="' . $url . '"', $html);
    }

    // Normalize broken local Google Fonts references present in some template pages.
    $googleFontsUrl = 'https://fonts.googleapis.com/css2?family=Fredoka+One&family=Montserrat:wght@300;400;500;600;700;800&display=swap';
    $html = str_replace(
        ['../../css2', '../../css2-1', '../../css2-2', '../../css2-3', 'css2', 'css2-1', 'css2-2', 'css2-3'],
        [$googleFontsUrl, $googleFontsUrl, $googleFontsUrl, $googleFontsUrl, $googleFontsUrl, $googleFontsUrl, $googleFontsUrl, $googleFontsUrl],
        $html
    );

    // Rewrite static asset URLs to Laravel public assets.
    $html = preg_replace_callback(
        '/\b(href|src)=["\']([^"\']+)["\']/i',
        static function (array $matches): string {
            $attr = $matches[1];
            $url = $matches[2];

            if (
                str_starts_with($url, 'http://')
                || str_starts_with($url, 'https://')
                || str_starts_with($url, '//')
                || str_starts_with($url, 'data:')
                || str_starts_with($url, 'mailto:')
                || str_starts_with($url, '#')
                || str_starts_with($url, '{{')
                || str_starts_with($url, '/')
            ) {
                return $matches[0];
            }

            $assetRoots = ['css/', 'js/', 'images/', 'fonts/', 'vendor/', 'demo/'];
            foreach ($assetRoots as $root) {
                if (str_starts_with($url, $root)) {
                    return $attr . '="' . asset($url) . '"';
                }
            }

            return $matches[0];
        },
        $html
    );

    // Rewrite common inline style background-image URLs.
    $html = str_replace('url(images/', 'url(' . asset('images/') . '/', $html);
    $html = str_replace('url(../images/', 'url(' . asset('images/') . '/', $html);

    if (auth()->check()) {
        $logoutForm = '<form method="POST" action="' . route('logout') . '" style="display:inline;">'
            . csrf_field()
            . '<button type="submit" class="menu-item"><i class="ti-power-off text-grey-500"></i><span>Logout</span></button>'
            . '</form>';
        $html = preg_replace('/<a href="login\.html"[^>]*>.*?<\/a>/s', $logoutForm, $html, 1);
    }
@endphp
{!! $html !!}
