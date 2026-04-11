/* 
* Main JavaScript for Ex-Student Data System
*/

document.addEventListener('DOMContentLoaded', () => {

    // Sticky Navbar Scroll Effect
    const navbar = document.querySelector('.navbar');

    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
            } else {
                navbar.style.boxShadow = 'var(--shadow-sm)';
            }
        });
    }

    // Status Toggle in Registration Form (Work vs Studies)
    const statusSelect = document.getElementById('currentStatus');
    const jobSection = document.getElementById('jobSection');
    const studiesSection = document.getElementById('studiesSection');

    if (statusSelect && jobSection && studiesSection) {
        statusSelect.addEventListener('change', function () {
            if (this.value === 'working') {
                jobSection.style.display = 'block';
                studiesSection.style.display = 'none';

                // Toggle required attributes for validation
                toggleRequired(jobSection, true);
                toggleRequired(studiesSection, false);
            } else if (this.value === 'studies') {
                jobSection.style.display = 'none';
                studiesSection.style.display = 'block';

                toggleRequired(jobSection, false);
                toggleRequired(studiesSection, true);
            } else {
                jobSection.style.display = 'none';
                studiesSection.style.display = 'none';

                toggleRequired(jobSection, false);
                toggleRequired(studiesSection, false);
            }
        });
    }

    // Helper to toggle required attribute on inputs within a container
    function toggleRequired(container, isRequired) {
        const inputs = container.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (isRequired) {
                input.setAttribute('required', 'required');
            } else {
                input.removeAttribute('required');
            }
        });
    }

    // File Upload Preview
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function (e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
            // Find sibling span or label
            const wrapper = this.closest('.file-upload-wrapper');
            if (wrapper) {
                const textSpan = wrapper.querySelector('span');
                if (textSpan) {
                    textSpan.textContent = "Selected: " + fileName;
                    textSpan.style.color = 'var(--primary-color)';
                }
            }
        });
    });

    // Mobile Menu Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            // Toggle sidebar visibility
            const isVisible = sidebar.style.display === 'block';
            sidebar.style.display = isVisible ? 'none' : 'block';
        });
    }

    if (mobileMenuBtn && navLinks) {
        mobileMenuBtn.addEventListener('click', () => {
            navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
            if (navLinks.style.display === 'flex') {
                navLinks.style.flexDirection = 'column';
                navLinks.style.position = 'absolute';
                navLinks.style.top = '70px';
                navLinks.style.left = '0';
                navLinks.style.width = '100%';
                navLinks.style.backgroundColor = 'white';
                navLinks.style.padding = '20px';
                navLinks.style.boxShadow = '0 5px 10px rgba(0,0,0,0.1)';
            }
        });
    }
});
