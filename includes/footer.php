</main>

<!-- Footer -->
<footer class="lg:ml-64 bg-white border-t">
    <div class="px-6 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
<div class="flex items-center space-x-3">
    <!-- Logo Wrapper -->
    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
        <img
            src="assets/images/logo-mea.svg"
            alt="Logo COBIT MEA"
            class="w-10 h-10 object-contain"
        >
    </div>

    <!-- Nama Aplikasi -->
    <span class="text-xl font-bold text-gray-800">
        <?php echo SITE_NAME; ?>
    </span>
</div>

            <div class="flex items-center space-x-6">
                <a href="?page=tentang_sistem" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Tentang Sistem</a>
                <a href="?page=panduan_penggunaan" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Panduan Pengguna</a>
                <a href="?page=kebijakan_privasi" class="text-sm text-gray-600 hover:text-blue-600 transition-colors">Kebijakan Privasi</a>
            </div>
        </div>
    </div>
</footer>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    function toggleProfileMenu() {
        const menu = document.getElementById('profileMenu');
        menu.classList.toggle('hidden');
    }

    // Close profile menu when clicking outside
    document.addEventListener('click', function(event) {
        const profileMenu = document.getElementById('profileMenu');
        const profileButton = event.target.closest('button[onclick="toggleProfileMenu()"]');

        if (!profileButton && !profileMenu.contains(event.target)) {
            profileMenu.classList.add('hidden');
        }
    });

    // Close sidebar on mobile when clicking menu item
    document.querySelectorAll('.sidebar-item').forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth < 1024) {
                toggleSidebar();
            }
        });
    });

    function confirmDelete(message) {
        return confirm(message || 'Apakah Anda yakin ingin menghapus data ini?');
    }
</script>
</body>

</html>