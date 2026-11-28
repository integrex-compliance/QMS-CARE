(function ($) {
    'use strict';
    var config = window.QMS_HRMS;
    var grants = config.grants || {};
    var viewer = '';
    var dirty = false;
    var saving = false;
    var boxes = $('#hrmsAccessForm input[name="target_ids[]"]');
    function feedback(id, message, error) {
        $(id).text(message).toggleClass('is-error', !!error).prop('hidden', false);
    }
    function isAdmin() { return $('#hrmsViewer option:selected').attr('data-main-admin') === '1'; }
    function updateCount() {
        $('#hrmsSelectedCount').text(boxes.filter(':checked').length + ' selected');
    }
    function renderAccess() {
        var admin = isAdmin();
        var allowed = grants[viewer] || [];
        boxes.each(function () {
            var own = this.value === viewer;
            this.checked = !!viewer && (admin || own || allowed.indexOf(Number(this.value)) !== -1);
            this.disabled = !viewer || admin || own || saving;
            $(this).closest('.hrms-user-row').find('.hrms-user-state').text(own ? 'Own dashboard' : (admin ? 'Always allowed' : ''));
        });
        $('#hrmsSaveAccess, #hrmsSelectAll, #hrmsClearAll').prop('disabled', !viewer || admin || saving);
        $('#hrmsViewerNote').text(!viewer ? 'Select a team member to review their current access.' : admin ? 'Main Admin has permanent access to every dashboard. This access cannot be restricted.' : 'This member can view their own dashboard and the additional dashboards selected here.');
        updateCount();
    }
    function save(data, button, feedbackId, done) {
        var label = button.text();
        button.prop('disabled', true).text('Saving…');
        $(feedbackId).prop('hidden', true);
        data.csrf_token = config.csrf;
        $.ajax({url: 'hrms_action.php', type: 'POST', dataType: 'json', data: data})
            .done(function (response) {
                if (response.success) {
                    done();
                    feedback(feedbackId, response.message, false);
                } else feedback(feedbackId, response.error || 'Could not save settings.', true);
            })
            .fail(function (xhr) { feedback(feedbackId, xhr.responseJSON && xhr.responseJSON.error || 'Could not save settings. Please try again.', true); })
            .always(function () {
                button.prop('disabled', false).text(label);
                if (feedbackId === '#accessFeedback') {
                    saving = false;
                    $('#hrmsViewer').prop('disabled', false);
                    boxes.each(function () { this.disabled = this.value === viewer; });
                    $('#hrmsSelectAll, #hrmsClearAll').prop('disabled', false);
                }
            });
    }
    $('#hrmsWeekoffForm').on('submit', function (event) {
        event.preventDefault();
        save({action: 'save_weekoff', weekoff_day: $(this).find(':checked').val()}, $(this).find('button'), '#weekoffFeedback', function () {});
    });
    $('#hrmsViewer').on('change', function () {
        if (dirty && !window.confirm('Discard unsaved dashboard access changes?')) {
            $(this).val(viewer);
            return;
        }
        viewer = this.value;
        dirty = false;
        $('#accessFeedback').prop('hidden', true);
        renderAccess();
    });
    boxes.on('change', function () { dirty = true; updateCount(); });
    $('#hrmsSelectAll, #hrmsClearAll').on('click', function () {
        boxes.filter(':enabled').prop('checked', this.id === 'hrmsSelectAll');
        dirty = true;
        updateCount();
    });
    $('#hrmsUserSearch').on('input', function () {
        var query = this.value.toLowerCase().trim();
        var visible = 0;
        $('.hrms-user-row').each(function () {
            var match = $(this).attr('data-search').indexOf(query) !== -1;
            $(this).prop('hidden', !match);
            if (match) visible++;
        });
        $('#hrmsNoResults').prop('hidden', visible > 0);
    });
    $('#hrmsAccessForm').on('submit', function (event) {
        event.preventDefault();
        if (!viewer || isAdmin() || saving) return;
        var selected = boxes.filter(':checked').map(function () { return Number(this.value); }).get();
        saving = true;
        boxes.prop('disabled', true);
        $('#hrmsViewer, #hrmsSelectAll, #hrmsClearAll').prop('disabled', true);
        save({action: 'save_access', viewer_id: viewer, target_ids: selected}, $('#hrmsSaveAccess'), '#accessFeedback', function () {
            grants[viewer] = selected;
            dirty = false;
        });
    });
    $(window).on('beforeunload', function (event) {
        if (dirty) { event.preventDefault(); event.originalEvent.returnValue = ''; return ''; }
    });
    renderAccess();
})(jQuery);
