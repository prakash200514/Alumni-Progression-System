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

    // File Upload Preview (Simple filename update)
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function (e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
            const label = this.parentElement.querySelector('span') || this.parentElement;
            // You can add logic here to update a label text if you have a custom UI
            console.log(`Selected file: ${fileName}`);
        });
    });

    // Form Validation (Bootstrap-like)
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();

                // Find invalid fields and add error class
                const inputs = form.querySelectorAll('input:invalid, select:invalid, textarea:invalid');
                inputs.forEach(input => {
                    input.classList.add('error');
                    // Reset error on input
                    input.addEventListener('input', () => {
                        input.classList.remove('error');
                    }, { once: true });
                });

                alert('Please fill in all required fields correctly.');
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Sidebar Mobile Toggle (if we add a hamburger button for dashboard)
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active'); // active class would need CSS to show/hide
        });
    }
});
