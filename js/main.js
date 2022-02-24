

const menu = document.querySelector('.contenido');

menu.addEventListener('click', function () {
  console.log('clik')
  document.getElementById('sidebar').classList.toggle('active');
  console.log(document.getElementById('sidebar'))
});

const mapa = document.querySelector('.contenido');

mapa.addEventListener('click', function () {
    console.log('click')
    document.getElementById('map').classList.toggle('active');
    console.log(document.getElementById('map'))
});
