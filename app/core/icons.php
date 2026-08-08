<?php
/**
 * Inline SVG icon set (24x24, stroke-based, crisp at any size).
 * Usage: tc_icon('shield', 'icon-lg')
 */
function tc_icon(string $name, string $class = ''): string
{
    $paths = [
        // Brand / company icons
        'registry'  => '<path d="M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/><circle cx="8.5" cy="11" r="2.2"/><path d="M5.4 16.5c.6-1.7 1.8-2.5 3.1-2.5s2.5.8 3.1 2.5"/><path d="M14 9.5h5M14 12.5h5M14 15.5h3"/>',
        'soc'       => '<path d="M12 3 4.5 6v5c0 4.6 3.2 8.1 7.5 10 4.3-1.9 7.5-5.4 7.5-10V6L12 3Z"/><circle cx="12" cy="11.5" r="3.4"/><path d="M12 9.4v2.1l1.5 1.5"/>',
        'cln'       => '<circle cx="5.5" cy="6" r="2.3"/><circle cx="18.5" cy="6" r="2.3"/><circle cx="5.5" cy="18" r="2.3"/><circle cx="18.5" cy="18" r="2.3"/><circle cx="12" cy="12" r="2.6"/><path d="M7.3 7.4 10 10m4 4 2.7 2.6M16.7 7.4 14 10m-4 4-2.7 2.6"/>',
        'institute' => '<path d="m12 4 10 4.5L12 13 2 8.5 12 4Z"/><path d="M6.5 10.5V15c0 1.4 2.5 3 5.5 3s5.5-1.6 5.5-3v-4.5"/><path d="M22 8.5V14"/>',
        'park'      => '<path d="M3 19V9.8L12 5l9 4.8V19"/><path d="M3 19h18"/><path d="M7.5 19v-5.5h4V19M14.5 19v-3.5h3V19"/><path d="M7.5 10.6h9"/>',
        'truck'     => '<path d="M2 7h11v9H2zM13 10h4.5L21 13v3h-8"/><circle cx="6" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>',
        'group'     => '<rect x="9" y="3" width="6" height="5" rx="1"/><rect x="2.5" y="15.5" width="6" height="5" rx="1"/><rect x="15.5" y="15.5" width="6" height="5" rx="1"/><path d="M12 8v4M5.5 15.5V12h13v3.5"/>',

        // UI icons
        'arrow'     => '<path d="M5 12h14m-6-6 6 6-6 6"/>',
        'arrow-up'  => '<path d="M12 19V5m-6 6 6-6 6 6"/>',
        'check'     => '<path d="m4.5 12.5 5 5L19.5 7"/>',
        'mail'      => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/>',
        'phone'     => '<path d="M6.8 3.5h2.9l1.5 4.2-2.1 1.6a12.4 12.4 0 0 0 5.6 5.6l1.6-2.1 4.2 1.5v2.9c0 1-.8 1.9-1.9 1.8C10.4 18.4 5.6 13.6 5 5.4c-.1-1 .8-1.9 1.8-1.9Z"/>',
        'pin'       => '<path d="M12 21s7-6.1 7-11.2A7 7 0 0 0 5 9.8C5 14.9 12 21 12 21Z"/><circle cx="12" cy="9.8" r="2.6"/>',
        'clock'     => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2.5"/>',
        'menu'      => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'close'     => '<path d="m6 6 12 12M18 6 6 18"/>',
        'chev-l'    => '<path d="m14.5 5.5-6.5 6.5 6.5 6.5"/>',
        'chev-r'    => '<path d="m9.5 5.5 6.5 6.5-6.5 6.5"/>',
        'external'  => '<path d="M14 4h6v6M20 4l-9 9M19 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h5"/>',
        'globe'     => '<circle cx="12" cy="12" r="8.5"/><path d="M3.5 12h17M12 3.5c2.6 2.4 3.8 5.2 3.8 8.5s-1.2 6.1-3.8 8.5c-2.6-2.4-3.8-5.2-3.8-8.5s1.2-6.1 3.8-8.5Z"/>',
        'shield'    => '<path d="M12 3 4.5 6v5c0 4.6 3.2 8.1 7.5 10 4.3-1.9 7.5-5.4 7.5-10V6L12 3Z"/>',
        'spark'     => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M18.4 5.6l-2.8 2.8M8.4 15.6l-2.8 2.8"/>',
        'target'    => '<circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="4.8"/><circle cx="12" cy="12" r="1.4"/>',
        'eye'       => '<path d="M2.5 12S6 5.8 12 5.8 21.5 12 21.5 12 18 18.2 12 18.2 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="2.8"/>',
        'handshake' => '<path d="m11.5 6.5-3.2-1.6a2 2 0 0 0-1.7 0L2 7.5v7l2.5 1.2"/><path d="m22 7.5-4.6-2.6a2 2 0 0 0-1.7 0L11.5 6.5 8.8 9.7a1.3 1.3 0 0 0 1.8 1.8l2.4-1.8 5.5 4.6a1.5 1.5 0 0 1-1.9 2.3"/><path d="m14.5 18.5-1.6 1.1a1.5 1.5 0 0 1-2-2.1"/><path d="M16.6 16.8a1.5 1.5 0 0 1-2.1 2"/><path d="M22 14.5l-2 1"/>',
        'leaf'      => '<path d="M5 19c0-8 5-13 14-14-.5 9-5 14-12 14"/><path d="M5 19c2-5 6-9 10-11"/>',
        'users'     => '<circle cx="9" cy="8.5" r="3.2"/><path d="M3.5 19c.8-3.3 2.9-5 5.5-5s4.7 1.7 5.5 5"/><circle cx="16.8" cy="9.5" r="2.6"/><path d="M16.5 14.2c2.2.2 3.6 1.7 4.2 4.3"/>',
        'doc'       => '<path d="M6 3h8l4 4v14H6V3Z"/><path d="M14 3v4h4M9.5 12h5M9.5 15.5h5"/>',
        'lock'      => '<rect x="5.5" y="10.5" width="13" height="9" rx="1.6"/><path d="M8.5 10.5V8a3.5 3.5 0 0 1 7 0v2.5"/>',
        'linkedin'  => '<rect x="3" y="3" width="18" height="18" rx="2.5"/><path d="M7.5 10.5v6M7.5 7.4v.2M11.5 16.5v-3.6c0-1.3.9-2.4 2.2-2.4 1.4 0 2.3 1 2.3 2.5v3.5"/>',
        'x-social'  => '<path d="m4.5 4.5 6.1 7.9-6.4 7.1h2.6l5-5.6 4.3 5.6h4.4l-6.6-8.5 5.9-6.5h-2.6l-4.5 5-3.8-5H4.5Z"/>',
        'facebook'  => '<path d="M14 8.5h3V5h-3a4 4 0 0 0-4 4v2.5H7.5V15H10v6h3.5v-6h3l.5-3.5h-3.5V9.3a.8.8 0 0 1 .8-.8H14Z"/>',
        'instagram' => '<rect x="3.5" y="3.5" width="17" height="17" rx="4.5"/><circle cx="12" cy="12" r="3.8"/><circle cx="17.2" cy="6.8" r="0.6"/>',
        'youtube'   => '<path d="M21 8.2a2.6 2.6 0 0 0-1.8-1.9C17.6 6 12 6 12 6s-5.6 0-7.2.3A2.6 2.6 0 0 0 3 8.2 27 27 0 0 0 2.7 12 27 27 0 0 0 3 15.8a2.6 2.6 0 0 0 1.8 1.9c1.6.3 7.2.3 7.2.3s5.6 0 7.2-.3a2.6 2.6 0 0 0 1.8-1.9A27 27 0 0 0 21.3 12 27 27 0 0 0 21 8.2Z"/><path d="m10.2 9.6 4.3 2.4-4.3 2.4V9.6Z"/>',
    ];

    $d = $paths[$name] ?? $paths['spark'];
    $cls = trim('tc-icon ' . $class);
    return '<svg class="' . e($cls) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
        . 'stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
}
