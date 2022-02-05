const SuccessAlertContainer = document.getElementById('success-alert-container');
if (SuccessAlertContainer !== null) {
    setTimeout(() => {
        SuccessAlertContainer.classList.add('d-none')
    }, 3500);
}

const ErrorAlertContainer = document.getElementById('error-alert-container');
if (ErrorAlertContainer !== null) {
    setTimeout(() => {
        ErrorAlertContainer.classList.add('d-none')
    }, 3500);
}