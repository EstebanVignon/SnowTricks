document.addEventListener('DOMContentLoaded', function (event) {
    document.getElementById('add-another-collection-widget-picture').addEventListener('click', function () {
        let pictureParent = document.getElementById('trick_create_pictures');
        const picturewidgetsCounter = document.getElementById('picture-widgets-counter')
        let index = picturewidgetsCounter.value;
        let template = pictureParent.dataset.prototype.replace(/__name__/g, index);
        pictureParent.insertAdjacentHTML('beforeend', template);
        picturewidgetsCounter.value = parseInt(index) + 1;
        pictureHandleDeleteBtn();
    });
});

function pictureHandleDeleteBtn() {
    let buttons = document.querySelectorAll('button[data-action="delete-picture"]');
    for (let i = 0; i < buttons.length; i++) {
        buttons[i].addEventListener('click', function () {
            const target = this.dataset.target;
            document.getElementById(target).remove();
        })
    }
}
function updatePictureWidgetsCounter() {
    const elements = document.querySelectorAll('#trick_create_videos > div');
    const widgetsCounter = document.getElementById('picture-widgets-counter');
    widgetsCounter.value = elements.length;
}

updatePictureWidgetsCounter();

pictureHandleDeleteBtn();