<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isCurrentPage($page) {
    return basename($_SERVER['PHP_SELF']) === $page;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Reliver Management System - Employee Management Solution">
    <title>Reliver Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .dropdown-menu {
            display: none;
            position: absolute;
            z-index: 50;
            min-width: 200px;
        }
        
        .nav-item:hover .dropdown-menu,
        .nav-item:focus-within .dropdown-menu {
            display: block;
        }

        @media (max-width: 768px) {
            .dropdown-menu {
                position: static;
                box-shadow: none;
                padding-left: 1rem;
            }
            
            .mobile-submenu {
                display: none;
            }
            
            .mobile-submenu.active {
                display: block;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
                const mobileMenu = document.getElementById('mobile-menu');
                mobileMenu.classList.toggle('hidden');
            });

            // Mobile submenu toggle
            window.toggleMobileSubmenu = function(submenuId) {
                const submenu = document.getElementById(submenuId);
                const allSubmenus = document.getElementsByClassName('mobile-submenu');
                
                Array.from(allSubmenus).forEach(menu => {
                    if (menu.id !== submenuId) {
                        menu.classList.remove('active');
                    }
                });
                
                submenu.classList.toggle('active');
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(event) {
                const dropdowns = document.getElementsByClassName('dropdown-menu');
                const isClickInsideNav = event.target.closest('.nav-item');
                
                if (!isClickInsideNav) {
                    Array.from(dropdowns).forEach(dropdown => {
                        dropdown.style.display = 'none';
                    });
                }
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                const dropdowns = document.getElementsByClassName('dropdown-menu');
                Array.from(dropdowns).forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            });

            // Keyboard navigation
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    const dropdowns = document.getElementsByClassName('dropdown-menu');
                    Array.from(dropdowns).forEach(dropdown => {
                        dropdown.style.display = 'none';
                    });
                    
                    const mobileMenu = document.getElementById('mobile-menu');
                    if (!mobileMenu.classList.contains('hidden')) {
                        mobileMenu.classList.add('hidden');
                    }
                }
            });
        });
    </script>
</head>
<body class="bg-gray-100">

