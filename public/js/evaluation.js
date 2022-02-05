document.querySelectorAll(".submit-form-icon").forEach((icon) => {
    icon.addEventListener('click', () => {
        document.querySelector('#form-' + icon.dataset.productId).submit()
    });
});