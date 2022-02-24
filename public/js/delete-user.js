document.querySelectorAll('.delete-icon').forEach((icon) => {
    icon.addEventListener('click', () => {
        document.getElementById('user-id-input').value = icon.dataset.userId;
        document.getElementById('submit-delete-form').click();
    });
});