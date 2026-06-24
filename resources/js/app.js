import '@fortawesome/fontawesome-free/css/all.min.css';

document.querySelector('#hamburger').addEventListener('click', function () {
    const menu = document.querySelector('#mobile-menu');
    menu.classList.toggle('hidden');
})