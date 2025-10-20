<footer class="bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Company Info -->
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">A</span>
                    </div>
                    <span class="text-xl font-bold">Alpha Ad Operations</span>
                </div>
                <p class="text-gray-300 mb-4">
                    Streamline your advertising operations with our comprehensive SaaS platform designed for media companies. 
                    Manage campaigns, track performance, and maximize revenue all in one place.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v8.385C8.5 22.954 13.35 18 18.125 18c6.627 0 12-5.373 12-12z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.727 4.958 4.958 0 00-2.163-2.723c-.891-.57-1.781-.97-2.723-1.125A10.004 10.004 0 0012 2c-4.478 0-8.268 2.943-9.543 7a9.97 9.97 0 011.828 3.827 4.9 4.9 0 002.723 1.625 4.902 4.902 0 002.163-2.723 10.004 10.004 0 009.543-7c-1.275 4.057-5.065 7-9.543 7z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.477 2 2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.879V14.89h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46s-1.026-.027-2.238-.027c-1.835 0-2.435 1.122-2.435 2.806V12h2.54l-.406 2.89h-2.134v6.989C18.343 21.128 22 16.991 22 12c0-5.523-4.477-10-10-10z"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Product Links -->
            <div>
                <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider mb-4">Product</h3>
                <ul class="space-y-2">
                    <li><a href="<?php echo e(route('home')); ?>" class="text-gray-400 hover:text-white transition-colors">Features</a></li>
                    <li><a href="<?php echo e(route('pricing')); ?>" class="text-gray-400 hover:text-white transition-colors">Pricing</a></li>
                    <li><a href="/admin" class="text-gray-400 hover:text-white transition-colors">Dashboard</a></li>
                </ul>
            </div>

            <!-- Company Links -->
            <div>
                <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider mb-4">Company</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">About</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact</a></li>
                </ul>
            </div>

            <!-- Legal Links -->
            <div>
                <h3 class="text-sm font-semibold text-gray-300 uppercase tracking-wider mb-4">Legal</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Terms</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Security</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="mt-8 pt-8 border-t border-gray-800">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">
                    © <?php echo e(date('Y')); ?> Alpha Ad Operations. All rights reserved.
                </p>
                <p class="text-gray-400 text-sm mt-2 md:mt-0">
                    Built with ❤️ for media companies
                </p>
            </div>
        </div>
    </div>
</footer><?php /**PATH /var/www/html/resources/views/components/marketing/footer.blade.php ENDPATH**/ ?>