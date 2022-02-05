const DeleteIcons = document.querySelectorAll('.delete-icon');
DeleteIcons.forEach((icon) => {
    icon.addEventListener('click', () => {
        document.querySelector('#form-' + icon.dataset.entryId).submit();
    });
});