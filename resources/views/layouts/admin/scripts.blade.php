    {{-- Theme Toggle Script --}}
<!-- jQuery, Bootstrap JS, DataTables, Toastr, Swal2 -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>
<script>
    if (window.toastr) {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-bottom-center",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        
        // Override error method to prevent auto close
        var originalError = toastr.error;
        toastr.error = function(message, title, optionsOverride) {
            var options = $.extend({}, toastr.options, {
                timeOut: 0,
                extendedTimeOut: 0,
                closeButton: true
            }, optionsOverride);
            return originalError(message, title, options);
        };
    }
</script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('js/admin/ajax-helper.js') }}"></script>
<script>
    if (typeof BidderAjax !== 'undefined') {
        BidderAjax.init($('meta[name="csrf-token"]').attr('content'));
    }
</script>
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

<script>
    // Global translation helper logic
    (function() {
        async function translateText(text, fromLang = 'ar', toLang = 'en') {
            if (!text || text.trim() === '') return '';
            try {
                const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=${fromLang}&tl=${toLang}&dt=t&q=${encodeURIComponent(text)}`;
                const response = await fetch(url);
                const data = await response.json();
                if (data && data[0]) {
                    return data[0].map(x => x[0]).join('');
                }
                return text;
            } catch (e) {
                console.error('Translation error:', e);
                throw e;
            }
        }

        async function translateHtml(htmlStr, fromLang = 'ar', toLang = 'en') {
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = htmlStr;

            const textNodes = [];
            function findTextNodes(node) {
                if (node.nodeType === Node.TEXT_NODE) {
                    if (node.nodeValue.trim() !== '') {
                        textNodes.push(node);
                    }
                } else {
                    for (let child of node.childNodes) {
                        findTextNodes(child);
                    }
                }
            }
            findTextNodes(tempDiv);

            for (let node of textNodes) {
                try {
                    const translated = await translateText(node.nodeValue, fromLang, toLang);
                    node.nodeValue = translated;
                } catch (err) {
                    console.error('Failed to translate node:', node.nodeValue, err);
                }
            }

            return tempDiv.innerHTML;
        }

        $(document).on('click', '.translate-btn', async function() {
            const btn = $(this);
            const fromSelector = btn.data('from');
            const toSelector = btn.data('to');
            const fromLang = btn.data('from-lang') || 'ar';
            const toLang = btn.data('to-lang') || 'en';
            const isEditor = btn.data('type') === 'editor';
            const isSummernote = $(fromSelector).next().hasClass('note-editor');
            
            let sourceText = '';
            if (isEditor) {
                const editorInstance = (window.editors || {})[fromSelector];
                if (editorInstance) {
                    sourceText = editorInstance.getData();
                }
            } else if (isSummernote) {
                sourceText = $(fromSelector).summernote('code');
            } else {
                sourceText = $(fromSelector).val();
            }

            if (!sourceText || sourceText.trim() === '') {
                if (window.toastr) {
                    toastr.warning('{{ __("Please enter text first") }}');
                } else {
                    alert('{{ __("Please enter text first") }}');
                }
                return;
            }

            const originalHtml = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> {{ __("Translating...") }}');

            try {
                let translated = '';
                if (isEditor) {
                    translated = await translateHtml(sourceText, fromLang, toLang);
                    const targetEditorInstance = (window.editors || {})[toSelector];
                    if (targetEditorInstance) {
                        targetEditorInstance.setData(translated);
                    }
                } else if (isSummernote) {
                    translated = await translateHtml(sourceText, fromLang, toLang);
                    $(toSelector).summernote('code', translated);
                    $(toSelector).val(translated);
                } else {
                    translated = await translateText(sourceText, fromLang, toLang);
                    $(toSelector).val(translated);
                }
                if (window.toastr) {
                    toastr.success('{{ __("Translated successfully") }}');
                }
            } catch (error) {
                if (window.toastr) {
                    toastr.error('{{ __("Translation failed") }}');
                } else {
                    alert('{{ __("Translation failed") }}');
                }
            } finally {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    })();
</script>

<script>
    $(document).ready(function() {
        let globalSearchTimeout;
        const dropdown = $('#globalSearchDropdown');

        $('#header_quick_search').on('keyup input', function(e) {
            let val = $(this).val().trim();
            
            // 1. Keep local search in sync (if present on page)
            let localSearch = $('#filter_search');
            if (localSearch.length) {
                localSearch.val($(this).val());
                if (e.key === 'Enter') {
                    let pressEvent = $.Event('keypress', { which: 13 });
                    localSearch.trigger(pressEvent);
                    
                    let filterBtn = $('#btn-filter');
                    if (filterBtn.length) {
                        filterBtn.trigger('click');
                    } else if (typeof window.fetchUsers === 'function') {
                        window.fetchUsers(1);
                    } else if (typeof window.fetchFaqs === 'function') {
                        window.fetchFaqs(1);
                    } else if (typeof window.fetchBids === 'function') {
                        window.fetchBids(1);
                    }
                } else {
                    // Let the page do its local filtering if needed
                    localSearch.trigger('input').trigger('keyup');
                }
            }

            // 2. Perform global search for autocomplete dropdown suggestions
            if (val.length < 2) {
                dropdown.hide().empty();
                return;
            }

            clearTimeout(globalSearchTimeout);
            globalSearchTimeout = setTimeout(function() {
                $.ajax({
                    url: "{{ route('admin.global-search') }}",
                    data: { q: val },
                    success: function(res) {
                        dropdown.empty();
                        if (res.results && res.results.length > 0) {
                            res.results.forEach(category => {
                                dropdown.append(`<div class="global-search-category">${category.category}</div>`);
                                category.items.forEach(item => {
                                    dropdown.append(`
                                        <a href="${item.url}" class="global-search-item">
                                            <i class="fa-solid ${item.icon}"></i>
                                            <div class="global-search-item-info">
                                                <span class="global-search-item-title">${item.title}</span>
                                                <span class="global-search-item-subtitle">${item.subtitle}</span>
                                            </div>
                                        </a>
                                    `);
                                });
                            });
                            dropdown.show();
                        } else {
                            let emptyMsg = $('html').attr('dir') === 'rtl' ? 'لا توجد نتائج مطابقة' : 'No matching results found';
                            dropdown.html(`<div class="p-3 text-center text-muted small">${emptyMsg}</div>`).show();
                        }
                    },
                    error: function() {
                        dropdown.hide().empty();
                    }
                });
            }, 300);
        });

        // Close suggestions dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-box').length) {
                dropdown.hide();
            }
        });

        // Show suggestions dropdown again if input is refocused with valid text
        $('#header_quick_search').on('focus', function() {
            if ($(this).val().trim().length >= 2) {
                dropdown.show();
            }
        });

        // Initialize header search input value from local page search (if pre-filled)
        let initialSearchVal = $('#filter_search').val();
        if (initialSearchVal) {
            $('#header_quick_search').val(initialSearchVal);
        }
    });
</script>
