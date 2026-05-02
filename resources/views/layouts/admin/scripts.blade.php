    {{-- Theme Toggle Script --}}
<!-- jQuery, Bootstrap JS, DataTables, Toastr, Swal2 -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script>
    (function() {
        var toggle = document.getElementById('themeToggle');
        var label = document.getElementById('themeLabel');
        var html = document.documentElement;
        var moonIcon = toggle.querySelector('.icon-moon');
        var sunIcon = toggle.querySelector('.icon-sun');

        function setTheme(theme) {
            html.setAttribute('data-theme', theme);
            localStorage.setItem('motorzad-theme', theme);
            updateUI(theme);
        }

        function updateUI(theme) {
            if (theme === 'dark') {
                label.textContent = '{{ __("Dark Mode") }}';
                moonIcon.style.display = 'block';
                sunIcon.style.display = 'none';
            } else {
                label.textContent = '{{ __("Light Mode") }}';
                moonIcon.style.display = 'none';
                sunIcon.style.display = 'block';
            }
        }

        // Initialize UI based on current theme
        var current = html.getAttribute('data-theme') || 'light';
        updateUI(current);

        // Toggle handler
        if (toggle) {
            toggle.addEventListener('click', function() {
                var current = html.getAttribute('data-theme');
                var next = current === 'dark' ? 'light' : 'dark';
                setTheme(next);
            });

            // Keyboard accessibility
            toggle.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    toggle.click();
                }
            });
        }
    })();
    </script>
