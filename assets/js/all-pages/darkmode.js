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

if (localStorage.theme === "light") {
    toggleBtn.innerText = "Light mode";
} else if (localStorage.theme === 'dark') {
    toggleBtn.innerText = "Dark mode";
}

toggleBtn.addEventListener('click', function () {
    if (localStorage.theme === 'light') {
        localStorage.theme = 'dark';
        document.documentElement.classList.add('dark');
        toggleBtn.innerText = "Dark mode";
    } else {
        localStorage.theme = 'light';
        document.documentElement.classList.remove('dark');
        toggleBtn.innerText = "light mode";
    }
});
