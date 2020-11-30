let search = document.getElementById("search");
search.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        let query = search.value.split(' ').join('').toLowerCase();
        window.location.replace('/~krkoit/prax3/search?string=' + query)
    }
})