<?php if(!isCurrentPage('index.php')): ?>
    <nav class="bg-indigo-600 p-4">
        <div class="max-w-7xl mx-auto">
            <!-- Mobile Header -->
            <div class="flex justify-between items-center md:hidden">
                <a href="index.php" class="text-white text-xl font-bold">RMS</a>
                <button id="mobile-menu-button" class="text-white focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex justify-between items-center">
                <a href="index.php" class="text-white text-2xl font-bold">Reliver Management System</a>
                <div class="space-x-6 flex items-center">
                    <!-- Home Menu -->
                    <a href="index.php" class="text-white hover:text-gray-200">Home</a>

                    <!-- Reliver Menu -->
                    <div class="nav-item relative">
                        <button class="text-white hover:text-gray-200 focus:outline-none py-2">
                            Reliver
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="dropdown-menu bg-white rounded-md shadow-lg">
                            <a href="today_reliver.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Today Reliver</a>
                        </div>
                    </div>

                    <!-- Dumper Allocation Menu -->
                    <div class="nav-item relative">
                        <button class="text-white hover:text-gray-200 focus:outline-none py-2">
                            Dumper Allocation
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="dropdown-menu bg-white rounded-md shadow-lg">
                            <a href="assign_dumper.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Assign Dumper</a>
                            <a href="view_dumper.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">View Dumper</a>
                        </div>
                    </div>

                    <!-- Attendance Menu -->
                    <div class="nav-item relative">
                        <button class="text-white hover:text-gray-200 focus:outline-none py-2">
                            Attendance
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="dropdown-menu bg-white rounded-md shadow-lg">
                            <a href="add_attendance.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Add Attendance</a>
                            <a href="view_attendance.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">View Attendance</a>
                        </div>
                    </div>

                    <!-- Overtime Menu -->
                    <div class="nav-item relative">
                        <button class="text-white hover:text-gray-200 focus:outline-none py-2">
                            Overtime
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="dropdown-menu bg-white rounded-md shadow-lg">
                            <a href="assign_ot.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Assign Overtime</a>
                            <a href="view_ot.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">View Overtime</a>
                        </div>
                    </div>

                    <!-- Admin Menu -->
                    <div class="nav-item relative">
                        <button class="text-white hover:text-gray-200 focus:outline-none py-2">
                            Admin
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="dropdown-menu bg-white rounded-md shadow-lg">
                            <a href="add_employee.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Employee</a>
                            <a href="add_dumper.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Dumper</a>
                           <a href="add_user.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">User</a>
                            <a href="emp_attend.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">Employee Details</a>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden mt-4">
                <div class="space-y-2">
                    <!-- Mobile Home Link -->
                    <a href="index.php" class="block text-white py-2 px-4 hover:bg-indigo-700">Home</a>

                    <!-- Mobile Reliver Menu -->
                    <div class="mobile-nav-item">
                        <button onclick="toggleMobileSubmenu('reliver-submenu')" class="w-full text-left text-white py-2 px-4 hover:bg-indigo-700">
                            Reliver
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="reliver-submenu" class="mobile-submenu bg-indigo-700">
                            <a href="today_reliver.php" class="block py-2 px-8 text-white hover:bg-indigo-800">Today Reliver</a>
                        </div>
                    </div>

                    <!-- Mobile Dumper Allocation Menu -->
                    <div class="mobile-nav-item">
                        <button onclick="toggleMobileSubmenu('dumper-submenu')" class="w-full text-left text-white py-2 px-4 hover:bg-indigo-700">
                            Dumper Allocation
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="dumper-submenu" class="mobile-submenu bg-indigo-700">
                            <a href="assign_dumper.php" class="block py-2 px-8 text-white hover:bg-indigo-800">Assign Dumper</a>
                            <a href="view_dumper.php" class="block py-2 px-8 text-white hover:bg-indigo-800">View Dumper</a>
                        </div>
                    </div>

                    <!-- Mobile Attendance Menu -->
                    <div class="mobile-nav-item">
                        <button onclick="toggleMobileSubmenu('attendance-submenu')" class="w-full text-left text-white py-2 px-4 hover:bg-indigo-700">
                            Attendance
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="attendance-submenu" class="mobile-submenu bg-indigo-700">
                            <a href="add_attendance.php" class="block py-2 px-8 text-white hover:bg-indigo-800">Add Attendance</a>
                            <a href="view_attendance.php" class="block py-2 px-8 text-white hover:bg-indigo-800">View Attendance</a>
                        </div>
                    </div>

                    <!-- Mobile Overtime Menu -->
                    <div class="mobile-nav-item">
                        <button onclick="toggleMobileSubmenu('overtime-submenu')" class="w-full text-left text-white py-2 px-4 hover:bg-indigo-700">
                            Overtime
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="overtime-submenu" class="mobile-submenu bg-indigo-700">
                            <a href="assign_ot.php" class="block py-2 px-8 text-white hover:bg-indigo-800">Assign Overtime</a>
                            <a href="view_ot.php" class="block py-2 px-8 text-white hover:bg-indigo-800">View Overtime</a>
                        </div>
                    </div>

                    <!-- Mobile Admin Menu -->
                    <div class="mobile-nav-item">
                        <button onclick="toggleMobileSubmenu('admin-submenu')" class="w-full text-left text-white py-2 px-4 hover:bg-indigo-700">
                            Admin
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="admin-submenu" class="mobile-submenu bg-indigo-700">
                            <a href="admin_employee.php" class="block py-2 px-8 text-white hover:bg-indigo-800">Employee</a>
                            <a href="admin_dumper.php" class="block py-2 px-8 text-white hover:bg-indigo-800">Dumper</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
<?php endif; ?>

<main class="container mx-auto mt-8 px-4" role="main">
</main>

</body>
</html>