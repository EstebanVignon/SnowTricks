//Profil Dropdown

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
