/**
 * BidderAjax - Unified AJAX Handler for the Bidder Admin Panel
 */
const BidderAjax = {
    /**
     * Initialize AJAX settings
     * @param {string} csrfToken 
     */
    init: function(csrfToken) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
    },

    /**
     * Generic request method
     */
    request: function(method, url, data = {}, options = {}) {
        let isFormData = data instanceof FormData;
        
        let defaultOptions = {
            url: url,
            type: method,
            data: data,
            processData: !isFormData,
            contentType: isFormData ? false : 'application/x-www-form-urlencoded; charset=UTF-8',
            success: function(response) {
                if (typeof options.onSuccess === 'function') {
                    options.onSuccess(response);
                }
            },
            error: function(xhr) {
                BidderAjax.handleError(xhr);
                if (typeof options.onError === 'function') {
                    options.onError(xhr);
                }
            },
            complete: function() {
                if (typeof options.onComplete === 'function') {
                    options.onComplete();
                }
            }
        };

        return $.ajax(defaultOptions);
    },

    get: function(url, data = {}, options = {}) {
        return this.request('GET', url, data, options);
    },

    post: function(url, data, options = {}) {
        return this.request('POST', url, data, options);
    },

    put: function(url, data, options = {}) {
        if (data instanceof FormData) {
            data.append('_method', 'PUT');
            return this.request('POST', url, data, options);
        }
        return this.request('PUT', url, data, options);
    },

    delete: function(url, data = {}, options = {}) {
        return this.request('DELETE', url, data, options);
    },

    /**
     * Centralized Error Handling
     */
    handleError: function(xhr) {
        if (xhr.status === 422) {
            let errors = xhr.responseJSON.errors;
            let errorMsg = '';
            Object.keys(errors).forEach(key => {
                errorMsg += errors[key][0] + '<br>';
            });
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في التحقق',
                    html: errorMsg
                });
            } else {
                alert('Validation Error:\n' + errorMsg.replace(/<br>/g, '\n'));
            }
        } else if (xhr.status === 401 || xhr.status === 403) {
            if (typeof Swal !== 'undefined') {
                Swal.fire('غير مصرح', 'ليس لديك صلاحية لهذا الإجراء.', 'error');
            } else {
                alert('Unauthorized Access');
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire('خطأ', 'حدث خطأ غير متوقع، يرجى المحاولة لاحقاً', 'error');
            } else {
                alert('An unexpected error occurred.');
            }
        }
    }
};
