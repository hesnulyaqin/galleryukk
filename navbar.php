<style>
#dropdown {
    position: relative;
}
</style>
<nav class="bg-white border-gray-200 dark:bg-gray-900 flex justify-end">
    <div class="max-w-screen-xl flex flex-wrap items-end justify-end p-4">

        <button data-collapse-toggle="navbar-default" type="button"
            class="inline-flex items-end p-2 w-10 h-10 justify-end text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
            aria-controls="navbar-default" aria-expanded="false">
            <span class="sr-only">Open main menu</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M1 1h15M1 7h15M1 13h15" />
            </svg>
        </button>

        <div class="hidden w-full md:block md:w-auto" id="navbar-default">
            <ul
                class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
                <li>
                    <a href="#"
                        class="block py-2 px-3 text-white bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:p-0 dark:text-white md:dark:text-blue-500"
                        aria-current="page">Home</a>
                </li>
                <li>
                    <a href="tambahfoto.php?redirect_id=beranda.php" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent
                        md:border-0
                        md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700
                        dark:hover:text-white md:dark:hover:bg-transparent">Tambah
                        Foto</a>
                </li>
                <li>
                    <a href="allalbum.php"
                        class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Album</a>
                </li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <li>
                    <a href="dataalbum.php"
                        class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Dashboard
                        Admin</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>

    </div>

    <div class="akun flex items-center relative">

        <img src="uploads/pp.jpeg" alt="Profile Logo" class="w-8 h-8 rounded-full cursor-pointer mr-2" />

        <span class="text-gray-900 dark:text-cursor-pointer" onclick="toggleDropdown()">
            <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>
        </span>
        <div id="dropdown"
            class="hidden absolute right-0 z-20 w-48 mt-2 bg-white border border-gray-200 rounded-lg shadow-lg dark:bg-gray-800">
            <ul class="py-2 text-sm text-black dark:text-gray-200">

                <?php if (!isset($_SESSION['username'])): ?>
                <li>
                    <a href="login.php"
                        class="block px-4 py-2 text-black hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">Login</a>
                </li>
                <?php else: ?>
                <li>
                    <a href="logout.php"
                        class="block px-4 py-2 text-black hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white">Logout</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>

    </div>

</nav>

<script>
function toggleDropdown() {
    const dropdown = document.getElementById('dropdown');
    dropdown.classList.toggle('hidden');
}

// Menutup dropdown jika diklik di luar
window.onclick = function(event) {
    if (!event.target.closest('.akun')) { // Periksa apakah klik terjadi di luar elemen akun
        const dropdown = document.getElementById('dropdown');
        if (!dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
        }
    }
}
</script>