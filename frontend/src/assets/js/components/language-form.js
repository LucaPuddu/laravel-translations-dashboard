$(document).ready(() => {
    const languageForm = $('#language-form');

    // Change language
    languageForm.find('select').change(() => {
        languageForm.submit();
    });
});