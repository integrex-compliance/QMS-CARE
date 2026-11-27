(function ($) {
    'use strict';
    $(function () {
        var $root = $('#customerServiceMilestones');
        if (!$root.length) return;
        var saved = $('#tosSelectedTags .tos-tag-chip').map(function () { return $(this).attr('data-service'); }).get();
        var busy = false;
        function notify(message, type) { window.showToast($('<div>').text(message).html(), type); }
        function decorate() {
            // Stable type identity, independent of selection order.
            $('[data-flow-name], .tos-tag-chip').each(function () {
                var name = $(this).attr('data-flow-name') || $(this).attr('data-service');
                if (!name) return;
                var hash = 0;
                for (var i = 0; i < name.length; i++) hash = ((hash * 31) + name.charCodeAt(i)) >>> 0;
                this.style.setProperty('--flow-color', 'hsl(' + (hash % 360) + ', 65%, 34%)');
            });
        }
        function restoreSelection() {
            var $tags = $('#tosSelectedTags').empty();
            saved.forEach(function (name) {
                var $chip = $('<span class="tos-tag-chip">').attr('data-service', name).append($('<span>').text(name));
                $tags.append($chip);
            });
            if (!saved.length) $tags.append('<span class="tos-placeholder">No services added yet.</span>');
            $('.customer-selected-services-count').text(saved.length).toggleClass('has-items', !!saved.length);
            updateAddControls();
            decorate();
        }
        function updateAddControls() {
            var $select = $('#tosAddServiceSelect');
            $select.find('option').each(function () {
                if (this.value) $(this).prop('disabled', saved.indexOf(this.value) !== -1);
            });
            if (saved.indexOf($select.val()) !== -1) $select.val('');
            var available = $select.find('option').filter(function () { return this.value && !this.disabled; }).length;
            $select.prop('disabled', busy || !available);
            $('#tosAddServiceButton').prop('disabled', busy || !$select.val() || !available);
            $('#tosAddHint').text(busy ? 'Saving service...' : (!available ? 'All available services have been added.' : 'Add one service at a time.'));
        }
        $(document).on('change', '#tosAddServiceSelect', updateAddControls);
        $(document).on('click', '#tosAddServiceButton', function () {
            var name = $('#tosAddServiceSelect').val();
            if (busy || !name || saved.indexOf(name) !== -1) return;
            window.saveCustomerServiceSelection(saved.concat([name]), false, name);
        });
        function request(data) {
            return $.ajax({url: 'customerServiceFlowAction.php', type: 'POST', dataType: 'json', data: $.extend({
                customer_id: $root.attr('data-customer'), csrf: $root.attr('data-csrf')
            }, data)});
        }
        function refreshSections() {
            return $.get('customerAccountDetails.php', {customerid: $root.attr('data-customer')}).then(function (html) {
                var $page = $('<div>').append($.parseHTML(html, document, false));
                var $fresh = $page.find('#customerServiceMilestones');
                if (!$fresh.length) throw new Error('Page refresh failed');
                // Keep existing editors intact, including any unsaved installment input.
                $fresh.find('.service-flow-dates').each(function () {
                    var name = $(this).attr('data-flow-name');
                    if (!$root.find('.service-flow-dates').filter(function () { return $(this).attr('data-flow-name') === name; }).length) $root.append(this);
                });
                $root.find('.service-flow-dates').each(function () { $(this).toggle(saved.indexOf($(this).attr('data-flow-name')) !== -1); });
                $root.find('.service-flow-empty').toggle(!saved.length);
                $page.find('.servicebox[data-flow-name]').each(function () {
                    var name = $(this).attr('data-flow-name');
                    if (!name) return;
                    var $existing = $('.servicebox-bunch .servicebox').filter(function () { return $(this).attr('data-flow-name') === name; });
                    if (!$existing.length) $('.servicebox-bunch').append(this);
                });
                $('.servicebox-bunch .servicebox[data-flow-name]').each(function () {
                    var name = $(this).attr('data-flow-name');
                    if (name) $(this).attr('data-flow-active', saved.indexOf(name) === -1 ? '0' : '1');
                });
                decorate();
            });
        }
        window.saveCustomerServiceSelection = function (selected, initial, addName) {
            if (busy) { restoreSelection(); return; }
            busy = true;
            updateAddControls();
            $('#tosMultiselectWrapper').addClass('flow-saving').attr('aria-busy', 'true');
            request(addName ? {action: 'add', service_name: addName} : {action: 'sync', selected: JSON.stringify(selected)}).done(function (res) {
                if (!res || res.status !== 'success' || !Array.isArray(res.selected)) {
                    restoreSelection(); notify('Unexpected save response. Refresh and retry.', 'error'); unlock(); return;
                }
                saved = res.selected;
                $('#tosAddServiceSelect').val('');
                restoreSelection();
                refreshSections().done(function () {
                    if (!initial) notify((addName || 'Service') + ' added successfully.', 'success');
                }).fail(function () { notify('Saved, but sections could not refresh. Reload this page to view them.', 'error'); }).always(unlock);
            }).fail(function (xhr) {
                restoreSelection();
                notify((xhr.responseJSON || {}).error || 'Save failed. Your previous selection has been restored.', 'error');
                unlock();
            });
        };
        function unlock() { busy = false; $('#tosMultiselectWrapper').removeClass('flow-saving').attr('aria-busy', 'false'); updateAddControls(); }
        $(document).on('click', '.flow-delete-service', function (e) {
            e.preventDefault(); e.stopPropagation();
            if (busy) { notify('Please wait for the current save to finish.', 'info'); return; }
            var $button = $(this), name = $button.closest('.servicebox').attr('data-flow-name');
            function closeDelete() {
                $('#delete-details').hide().removeClass('flow-delete-dialog').removeAttr('role aria-modal aria-label');
                $('.meeting-cancel-confirm-message').text('Are you sure that you want to delete details?');
                $('.delete-confirm-ok, .delete-cancel').off('click.flowDelete');
                $(document).off('keydown.flowDelete');
                $button.focus();
            }
            $('.meeting-cancel-confirm-message').text('Are you sure you want to delete it?');
            $('.delete-confirm-ok').off('click').on('click.flowDelete', function () {
                closeDelete();
                if (busy) return;
                busy = true;
                updateAddControls();
                $('#tosMultiselectWrapper').addClass('flow-saving').attr('aria-busy', 'true');
                $('.flow-delete-service').prop('disabled', true);
                request({action: 'delete', id: $button.attr('data-flow-id')}).done(function (res) {
                    if (!res || res.status !== 'success' || !Array.isArray(res.selected)) {
                        notify('Could not delete service. Refresh and retry.', 'error'); return;
                    }
                    saved = res.selected;
                    restoreSelection();
                    $('.servicebox[data-flow-name]').filter(function () { return $(this).attr('data-flow-name') === res.service_name; }).attr('data-flow-active', '0');
                    $root.find('.service-flow-dates').filter(function () { return $(this).attr('data-flow-name') === res.service_name; }).hide();
                    if (!$root.find('.service-flow-empty').length) $root.append('<p class="service-flow-empty">Select a service to create its section and individual dates.</p>');
                    $root.find('.service-flow-empty').toggle(!saved.length);
                    notify(res.service_name + ' deleted from active services. History retained.', 'success');
                }).fail(function (xhr) {
                    notify((xhr.responseJSON || {}).error || 'Could not delete service. Please retry.', 'error');
                }).always(function () { $('.flow-delete-service').prop('disabled', false); unlock(); });
            });
            $('.delete-cancel').off('click').on('click.flowDelete', function () {
                closeDelete();
            });
            $('#delete-details').addClass('flow-delete-dialog').attr({role: 'dialog', 'aria-modal': 'true', 'aria-label': 'Delete service'}).show().find('.delete-cancel').focus();
            $(document).on('keydown.flowDelete', function (event) {
                if (event.key === 'Escape') { event.preventDefault(); closeDelete(); }
                if (event.key === 'Tab') {
                    event.preventDefault();
                    var selector = $(document.activeElement).hasClass('delete-cancel') ? '.delete-confirm-ok' : '.delete-cancel';
                    $('#delete-details').find(selector).focus();
                }
            });
        });
        $(document).on('change', '.flow-current-stage', function () {
            var $select = $(this), previous = $select.attr('data-saved-stage');
            $select.prop('disabled', true).attr('aria-busy', 'true');
            request({action: 'stage', id: $select.attr('data-id'), stage: $select.val()}).done(function (res) {
                if (!res || res.status !== 'success') {
                    $select.val(previous); notify('Stage could not be saved. Please retry.', 'error'); return;
                }
                $select.attr('data-saved-stage', res.stage).val(res.stage);
                var sid = String(res.service_id);
                $('.service-type').filter(function () { return $(this).attr('data-row-id') === sid; }).val(res.stage);
                $('.service_title').filter(function () { return $(this).attr('data-row-id') === sid; }).find('.serviceStatus').text(res.stage);
                notify($select.closest('.service-flow-dates').attr('data-flow-name') + ' stage saved.', 'success');
            }).fail(function (xhr) {
                $select.val(previous);
                notify((xhr.responseJSON || {}).error || 'Stage could not be saved. Previous stage restored.', 'error');
            }).always(function () { $select.prop('disabled', false).attr('aria-busy', 'false'); });
        });
        $(document).on('focus', '.flow-milestone:not([readonly])', function () {
            if ($(this).attr('data-column') !== 'next_service_type' && !$(this).hasClass('hasDatepicker')) $(this).datepicker({dateFormat: 'dd.mm.yy', changeMonth: true, changeYear: true, yearRange: '-100:+100'});
        });
        $(document).on('change', '.flow-milestone:not([readonly])', function () {
            var $input = $(this);
            $input.prop('disabled', true).attr('aria-invalid', 'false');
            request({action: 'dates', id: $input.attr('data-id'), column: $input.attr('data-column'), value: $input.val()}).done(function () {
                notify('Service date / stage saved.', 'success');
            }).fail(function (xhr) {
                $input.attr('aria-invalid', 'true');
                notify((xhr.responseJSON || {}).error || 'Could not save. Please retry.', 'error');
            }).always(function () { $input.prop('disabled', false); });
        });
        decorate();
        updateAddControls();
        if ($root.attr('data-readonly') !== '1' && saved.some(function (name) {
            return !$root.find('.service-flow-dates').filter(function () { return $(this).attr('data-flow-name') === name; }).length;
        })) window.saveCustomerServiceSelection(saved, true);
    });
})(jQuery);
