const axios = require('axios');
const errorHelper = require('./../helpers/errorHelper.js');

$(document).ready(function () {
    axios.create({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });

    // New page logic
    const addNewModal = $('#add-new');
    const addNewForm = addNewModal.find('form').first();
    const addNewAction = addNewForm.attr('action');
    const nameInput = addNewForm.find('[name="group"]').first();
    function submitNew(e){
        const toDisable = addNewModal.find('[data-disable-onloading]');

        // Disable when sending request
        toDisable.prop('disabled', true);

        const inputs = {
            group: nameInput
        };

        axios.post(addNewAction, {name: nameInput.val()})
            .then((res) => {
                addNewModal.modal('hide');
                $('.page-new').removeClass('d-none');
            })
            .catch((err) => {
                errorHelper.default(err.response.data, inputs);
            }).then(() => {
            toDisable.prop('disabled', false);
        });

        e.preventDefault();
    }
    $('[data-new-confirm]').click(submitNew);
    addNewForm.on('submit', submitNew);

    // Delete logic
    let deletePage;
    const deleteModal = $('#delete');
    const confirmationWord = $('[name="confirmation_word"]').val();
    const confirmation = deleteModal.find('[name="delete_confirmation"]');
    // Show modal and set id to delete
    $('[data-delete-page]').click((e) => {
        deletePage = $(e.target).data('delete-page');
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
                name: deletePage,
                confirmation: confirmation.val()
            })
                .then((res) => {
                    deleteModal.modal('hide');
                    $('.page-deleted').removeClass('d-none');
                    $(`[data-page-title="${deletePage}"]`).remove();
                    confirmation.val('');
                })
                .catch((err) => {
                    errorHelper.default(err.response.data, inputs);
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
});