export default function displayErrors(errors, inputs) {
    // Get error messages and display them
    Object.keys(errors).forEach(function (key, index) {
        if (inputs[key]) {
            inputs[key].addClass('is-invalid');
            inputs[key].next('.invalid-feedback').text(errors[key].join('<br/>'));
        }
    });
}
