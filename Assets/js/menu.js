document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('translate-menus').addEventListener('click', function () {
        translateMenus();
    });
});

function translateMenus()
{
    let sourceLanguage = document.getElementById('source-language').value;
    let destLanguage = document.getElementById('dest-language').value;
    let nonce = document.getElementById('nonce').value;
    
    let menuSelector = document.getElementById('menu-select');
    let menus = Array.from(menuSelector.options).filter(function (option) {
        return option.selected;
    }).map(function (option) {
        return option.value;
    });

    if (sourceLanguage == '' || destLanguage == '' || !menus || !menus.length) {
        alert('You must select a source language, destination language, and at least 1 menu to translate.');
    }

    let http = new XMLHttpRequest();
    let data = {
        'sourceLang': sourceLanguage,
        'destLang': destLanguage,
        'menus': menus,
        '_wpnonce': nonce,
    };
    let dataString = JSON.stringify(data);

    http.open('POST', '/wp-json/multilingualmenuduplication/v1/schedule/', true);
    http.setRequestHeader('Content-Type', 'application/json');
    http.setRequestHeader('X-WP-Nonce', nonce);
    http.send(dataString);

    document.querySelector('.mmd__result').style.display = 'block';

    setTimeout(function() {
        document.querySelector('.mmd__result').style.display = 'none';
    }, 3000);
}
