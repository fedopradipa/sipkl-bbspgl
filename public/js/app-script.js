// This event listener runs once the entire HTML document has been loaded.
document.addEventListener('DOMContentLoaded', function () {
    const mainLayout = document.querySelector('.main-layout');
    const toggler = document.getElementById('universal-toggler');
    const sidebarNavLinks = document.querySelectorAll('#sidebar .nav-pills .nav-link');

    // --- Sidebar Toggle Logic ---
    const toggleSidebar = () => {
        mainLayout.classList.toggle('sidebar-collapsed');
    };

    // Event listener for the universal toggle button
    if (toggler) {
        toggler.addEventListener('click', toggleSidebar);
    }
    
    // Automatically collapse sidebar on smaller screens on initial load
    if (window.innerWidth < 992) {
        mainLayout.classList.add('sidebar-collapsed');
    }

    // --- Active Navigation Link Logic ---
    // This logic runs on page load to set the correct 'active' link
    // based on the current URL. This makes the active state work
    // across different pages.
    let isLinkActive = false;

    sidebarNavLinks.forEach(link => {
        // Skip dropdown toggles in this logic
        if (link.classList.contains('dropdown-toggle')) {
            return;
        }
        
        // Compare the link's full resolved URL with the current page's URL
        // `link.href` gives the full absolute URL (e.g., "http://.../tes1.html")
        if (link.href === window.location.href) {
            link.classList.add('active');
            link.setAttribute('aria-current', 'page');
            isLinkActive = true;
        } else {
            // Ensure other links are not active
            link.classList.remove('active');
            link.removeAttribute('aria-current');
        }
    });

    // Fallback: If no specific link matched (e.g., you are on the root domain),
    // activate the first navigation link, which is typically the Dashboard.
    if (!isLinkActive && sidebarNavLinks.length > 0) {
            const firstLink = sidebarNavLinks[0];
            if (firstLink && !firstLink.classList.contains('dropdown-toggle')) {
            firstLink.classList.add('active');
            firstLink.setAttribute('aria-current', 'page');
            }
    }
});