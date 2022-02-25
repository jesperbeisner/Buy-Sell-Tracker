document.querySelectorAll('.delete-icon').forEach((icon) => {
    icon.addEventListener('click', () => {
        document.getElementById('seller-id-input').value = icon.dataset.sellerId;
        document.getElementById('submit-input').click();
    });
});