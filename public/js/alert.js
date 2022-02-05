const AlertContainer = document.getElementById('alert-container');
if (AlertContainer !== null) {
    setTimeout(() => {
        AlertContainer.classList.add('d-none')
    }, 3500);
}