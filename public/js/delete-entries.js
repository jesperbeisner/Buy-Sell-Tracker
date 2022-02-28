document.querySelectorAll('.delete-icon').forEach((element) => {
    element.addEventListener('click', () => {
        const deleteUrl = element.dataset.deleteUrl;
        const rowId = element.dataset.rowId;
        const entryType = element.dataset.entryType;

        addSpinner(element);

        fetch(deleteUrl, {
            method: 'DELETE'
        }).then(response => {
            if (response.ok) {
                document.getElementById(rowId).remove();
                checkTableLength(entryType);
            } else {
                removeSpinner(element);
                alert('Fehler! Der Eintrag konnte nicht gelöscht werden.');
            }
        });
    });
});

const checkTableLength = function (entryType) {
    const tableBody = document.getElementById(entryType + 's-table-body');
    const tableRows = tableBody.querySelectorAll('tr');
    if (tableRows.length === 0) {
        document.getElementById(entryType + 's-table').remove();
        const noEntryText = document.getElementById('no-' + entryType + 's-text');
        noEntryText.classList.remove('d-none')
    }
}

const addSpinner = function (element) {
    element.classList.remove('fa-trash');
    element.classList.remove('text-danger');
    element.classList.add('fa-spinner');
    element.classList.add('fa-spin');
}

const removeSpinner = function (element) {
    element.classList.remove('fa-spinner');
    element.classList.remove('fa-spin');
    element.classList.add('fa-trash');
    element.classList.add('text-danger');
}