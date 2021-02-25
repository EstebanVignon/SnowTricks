if (!('theme' in localStorage)) {
    localStorage.theme = 'light';
} else if (localStorage.theme === 'dark') {
    document.documentElement.classList.add('dark');
} else if (localStorage.theme === 'light') {
    document.documentElement.classList.remove('dark');
}

// BTN TOGGLER

console.log(localStorage.theme);

let toggleBtn = document.getElementById('toggleBtn');

toggleBtn.addEventListener('click', function () {
    if (localStorage.theme === 'light') {
        localStorage.theme = 'dark';
        document.documentElement.classList.add('dark');
    } else {
        localStorage.theme = 'light';
        document.documentElement.classList.remove('dark');
    }
});
