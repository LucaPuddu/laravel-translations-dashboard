const axios = require('axios');
const select2 = require('select2');
const errorHelper = require('./../helpers/errorHelper.js');

$(document).ready(function () {
    function displayErrors(errors, inputs) {
        errorHelper.default(errors, inputs);
    }

    axios.create({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

    const saveAction = $('[data-action]').data('action');

    // Save logic
    $('[data-save-id]').click((e) => {
        const id = $(e.target).data('save-id');
        const form = $(`[data-lang-id="${id}"]`);
        const toDisable = form.find('[data-disable-onloading]');

        // Disable when sending request
        toDisable.prop('disabled', true);

        // Get inputs
        const inputs = {
            name: form.find('[name="name"]').first(),
            locale: form.find('[name="locale"]').first(),
            visible: form.find('[name="visible"]').first()
        };

        let data = {
            id: id,
            name: inputs.name.val(),
            locale: inputs.locale.val(),
            visible: inputs.visible.prop('checked')
        };

        // Send request
        axios.post(saveAction, data)
            .then((res) => {
                inputs.name.removeClass('is-invalid');
                inputs.locale.removeClass('is-invalid');
                inputs.visible.removeClass('is-invalid');
            })
            .catch((err) => {
                displayErrors(err.response.data, inputs);
            })
            .then(() => {
                toDisable.prop('disabled', false)
            })
    });

    // Delete logic
    let deleteId;
    const deleteModal = $('#delete');
    const confirmationWord = $('[name="confirmation_word"]').val();
    const confirmation = deleteModal.find('[name="delete_confirmation"]');
    // Show modal and set id to delete
    $('[data-delete-id]').click((e) => {
        deleteId = $(e.target).data('delete-id');
    });
    // Send delete request
    $('[data-delete-confirm]').click((e) => {
        const action = deleteModal.find('[data-action]').data('action');

        const inputs = {
            confirmation: confirmation
        };

        // Send request
        if (confirmation.val() === confirmationWord) {
            confirmation.removeClass('is-invalid');
            deleteModal.find('.invalid-feedback').text('');

            axios.post(action, {
                id: deleteId,
                confirmation: confirmation.val()
            })
                .then((res) => {
                    deleteModal.modal('hide');
                    $('.language-deleted').removeClass('d-none');
                    $(`[data-lang-id="${deleteId}"]`).remove();
                    confirmation.val('');
                })
                .catch((err) => {
                    displayErrors(err.response.data, inputs);
                })
        } else {
            confirmation.addClass('is-invalid');
            deleteModal.find('.invalid-feedback').text('Confirmation value doesn\'t match.');
        }
    });
    // Reset error on close
    deleteModal.find('[data-dismiss]').click(() => {
        confirmation.removeClass('is-invalid');
        confirmation.val('');
        deleteModal.find('.invalid-feedback').text('');
    });

    // New language logic
    const addNewModal = $('#add-new');
    const addNewForm = addNewModal.find('form').first();
    const addNewAction = addNewForm.attr('action');
    const nameInput = addNewForm.find('[name="name"]').first();
    const localeInput = addNewForm.find('[name="locale"]').first();
    $('[data-new-confirm]').click(() => {
        const inputs = {
            name: nameInput,
            locale: localeInput,
        };
        const data = addNewForm.serializeArray().reduce(function (obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});

        // Disable form when sending request
        const toDisable = addNewModal.find('[data-disable-onloading]');
        toDisable.prop('disabled', true);

        axios.post(addNewAction, data)
            .then((res) => {
                addNewModal.modal('hide');
                $('.language-new').removeClass('d-none');
            })
            .catch((err) => {
                displayErrors(err.response.data, inputs);
            })
            .then(()=>{
                toDisable.prop('disabled', false);
            });
    });

    // Select search
    const localesSelect = $('.select2');
    localesSelect.select2({
        theme: 'bootstrap',
        dropdownParent: addNewModal //prevent bug
    });
    localesSelect.on("select2:select", function (e) {
        const data = $(e.target).select2('data')[0];
        const locale = data.id;
        const name = data.text;

        localeInput.val(locale);
        nameInput.val(name);
    });
});