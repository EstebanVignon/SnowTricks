//Profil Dropdown
const userMenu = document.getElementById('user-menu');
const userMenuDropdown = document.getElementById('user-menu-dropdown');

userMenu.addEventListener('click', function () {
    if (userMenuDropdown.classList.contains('invisible')) {
        userMenuDropdown.classList.remove('invisible');
    } else {
        userMenuDropdown.classList.add('invisible');
    }
});

//Main Menu
const mobileMenu = document.getElementById('mobile-menu');
const mobileMenuIcon = document.getElementById('mobile-menu-icon');
const mobileMenuIconSvgOpen = document.getElementById('mobile-menu-icon-svg-open');
const mobileMenuIconSvgClose = document.getElementById('mobile-menu-icon-svg-close');

mobileMenuIcon.addEventListener('click', function () {
    if (mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.remove('hidden');
        mobileMenuIconSvgOpen.classList.add('hidden');
        mobileMenuIconSvgClose.classList.remove('hidden');
    } else {
        mobileMenu.classList.add('hidden');
        mobileMenuIconSvgOpen.classList.remove('hidden');
        mobileMenuIconSvgClose.classList.add('hidden');
    }
});
