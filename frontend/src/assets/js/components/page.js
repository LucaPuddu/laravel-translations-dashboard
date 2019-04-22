const axios = require('axios');
const errorHelper = require('./../helpers/errorHelper.js');

$(document).ready(() => {
    const elementsContainer = $('.page-elements');
    const addNewModal = $('#add-new');
    const addNewForm = addNewModal.find('form').first();
    const addNewAction = addNewForm.attr('action');
    const itemInput = addNewForm.find('[name="item"]').first();
    const pageInput = addNewForm.find('[name="group"]').first();
    const addSuccess = $('.element-new');
    const deleteSuccess = $('.element-deleted');
    const saveError = $('.save-error');
    const saveAction = $('[data-save-action]').data('save-action');
    let editors = {};
    const isPellActive = window.pell;
    const canManagePages = $('[name="manage_pages"]').val() === '1';
    const inputs = $('[data-content]');
    const pellEditors = $('.pell-content');
    inputs.change(function(){
        $(this).addClass('dirty');
    });

    let deleteElement;
    const deleteModal = $('#delete');
    let page = null;

    /* Init pell editor */
    function initPellEditor(element) {
        return window.pell.init({
            element: element,
            onChange: (html) => {
                $(element).find('.pell-content').addClass('dirty');
            },
            defaultParagraphSeparator: 'p',
            styleWithCSS: true,
            actions: [
                'bold',
                'italic',
                'underline',
                'olist',
                'ulist',
                'link'
            ]
        });
    }

    /* Set pages to add new element to */
    function setPageToAddElementTo(event) {
        pageInput.val($(event.target).parents('.page-elements').data('page'));
    }

    /* Submit new element */
    function submitNew(e) {
        const toDisable = addNewModal.find('[data-disable-onloading]');

        // Disable when sending request
        toDisable.prop('disabled', true);

        const inputs = {
            group: pageInput,
            item: itemInput
        };

        axios.post(addNewAction, {
            item: itemInput.val(),
            group: pageInput.val()
        })
            .then((res) => {
                addNewModal.modal('hide');
                addSuccess.removeClass('d-none');
                deleteSuccess.addClass('d-none');

                // Add div
                let newEl = `<div class="row mar-bottom-20 align-items-stretch" data-item="${itemInput.val()}">
                                    <div class="col-2">
                                        <span class="pre h6">${itemInput.val()}</span>
                                    </div>
                                    <div class="col-4">
                                        <div class="form-control origin-text">
                                        </div>
                                    </div>
                                    <div class="col-4">`;

                if (isPellActive) {
                    newEl += `<div data-pell>
                                            <span data-content></span>
                                        </div>`;
                } else {
                    newEl += `<textarea class="form-control" data-content></textarea>`;
                }

                newEl +=
                    `<div class="loader"></div>
                                    </div>
                                    <div class="col-2 gap-3">
                                        <div class="d-inline-block">
                                            <button class="btn btn-primary" type="button" data-save-item="${itemInput.val()}">Save</button>
                                        </div>`;

                if (canManagePages) {
                    newEl += `<div class="d-inline-block">
                                            <button type="button" data-delete-item="${itemInput.val()}" class="btn btn-danger"
                                                    data-toggle="modal" data-target="#delete">Delete</button>
                                        </div>`;
                }

                newEl += `</div></div>`;

                elementsContainer.append($(newEl));

                // Init editor on new element
                if (isPellActive) {
                    newEl = elementsContainer.children().last().find('[data-pell]').first().get(0);
                    editors[itemInput.val()] = initPellEditor(newEl);
                }
            })
            .catch((err) => {
                errorHelper.default(err.response.data, inputs);
            }).then(() => {
            toDisable.prop('disabled', false);
        });

        e.preventDefault();
    }

    /* Set element to be deleted */
    function setElementToDelete(event) {
        deleteElement = $(event.target).data('delete-item');
        page = $(event.target).parents('.page-elements').data('page');
    }

    /* Send delete request */
    function sendDelete(event) {
        const action = deleteModal.find('[data-action]').data('action');

        // Send request
        axios.post(action, {
            group: page,
            item: deleteElement
        })
            .then((res) => {
                deleteModal.modal('hide');
                deleteSuccess.removeClass('d-none');
                addSuccess.addClass('d-none');

                $(`[data-page="${page}"]`).find(`[data-item="${deleteElement}"]`).remove();
            })
            .catch((err) => {
                errorHelper.default(err.response.data, inputs);
            })
    }

    /* Send save request */
    function saveElement(event) {
        const item = $(event.target).data('save-item');
        const parentPageElements = $(event.target).parents('.page-elements');
        let page = parentPageElements.data('page');
        const parentRow = parentPageElements.find(`[data-item="${item}"]`);
        let lang = $('select[name=destination]').val();
        let value;
        let input;
        if (isPellActive) {
            input = editors[page][item];
            value = input.content.innerHTML;

            input = $(input).find('.dirty');
        } else {
            input = parentRow.find('[data-content]');
            value = input.val();
        }
        const loader = parentRow.find('.loader');
        loader.show();

        if (isPellActive) {
            if ((value.match(/<p>/g) || []).length === 1) {
                value = value.replace('<p>', '').replace('</p>', '');
            }
        }

        // Send request
        axios.post(saveAction, {
            group: page,
            item: item,
            lang: lang,
            text: value
        })
            .then((res) => {
                input.removeClass('dirty');
            })
            .catch((err) => {
                deleteSuccess.addClass('d-none');
                addSuccess.addClass('d-none');
                saveError.removeClass('d-none');
            })
            .then(() => {
                loader.hide();
            })
    }

    // Init Editors
    if (isPellActive) {
        $('[data-pell]').each((i, el) => {
            const contentEl = $(el).find('[data-content]').first();
            const content = contentEl.text();
            contentEl.remove();

            const editor = initPellEditor(el);

            editor.content.innerHTML = content;

            const page = $(el).closest('[data-page]').data('page');
            const item = $(el).closest('[data-item]').data('item');

            if (!editors[page]) {
                editors[page] = {};
            }

            // Save reference to this editor
            editors[page][item] = editor;
        });
    }

    $('#add-new-btn').click(setPageToAddElementTo);

    $('[data-new-confirm]').click(submitNew);
    addNewForm.on('submit', submitNew);

    elementsContainer.on('click', '[data-delete-item]', setElementToDelete);
    $('[data-delete-confirm]').click(sendDelete);

    elementsContainer.on('click', '[data-save-item]', saveElement);
});