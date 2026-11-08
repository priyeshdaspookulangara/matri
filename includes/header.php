<?php
// includes/header.php - Global Header Layout
declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

$currentUser = getCurrentUser();
$flash = getFlash();
$pageTitle = $pageTitle ?? 'Guruvision Matrimony - Kerala Ezhava & Thiyya Matrimonial Portal';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($pageTitle) ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        },
                        maroon: {
                            800: '#800020',
                            900: '#5c0017',
                        },
                        gold: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome / Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif-title { font-family: 'Playfair Display', serif; }
        .bg-pattern {
            background-color: #fffaf0;
            background-image: radial-gradient(#ea580c 0.5px, transparent 0.5px), radial-gradient(#ea580c 0.5px, #fffaf0 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            background-opacity: 0.05;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen">

    <!-- Top Contact & Welcome Bar -->
    <div class="bg-gradient-to-r from-red-950 via-maroon-900 to-red-950 text-white text-xs sm:text-sm py-2 px-4 shadow-sm border-b border-yellow-600/30">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
            <div class="flex items-center space-x-4">
                <span class="flex items-center gap-1.5 text-amber-200">
                    <i class="fa-solid fa-phone text-xs"></i>
                    <a href="tel:+914844042401" class="hover:underline">+91 484 4042401</a>
                </span>
                <span class="hidden md:inline text-amber-400/40">|</span>
                <span class="flex items-center gap-1.5 text-amber-200">
                    <i class="fa-solid fa-mobile-screen-button text-xs"></i>
                    <a href="tel:+919744222772" class="hover:underline">+91 97 44 222772</a>
                </span>
                <span class="hidden lg:inline text-amber-200/80 italic">
                    (Guruvision Ezhava &amp; Thiyya Portal)
                </span>
            </div>

            <div class="flex items-center space-x-3">
                <?php if ($currentUser): ?>
                    <span class="text-amber-100">
                        Welcome, <strong><?= sanitize($currentUser['full_name']) ?></strong> (<?= sanitize($currentUser['profile_id']) ?>)
                    </span>
                    <a href="/dashboard/index.php" class="bg-amber-600 hover:bg-amber-500 text-white px-2.5 py-1 rounded text-xs font-semibold inline-flex items-center gap-1">
                        <i class="fa-solid fa-gauge"></i> Dashboard
                    </a>
                    <a href="/logout.php" class="text-slate-300 hover:text-white px-1.5 py-1 text-xs">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </a>
                <?php else: ?>
                    <span class="text-amber-200 hidden sm:inline">Guest Welcome!</span>
                    <a href="/login.php" class="text-white hover:text-amber-300 font-medium inline-flex items-center gap-1">
                        <i class="fa-solid fa-arrow-right-to-bracket text-xs"></i> Member Login
                    </a>
                    <span class="text-amber-400/40">|</span>
                    <a href="/register.php" class="bg-amber-500 hover:bg-amber-600 text-slate-900 px-3 py-1 rounded-full text-xs font-bold transition shadow-sm inline-flex items-center gap-1">
                        <i class="fa-solid fa-user-plus text-xs"></i> Register FREE
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Header & Branding -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="/index.php" class="flex items-center space-x-3 group">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-600 via-orange-500 to-red-600 flex items-center justify-center text-white shadow-md group-hover:scale-105 transition-transform">
                        <i class="fa-solid fa-ring text-2xl"></i>
                    </div>
                    <div>
                        <div class="font-serif-title text-2xl font-black text-red-950 tracking-tight leading-none group-hover:text-primary-600 transition">
                            GURUVISION
                        </div>
                        <div class="text-[10px] sm:text-xs font-bold tracking-widest uppercase text-amber-700">
                            Ezhava / Thiyya Matrimonial Portal
                        </div>
                    </div>
                </a>

                <!-- Desktop Navigation Menu -->
                <nav class="hidden lg:flex items-center space-x-1 font-medium text-sm text-slate-700">
                    <a href="/index.php" class="px-3 py-2 rounded-lg hover:text-primary-600 hover:bg-orange-50 transition">Home</a>
                    <a href="/about.php" class="px-3 py-2 rounded-lg hover:text-primary-600 hover:bg-orange-50 transition">About Us</a>
                    <a href="/advanced_search.php" class="px-3 py-2 rounded-lg hover:text-primary-600 hover:bg-orange-50 transition">
                        <i class="fa-solid fa-magnifying-glass text-xs mr-1 text-primary-500"></i> Search
                    </a>
                    <a href="/membership.php" class="px-3 py-2 rounded-lg hover:text-primary-600 hover:bg-orange-50 transition">Membership &amp; Payment</a>
                    <a href="/services.php" class="px-3 py-2 rounded-lg hover:text-primary-600 hover:bg-orange-50 transition">Services</a>
                    <a href="/stories.php" class="px-3 py-2 rounded-lg hover:text-primary-600 hover:bg-orange-50 transition">Success Stories</a>
                    <a href="/faq.php" class="px-3 py-2 rounded-lg hover:text-primary-600 hover:bg-orange-50 transition">FAQ</a>
                    <a href="/contact.php" class="px-3 py-2 rounded-lg hover:text-primary-600 hover:bg-orange-50 transition">Contact Us</a>
                </nav>

                <!-- Action Button -->
                <div class="hidden sm:flex items-center space-x-2">
                    <?php if ($currentUser): ?>
                        <a href="/dashboard/index.php" class="bg-primary-600 hover:bg-primary-700 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow transition flex items-center gap-2">
                            <i class="fa-solid fa-user-circle"></i> My Account
                        </a>
                    <?php else: ?>
                        <a href="/register.php" class="bg-gradient-to-r from-primary-600 to-amber-600 hover:from-primary-700 hover:to-amber-700 text-white font-bold text-xs uppercase tracking-wider px-5 py-2.5 rounded-lg shadow-md hover:shadow-lg transition">
                            Register Free
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile menu toggle -->
                <div class="lg:hidden flex items-center">
                    <button id="mobile-menu-btn" type="button" class="text-slate-600 hover:text-slate-900 p-2 focus:outline-none">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-200 py-3 space-y-1 text-sm font-medium">
                <a href="/index.php" class="block px-3 py-2 rounded-md hover:bg-orange-50 hover:text-primary-600">Home</a>
                <a href="/about.php" class="block px-3 py-2 rounded-md hover:bg-orange-50 hover:text-primary-600">About Us</a>
                <a href="/advanced_search.php" class="block px-3 py-2 rounded-md hover:bg-orange-50 hover:text-primary-600">Advanced Search</a>
                <a href="/membership.php" class="block px-3 py-2 rounded-md hover:bg-orange-50 hover:text-primary-600">Membership &amp; Payment</a>
                <a href="/services.php" class="block px-3 py-2 rounded-md hover:bg-orange-50 hover:text-primary-600">Services</a>
                <a href="/stories.php" class="block px-3 py-2 rounded-md hover:bg-orange-50 hover:text-primary-600">Success Stories</a>
                <a href="/faq.php" class="block px-3 py-2 rounded-md hover:bg-orange-50 hover:text-primary-600">FAQ</a>
                <a href="/contact.php" class="block px-3 py-2 rounded-md hover:bg-orange-50 hover:text-primary-600">Contact Us</a>
                <?php if ($currentUser): ?>
                    <a href="/dashboard/index.php" class="block px-3 py-2 rounded-md bg-amber-50 text-amber-800 font-semibold">Dashboard (<?= sanitize($currentUser['profile_id']) ?>)</a>
                    <a href="/logout.php" class="block px-3 py-2 rounded-md text-red-600">Logout</a>
                <?php else: ?>
                    <a href="/login.php" class="block px-3 py-2 rounded-md text-primary-600 font-bold">Login</a>
                    <a href="/register.php" class="block px-3 py-2 rounded-md text-amber-700 font-bold">Register Free</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>

    <!-- Flash Notifications -->
    <?php if ($flash): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
            <?php
            $bg = match($flash['type']) {
                'success' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                'error'   => 'bg-rose-50 text-rose-800 border-rose-300',
                'warning' => 'bg-amber-50 text-amber-800 border-amber-300',
                default   => 'bg-sky-50 text-sky-800 border-sky-300',
            };
            $icon = match($flash['type']) {
                'success' => 'fa-circle-check text-emerald-500',
                'error'   => 'fa-circle-exclamation text-rose-500',
                'warning' => 'fa-triangle-exclamation text-amber-500',
                default   => 'fa-circle-info text-sky-500',
            };
            ?>
            <div class="p-4 rounded-xl border <?= $bg ?> flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-3">
                    <i class="fa-solid <?= $icon ?> text-xl"></i>
                    <span class="font-medium text-sm"><?= sanitize($flash['message']) ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>
    <?php endif; ?>

    <!-- Main Content Container -->
    <main class="flex-grow">
