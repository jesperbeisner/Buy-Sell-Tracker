const customers = document.getElementById('customer-search');
const customersTableBody = document.getElementById('customers-table-body');

customers.addEventListener('input', () => {
  const searchValue = customers.value.toLowerCase();
  const customersTableRows = customersTableBody.querySelectorAll('tr');

  customersTableRows.forEach((row) => {
    const customerNameData = row.querySelector('.customer-name');
    const dataText = customerNameData.innerText.toLowerCase();

    if (dataText.includes(searchValue)) {
      row.classList.remove('d-none');
    } else {
      row.classList.add('d-none');
    }
  });
});

const deleteIcons = document.querySelectorAll('.delete-icon');
deleteIcons.forEach((icon) => {
  icon.addEventListener('click', () => {
    const deleteUrl = icon.dataset.customerDeleteUrl;
    const customerRowId = icon.dataset.customerRow;

    addSpinner(icon);

    fetch(deleteUrl, {
      method: 'DELETE'
    }).then(response => {
      if (response.ok) {
        document.getElementById(customerRowId).remove();
      } else {
        removeSpinner(icon);
        alert('Fehler! Der Eintrag konnte nicht gelöscht werden.');
      }
    });
  })
});

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