<?php
// includes/footer.php - Global Footer Layout
declare(strict_types=1);
?>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-300 pt-16 pb-8 border-t border-slate-800 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
                <!-- Col 1: About -->
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-primary-600 flex items-center justify-center text-white shadow">
                            <i class="fa-solid fa-ring text-xl"></i>
                        </div>
                        <span class="font-serif-title text-xl font-bold text-white tracking-wide">Guruvision</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed mb-4">
                        Kerala's premier matrimonial service dedicated to the Ezhava and Thiyya community. Connecting hearts with trusted, verified profiles, privacy, and cultural harmony.
                    </p>
                    <div class="flex space-x-3">
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-primary-600 text-slate-400 hover:text-white flex items-center justify-center transition">
                            <i class="fa-brands fa-facebook-f text-xs"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-primary-600 text-slate-400 hover:text-white flex items-center justify-center transition">
                            <i class="fa-brands fa-instagram text-xs"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-primary-600 text-slate-400 hover:text-white flex items-center justify-center transition">
                            <i class="fa-brands fa-whatsapp text-xs"></i>
                        </a>
                        <a href="#" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-primary-600 text-slate-400 hover:text-white flex items-center justify-center transition">
                            <i class="fa-brands fa-youtube text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div>
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">
                        Quick Links
                    </h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="/index.php" class="hover:text-amber-400 transition flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs text-amber-500"></i> Home</a></li>
                        <li><a href="/about.php" class="hover:text-amber-400 transition flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs text-amber-500"></i> About Guruvision</a></li>
                        <li><a href="/advanced_search.php" class="hover:text-amber-400 transition flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs text-amber-500"></i> Search Profiles</a></li>
                        <li><a href="/stories.php" class="hover:text-amber-400 transition flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs text-amber-500"></i> Success Stories</a></li>
                        <li><a href="/membership.php" class="hover:text-amber-400 transition flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs text-amber-500"></i> Membership Packages</a></li>
                        <li><a href="/faq.php" class="hover:text-amber-400 transition flex items-center gap-2"><i class="fa-solid fa-angle-right text-xs text-amber-500"></i> Frequently Asked Questions</a></li>
                    </ul>
                </div>

                <!-- Col 3: Community & Districts -->
                <div>
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">
                        Kerala Districts
                    </h3>
                    <div class="grid grid-cols-2 gap-x-2 gap-y-1.5 text-xs text-slate-400">
                        <a href="/advanced_search.php?district=Alappuzha" class="hover:text-amber-400">Alappuzha Matrimony</a>
                        <a href="/advanced_search.php?district=Ernakulam" class="hover:text-amber-400">Ernakulam Matrimony</a>
                        <a href="/advanced_search.php?district=Kannur" class="hover:text-amber-400">Kannur Thiyya</a>
                        <a href="/advanced_search.php?district=Kollam" class="hover:text-amber-400">Kollam Ezhava</a>
                        <a href="/advanced_search.php?district=Kottayam" class="hover:text-amber-400">Kottayam Matrimony</a>
                        <a href="/advanced_search.php?district=Kozhikode" class="hover:text-amber-400">Kozhikode Thiyya</a>
                        <a href="/advanced_search.php?district=Palakkad" class="hover:text-amber-400">Palakkad Matrimony</a>
                        <a href="/advanced_search.php?district=Thrissur" class="hover:text-amber-400">Thrissur Ezhava</a>
                        <a href="/advanced_search.php?district=Thiruvananthapuram" class="hover:text-amber-400">Trivandrum Ezhava</a>
                        <a href="/advanced_search.php?district=Malappuram" class="hover:text-amber-400">Malappuram Matrimony</a>
                    </div>
                </div>

                <!-- Col 4: Contact & Support -->
                <div>
                    <h3 class="text-white font-semibold text-sm uppercase tracking-wider mb-4 border-b border-slate-800 pb-2">
                        Get In Touch
                    </h3>
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li class="flex items-start space-x-3">
                            <i class="fa-solid fa-location-dot text-amber-500 mt-1"></i>
                            <span>Guruvision Towers, MG Road, Ernakulam, Kerala 682016</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fa-solid fa-phone text-amber-500"></i>
                            <a href="tel:+914844042401" class="hover:text-amber-400">+91 484 4042401</a>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fa-solid fa-mobile text-amber-500"></i>
                            <a href="tel:+919744222772" class="hover:text-amber-400">+91 97 44 222772</a>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fa-solid fa-envelope text-amber-500"></i>
                            <a href="mailto:support@guruvisionmatrimony.com" class="hover:text-amber-400">support@guruvisionmatrimony.com</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-slate-800 pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <p>&copy; <?= date('Y') ?> Guruvision Matrimony. All Rights Reserved. Modelled for Ezhava &amp; Thiyya Community.</p>
                <div class="flex space-x-6">
                    <a href="#" class="hover:text-slate-400">Privacy Policy</a>
                    <a href="#" class="hover:text-slate-400">Terms of Service</a>
                    <a href="#" class="hover:text-slate-400">Security Guidelines</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
