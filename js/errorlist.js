document.addEventListener("DOMContentLoaded", function() {

    let errorClearButton = document.getElementById('clear-errors-button');
    errorClearButton.addEventListener("click", function() {
        clearErrors();
    });
});

function clearErrors() {

    let errorsWrap = document.getElementById('errors-wrap');
    errorsWrap.setAttribute('data-empty', 'true');
    let errors = document.getElementById('errors');
    errors.innerHTML = "";
}

function addError($message) {
    
    let errorsWrap = document.getElementById('errors-wrap');
    errorsWrap.setAttribute('data-empty', 'false');
    let errors = document.getElementById('errors');
    let newError = document.createElement('div');
    newError.classList.add('error');
    newError.innerHTML = sanitize($message);
    errors.appendChild(newError);
}