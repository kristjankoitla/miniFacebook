let search = document.getElementById("search");
search.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        let query = search.value.split(' ').join('').toLowerCase();
        window.location.replace('/search?string=' + query)
    }
})