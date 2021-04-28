//VIDEO
document.addEventListener('DOMContentLoaded', function (event) {
    document.getElementById('add-another-collection-widget').addEventListener('click', function () {
        let parent = document.getElementById('trick_edit_videos');
        const widgetsCounter = document.getElementById('widgets-counter');
        let index = widgetsCounter.value;
        let template = parent.dataset.prototype.replace(/__name__/g, index);
        parent.insertAdjacentHTML('beforeend', template);
        widgetsCounter.value = parseInt(index) + 1;
        handleDeleteBtn();
    });
})

function handleDeleteBtn()
{
    let buttons = document.querySelectorAll('button[data-action="delete"]');
    for (let i = 0; i < buttons.length; i++) {
        buttons[i].addEventListener('click', function () {
            const target = this.dataset.target;
            document.getElementById(target).remove();
        })
    }
}

function updateWidgetsCounter()
{
    const elements = document.querySelectorAll('#trick_edit_videos > div');
    const widgetsCounter = document.getElementById('widgets-counter');
    widgetsCounter.value = elements.length;
}

updateWidgetsCounter();

handleDeleteBtn();