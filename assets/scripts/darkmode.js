const toggleBtn = document.getElementById('toggleBtn');
const html = document.getElementsByTagName('html')[0];
toggleBtn.addEventListener('click', function () {
    html.classList.toggle("dark");
});