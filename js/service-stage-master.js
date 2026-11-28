(function ($) {
    'use strict';
    $(function () {
        var $editor = $('#serviceStageEditor'), typeId = '', busy = false, loaded = false, generation = 0;
        function attachButtons() {
            $('.operateServices > li').each(function () {
                var $row = $(this), $delete = $row.find('.deleteService');
                if (!$delete.length || $row.find('.manage-service-stages').length) return;
                var name = $row.clone().children().remove().end().text().trim();
                $('<button type="button" class="manage-service-stages">Add / Manage Stages</button>').attr({'data-id': $delete.attr('data-id'), 'data-service': name, 'aria-label': 'Manage stages for ' + name}).insertBefore($delete);
            });
        }
        function status(message, error) { $editor.find('.stage-editor-status').text(message).toggleClass('is-error', !!error); }
        function setBusy(value) {
            busy = value;
            $editor.find('input, .stage-editor-add, .stage-editor-save').prop('disabled', value);
            $editor.find('[data-dismiss="modal"]').prop('disabled', value);
            $editor.find('.stage-editor-save').prop('disabled', value || !loaded);
        }
        function request(action, stages) {
            return $.ajax({url: 'serviceTypeStagesAction.php', type: 'POST', dataType: 'json', data: {action: action, service_type_id: typeId, csrf: $editor.attr('data-csrf'), stages: JSON.stringify(stages || [])}});
        }
        function addOption(name, checked) {
            var $label = $('<label class="stage-editor-option">');
            $label.append($('<input type="checkbox">').val(name).prop('checked', checked), $('<span>').text(name));
            $editor.find('.stage-editor-options').append($label);
        }
        function render(data) {
            $editor.find('.stage-editor-service').text(data.service_name);
            $editor.find('.stage-editor-options').empty();
            data.stages.forEach(function (name) { addOption(name, data.selected.indexOf(name) !== -1); });
            var $catalog = $('.operateServiceStages').empty();
            data.catalog.forEach(function (stage) {
                var $row = $('<li class="list-group-item">').text(stage.stage);
                $row.append($('<a class="deleteServiceStage" style="float:right;cursor:pointer"><i class="fa fa-trash"></i></a>').attr('data-id', stage.id));
                $catalog.append($row);
            });
        }
        $(document).on('click', '.manage-service-stages', function () {
            if (busy) return;
            typeId = $(this).attr('data-id');
            loaded = false;
            var current = ++generation;
            $editor.find('.stage-editor-service').text($(this).attr('data-service'));
            $editor.find('.stage-editor-options').empty();
            $('#serviceStageNewName').val('');
            status('Loading stages...');
            setBusy(true);
            $editor.modal({backdrop: 'static', keyboard: false, show: true});
            request('load').done(function (data) {
                if (current !== generation) return;
                loaded = true;
                render(data);
                status(data.selected.length ? 'Tick or untick stages, then save.' : 'No stages assigned yet. Select or add stages below.');
            }).fail(function (xhr) { status((xhr.responseJSON || {}).error || 'Could not load stages. Close and retry.', true); })
              .always(function () { setBusy(false); });
        });
        $editor.on('click', '.stage-editor-add', function () {
            if (busy || !loaded) return;
            var name = $('#serviceStageNewName').val().trim();
            if (!name) { $('#serviceStageNewName').focus(); return; }
            var $existing = $editor.find('.stage-editor-options input').filter(function () { return this.value.toLowerCase() === name.toLowerCase(); });
            if ($existing.length) $existing.prop('checked', true);
            else addOption(name, true);
            $('#serviceStageNewName').val('').focus();
            status('Stage selected. Click Save stages to apply.');
        });
        $('#serviceStageNewName').on('keydown', function (event) { if (event.key === 'Enter') { event.preventDefault(); $editor.find('.stage-editor-add').click(); } });
        $editor.on('click', '.stage-editor-save', function () {
            if (busy || !loaded) return;
            if ($('#serviceStageNewName').val().trim()) $editor.find('.stage-editor-add').click();
            var selected = $editor.find('.stage-editor-options input:checked').map(function () { return this.value; }).get();
            setBusy(true); status('Saving stages...');
            request('save', selected).done(function (data) {
                render(data);
                status('Saved. Customer Account will use these ' + data.selected.length + ' stages.');
            }).fail(function (xhr) { status((xhr.responseJSON || {}).error || 'Could not save stages. Please retry.', true); })
              .always(function () { setBusy(false); });
        });
        $(document).ajaxSuccess(function (event, xhr, settings) { if (settings.url.indexOf('addNewServices.php') !== -1) attachButtons(); });
        attachButtons();
    });
})(jQuery);
