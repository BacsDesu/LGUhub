<?php
/**
 * System settings + theme color palette resolution.
 */

// ============================================
// SYSTEM SETTINGS AND THEME COLORS
// ============================================
$system_title = getSetting($conn, 'system_title') ?: 'LGU Support Hub';
$system_logo = getSetting($conn, 'system_logo') ?: 'assets/img/EST.webp';
$login_bg = getSetting($conn, 'login_bg') ?: 'assets/img/EST.webp';
$system_theme = getSetting($conn, 'system_theme') ?: 'dark';

if (isLoggedIn()) {
    $user_theme = getUserTheme($conn, $_SESSION['user_id']);
    $current_theme = $user_theme ? $user_theme : $system_theme;
    $_SESSION['theme_preference'] = $current_theme;
} else {
    $current_theme = $system_theme;
}

// ============================================
// THEME COLORS — refined for contrast + a calmer,
// more official palette per theme. Same 7 named
// themes and the same array keys as before, so
// nothing else in the app needs to change.
// ============================================
$theme_colors = [
    'dark' => [
        'sidebar' => '#161a26',
        'sidebar_hover' => '#212739',
        'header' => '#161a26',
        'header_border' => '#262c40',
        'card_bg' => 'rgba(30, 34, 48, 0.92)',
        'card_border' => 'rgba(255, 255, 255, 0.07)',
        'text_primary' => '#e7e9ee',
        'text_secondary' => '#9298a8',
        'input_bg' => '#1e2230',
        'input_border' => '#2c3244',
        'stat_bg' => 'rgba(26, 30, 42, 0.92)',
        'accent' => '#6C63FF',
        'accent_rgb' => '108,99,255',
        'accent_gradient' => 'linear-gradient(135deg, #6C63FF, #5850DB)',
        'sidebar_text' => '#9298a8',
        'sidebar_active' => '#8b84ff',
        'header_text' => '#e7e9ee',
        'login_container' => '#161a26',
        'login_text' => '#e7e9ee',
        'login_input_bg' => '#161a26',
        'bg_color' => '#12151f'
    ],
    'blue' => [
        'sidebar' => '#0e1e30',
        'sidebar_hover' => '#173350',
        'header' => '#0e1e30',
        'header_border' => '#1c3654',
        'card_bg' => 'rgba(255, 255, 255, 0.96)',
        'card_border' => 'rgba(15, 60, 110, 0.12)',
        'text_primary' => '#16283c',
        'text_secondary' => '#54687e',
        'input_bg' => '#eef4fb',
        'input_border' => '#c7dcf0',
        'stat_bg' => 'rgba(255, 255, 255, 0.92)',
        'accent' => '#1668C1',
        'accent_rgb' => '22,104,193',
        'accent_gradient' => 'linear-gradient(135deg, #1668C1, #0F4E93)',
        'sidebar_text' => '#a9c2da',
        'sidebar_active' => '#5fa4ec',
        'header_text' => '#eef4fb',
        'login_container' => '#0e1e30',
        'login_text' => '#eef4fb',
        'login_input_bg' => '#0e1e30',
        'bg_color' => '#eef3f8'
    ],
    'green' => [
        'sidebar' => '#0f2419',
        'sidebar_hover' => '#193c29',
        'header' => '#0f2419',
        'header_border' => '#1f4530',
        'card_bg' => 'rgba(255, 255, 255, 0.96)',
        'card_border' => 'rgba(20, 90, 55, 0.12)',
        'text_primary' => '#152a1e',
        'text_secondary' => '#4f6b58',
        'input_bg' => '#eef8f1',
        'input_border' => '#c6e4d1',
        'stat_bg' => 'rgba(255, 255, 255, 0.92)',
        'accent' => '#238053',
        'accent_rgb' => '35,128,83',
        'accent_gradient' => 'linear-gradient(135deg, #238053, #1B6642)',
        'sidebar_text' => '#a6cdb7',
        'sidebar_active' => '#57c78a',
        'header_text' => '#eef8f1',
        'login_container' => '#0f2419',
        'login_text' => '#eef8f1',
        'login_input_bg' => '#0f2419',
        'bg_color' => '#eef7f1'
    ],
    'purple' => [
        'sidebar' => '#1c1730',
        'sidebar_hover' => '#2c2450',
        'header' => '#1c1730',
        'header_border' => '#332a58',
        'card_bg' => 'rgba(255, 255, 255, 0.96)',
        'card_border' => 'rgba(90, 60, 160, 0.12)',
        'text_primary' => '#221c38',
        'text_secondary' => '#5c5578',
        'input_bg' => '#f4f0fb',
        'input_border' => '#dcd0f0',
        'stat_bg' => 'rgba(255, 255, 255, 0.92)',
        'accent' => '#6B45C9',
        'accent_rgb' => '107,69,201',
        'accent_gradient' => 'linear-gradient(135deg, #6B45C9, #52369E)',
        'sidebar_text' => '#c1b3e0',
        'sidebar_active' => '#a487ea',
        'header_text' => '#f4f0fb',
        'login_container' => '#1c1730',
        'login_text' => '#f4f0fb',
        'login_input_bg' => '#1c1730',
        'bg_color' => '#f3f0f9'
    ],
    'pink' => [
        'sidebar' => '#2a1420',
        'sidebar_hover' => '#452036',
        'header' => '#2a1420',
        'header_border' => '#4a2438',
        'card_bg' => 'rgba(255, 255, 255, 0.96)',
        'card_border' => 'rgba(180, 40, 90, 0.12)',
        'text_primary' => '#341a26',
        'text_secondary' => '#77515f',
        'input_bg' => '#fbeef3',
        'input_border' => '#f0cddd',
        'stat_bg' => 'rgba(255, 255, 255, 0.92)',
        'accent' => '#C43D75',
        'accent_rgb' => '196,61,117',
        'accent_gradient' => 'linear-gradient(135deg, #C43D75, #9C2F5C)',
        'sidebar_text' => '#dcaec2',
        'sidebar_active' => '#e878a8',
        'header_text' => '#fbeef3',
        'login_container' => '#2a1420',
        'login_text' => '#fbeef3',
        'login_input_bg' => '#2a1420',
        'bg_color' => '#faeef2'
    ],
    'orange' => [
        'sidebar' => '#2a1c0e',
        'sidebar_hover' => '#453018',
        'header' => '#2a1c0e',
        'header_border' => '#4a3419',
        'card_bg' => 'rgba(255, 255, 255, 0.96)',
        'card_border' => 'rgba(180, 100, 20, 0.12)',
        'text_primary' => '#332314',
        'text_secondary' => '#7a6248',
        'input_bg' => '#fbf2e8',
        'input_border' => '#f0dcc0',
        'stat_bg' => 'rgba(255, 255, 255, 0.92)',
        'accent' => '#C9701E',
        'accent_rgb' => '201,112,30',
        'accent_gradient' => 'linear-gradient(135deg, #C9701E, #A0580F)',
        'sidebar_text' => '#e0c3a0',
        'sidebar_active' => '#eb9a4e',
        'header_text' => '#fbf2e8',
        'login_container' => '#2a1c0e',
        'login_text' => '#fbf2e8',
        'login_input_bg' => '#2a1c0e',
        'bg_color' => '#faf3e9'
    ],
    'teal' => [
        'sidebar' => '#0c2422',
        'sidebar_hover' => '#153c38',
        'header' => '#0c2422',
        'header_border' => '#1c4540',
        'card_bg' => 'rgba(255, 255, 255, 0.96)',
        'card_border' => 'rgba(10, 130, 115, 0.12)',
        'text_primary' => '#122d29',
        'text_secondary' => '#4d6e69',
        'input_bg' => '#eaf8f5',
        'input_border' => '#c1e6dd',
        'stat_bg' => 'rgba(255, 255, 255, 0.92)',
        'accent' => '#128577',
        'accent_rgb' => '18,133,119',
        'accent_gradient' => 'linear-gradient(135deg, #128577, #0D655A)',
        'sidebar_text' => '#a0d3ca',
        'sidebar_active' => '#3fc3af',
        'header_text' => '#eaf8f5',
        'login_container' => '#0c2422',
        'login_text' => '#eaf8f5',
        'login_input_bg' => '#0c2422',
        'bg_color' => '#eaf7f4'
    ],
];

$current_theme_colors = isset($theme_colors[$current_theme]) ? $theme_colors[$current_theme] : $theme_colors['dark'];
