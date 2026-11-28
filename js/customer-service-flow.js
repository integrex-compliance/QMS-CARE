(function ($) {
    'use strict';
    $(function () {
        var $root = $('#customerServiceMilestones');
        if (!$root.length) return;
        var saved = $('#tosSelectedTags .tos-tag-chip').map(function () { return $(this).attr('data-service'); }).get();
        var busy = false;
        function notify(message, type) { window.showToast($('<div>').text(message).html(), type); }
        function decorate() {
            // Use the service's theme, with certification-grade colors for ZED.
            $('[data-flow-name], .tos-tag-chip').each(function () {
                var name = $(this).attr('data-flow-name') || $(this).attr('data-service');
                if (!name) return;
                var theme = window.customerServiceTheme(name, $(this).attr('data-flow-stage'));
                $(this).attr('data-flow-theme', theme.key);
                this.style.setProperty('--flow-color', theme.color);
                this.style.setProperty('--flow-soft', theme.soft);
                this.style.setProperty('--flow-ink', theme.ink);
            });
        }
        function updateGroupControls() {
            $root.find('.service-flow-group').each(function () {
                var $group = $(this);
                $group.toggle(saved.indexOf($group.attr('data-flow-name')) !== -1);
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
        function refreshStageChoices($existing, $incoming) {
            if (!$existing.length || !$incoming.length) return;
            $existing.empty().append($incoming.children().clone()).prop('disabled', $incoming.prop('disabled'));
            $existing.attr('data-id', $incoming.attr('data-id'));
        }
        function refreshSections() {
            return $.get('customerAccountDetails.php', {customerid: $root.attr('data-customer')}).then(function (html) {
                var $page = $('<div>').append($.parseHTML(html, document, false));
                var $fresh = $page.find('#customerServiceMilestones');
                if (!$fresh.length) throw new Error('Page refresh failed');
                // Keep existing editors intact, including any unsaved installment input.
                $root.find('.service-flow-group').hide();
                $fresh.find('.service-flow-group').each(function () {
                    var $incoming = $(this), name = $incoming.attr('data-flow-name');
                    var $group = $root.find('.service-flow-group').filter(function () { return $(this).attr('data-flow-name') === name; });
                    if (!$group.length) $group = $incoming;
                    else {
                        var $dates = $group.find('.service-flow-dates'), $newDates = $incoming.find('.service-flow-dates');
                        if ($dates.attr('data-flow-id') !== $newDates.attr('data-flow-id')) $dates.replaceWith($newDates);
                        refreshStageChoices($group.find('.flow-current-stage'), $incoming.find('.flow-current-stage'));
                        refreshStageChoices($group.find('select.flow-milestone'), $incoming.find('select.flow-milestone'));
                    }
                    $root.append($group.show());
                });
                updateGroupControls();
                $root.find('.service-flow-empty').toggle(!saved.length);
                $page.find('.servicebox[data-flow-name]').each(function () {
                    var $incoming = $(this), name = $incoming.attr('data-flow-name'), id = $incoming.attr('data-service-id');
                    if (!name) return;
                    var $existing = $('.servicebox-bunch .servicebox').filter(function () { return $(this).attr('data-service-id') === id; });
                    if ($existing.length) {
                        $existing.attr('data-flow-active', $incoming.attr('data-flow-active'));
                        refreshStageChoices($existing.find('.flow-current-stage'), $incoming.find('.flow-current-stage'));
                    }
                    // Server order groups all stages of each service, including newly added ones.
                    $('.servicebox-bunch').append($existing.length ? $existing : this);
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
            $('.meeting-cancel-confirm-message').text('Delete this stage of ' + name + '? Other stages and saved history will be retained.');
            $('.delete-confirm-ok').off('click').on('click.flowDelete', function () {
                closeDelete();
                if (busy) return;
                busy = true;
                updateAddControls();
                $('#tosMultiselectWrapper').addClass('flow-saving').attr('aria-busy', 'true');
                $('.flow-delete-service').prop('disabled', true);
                function finishDelete() { $('.flow-delete-service').prop('disabled', false); unlock(); }
                request({action: 'delete', id: $button.attr('data-flow-id')}).done(function (res) {
                    if (!res || res.status !== 'success' || !Array.isArray(res.selected)) {
                        notify('Could not delete service. Refresh and retry.', 'error'); finishDelete(); return;
                    }
                    saved = res.selected;
                    restoreSelection();
                    $('.servicebox[data-service-id]').filter(function () { return $(this).attr('data-service-id') === String(res.service_id); }).attr('data-flow-active', '0');
                    updateGroupControls();
                    if (!$root.find('.service-flow-empty').length) $root.append('<p class="service-flow-empty">Select a service to create its section and individual dates.</p>');
                    $root.find('.service-flow-empty').toggle(!saved.length);
                    notify(res.service_name + ' stage deleted. History retained.', 'success');
                    refreshSections().fail(function () {
                        notify('Stage deleted. Reload this page to refresh the service dates.', 'error');
                    }).always(finishDelete);
                }).fail(function (xhr) {
                    notify((xhr.responseJSON || {}).error || 'Could not delete service. Please retry.', 'error');
                    finishDelete();
                });
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
            var $select = $(this), currentStage = $select.attr('data-current-stage') || '';
            if (!$select.val() || $select.val() === currentStage) return;
            if (busy) { $select.val(currentStage); notify('Please wait for the current save to finish.', 'info'); return; }
            busy = true;
            updateAddControls();
            $select.prop('disabled', true).attr('aria-busy', 'true');
            function finishStage() {
                var hasStages = $select.find('option:not(:disabled)').filter(function () { return this.value !== ''; }).length > 0;
                $select.val(currentStage).prop('disabled', !hasStages || $root.attr('data-readonly') === '1').attr('aria-busy', 'false');
                unlock();
            }
            request({action: 'stage', id: $select.attr('data-id'), stage: $select.val()}).done(function (res) {
                if (!res || res.status !== 'success') {
                    notify('Stage could not be added. Please retry.', 'error'); finishStage(); return;
                }
                refreshSections().done(function () {
                    notify($select.closest('[data-flow-name]').attr('data-flow-name') + ' — ' + res.stage + ' is ready. Existing stages retained.', 'success');
                }).fail(function () {
                    notify('Stage saved, but sections could not refresh. Reload this page to view it.', 'error');
                }).always(finishStage);
            }).fail(function (xhr) {
                notify((xhr.responseJSON || {}).error || 'Stage could not be added. Please retry.', 'error');
                finishStage();
            });
        });
        $(document).on('focus', '.flow-milestone:not([readonly])', function () {
            if ($(this).attr('data-column') !== 'next_service_type' && !$(this).hasClass('hasDatepicker')) $(this).datepicker({dateFormat: 'dd.mm.yy', changeMonth: true, changeYear: true, yearRange: '-100:+100'});
        });
        $(document).on('change', '.flow-milestone:not([readonly])', function () {
            var $input = $(this), id = $input.attr('data-id'), column = $input.attr('data-column'), value = $input.val();
            var $copies = $('.flow-milestone').filter(function () { return $(this).attr('data-id') === id && $(this).attr('data-column') === column; });
            $copies.prop('disabled', true).attr('aria-invalid', 'false');
            request({action: 'dates', id: id, column: column, value: value}).done(function () {
                $copies.val(value);
                notify('Service date / stage saved.', 'success');
            }).fail(function (xhr) {
                $input.attr('aria-invalid', 'true');
                notify((xhr.responseJSON || {}).error || 'Could not save. Please retry.', 'error');
            }).always(function () { $copies.prop('disabled', false); });
        });
        decorate();
        updateAddControls();
        if ($root.attr('data-readonly') !== '1' && saved.some(function (name) {
            return !$root.find('.service-flow-group').filter(function () { return $(this).attr('data-flow-name') === name; }).length;
        })) window.saveCustomerServiceSelection(saved, true);
    });
})(jQuery);
