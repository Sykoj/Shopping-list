var relative_url = "/~sykorajak/"
var exclude_list_request = "controllers/fetch_items.php?excludeList=true";

document.addEventListener("DOMContentLoaded", function() {

    let input = document.getElementById('input-items-id'); 

    input.addEventListener("focus", function() {
        fetchItemsExcludedFromList();
    });
});

function fetchItemsExcludedFromList() {

    let datalist = document.getElementById('items-datalist');
    fetch(relative_url + exclude_list_request)
    .then(t => {
        if (!t.ok) {
            throw Error();
        }
        return t.json();
    })
    .then(function(data) {

        datalist.innerHTML = "";
        data['items'].forEach(item => {
            datalist.innerHTML += "<option>" + sanitize(item.name) + "</option>\n";
        });
    }).catch(function() {
        addError('Unable to fetch data into autocomplete list.');
    });
}