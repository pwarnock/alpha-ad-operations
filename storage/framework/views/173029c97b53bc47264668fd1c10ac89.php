<nav class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="<?php echo e(route('home')); ?>" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">A</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Alpha Ad Operations</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="<?php echo e(route('home')); ?>" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    Home
                </a>
                <a href="<?php echo e(route('pricing')); ?>" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    Pricing
                </a>
                <a href="/admin" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    Login
                </a>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button type="button" class="text-gray-700 hover:text-blue-600 p-2 rounded-md" onclick="toggleMobileMenu()">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="<?php echo e(route('home')); ?>" class="text-gray-700 hover:text-blue-600 block px-3 py-2 rounded-md text-base font-medium">
                    Home
                </a>
                <a href="<?php echo e(route('pricing')); ?>" class="text-gray-700 hover:text-blue-600 block px-3 py-2 rounded-md text-base font-medium">
                    Pricing
                </a>
                <a href="/admin" class="text-gray-700 hover:text-blue-600 block px-3 py-2 rounded-md text-base font-medium">
                    Login
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
}
</script><?php /**PATH /var/www/html/resources/views/components/marketing/header.blade.php ENDPATH**/ ?>