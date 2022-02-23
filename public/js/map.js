document.getElementById('gta-map').addEventListener('click', (event) => {
    document.getElementById('x-value').value = event.offsetX;
    document.getElementById('y-value').value = event.offsetY;

    document.getElementById('map-save-button').click();
});