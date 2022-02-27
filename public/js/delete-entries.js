document.querySelectorAll('.delete-icon').forEach((element) => {
    element.addEventListener('click', () => {
        const deleteUrl = element.dataset.deleteUrl;
        const rowId = element.dataset.rowId;

        addSpinner(element)

        fetch(deleteUrl, {
            method: 'DELETE'
        }).then(response => {
            if (response.ok) {
                document.getElementById(rowId).remove();
            } else {
                removeSpinner(element)
                alert('Fehler! Der Eintrag konnte nicht gelöscht werden.')
            }
        })
    });
});

function addSpinner(element) {
    element.classList.remove('fa-trash')
    element.classList.remove('text-danger')
    element.classList.add('fa-spinner')
    element.classList.add('fa-spin')
}

function removeSpinner(element) {
    element.classList.remove('fa-spinner')
    element.classList.remove('fa-spin')
    element.classList.add('fa-trash')
    element.classList.add('text-danger')
}