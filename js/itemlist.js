var relative_url = "/~sykorajak/"
var quantity_values = new Map();

document.addEventListener("DOMContentLoaded", function() {

    let items = document.getElementsByClassName('item');
    for (let i = 0; i < items.length; ++i) {

        let item = items[i];
        let name = extractName(item.getAttribute('data-id'));
        
        let deleteButton = items[i].getElementsByClassName('item-delete-button')[0];
        deleteButton.addEventListener('click', function() {
            removeItemFromList(item, name);
        });

        let editAmountButton = items[i].getElementsByClassName('item-edit-quantity-button')[0];
        let amountInput = items[i].getElementsByClassName('item-quantity-input')[0];

        let editCancelButton = items[i].getElementsByClassName('item-edit-cancel-button')[0];
        editCancelButton.addEventListener('click', function() {

            amountInput.setAttribute('readonly', 'true');
            editAmountButton.setAttribute('data-value', 'edit');
            amountInput.value = quantity_values[name];
        });

        quantity_values[name] = amountInput.value;
        
        editAmountButton.addEventListener('click', () => {

            value = editAmountButton.getAttribute('data-value');
            if (value == 'edit') {
                tryEditAmount(name, amountInput, editAmountButton);
            } else if (value == 'save') {
                trySaveAmount(name, amountInput, editAmountButton);
            }
        });

        let itemUp = items[i].getElementsByClassName('item-up')[0];
        let itemDown = items[i].getElementsByClassName('item-down')[0];
        
        itemUp.addEventListener('click', () => {
            swapUp(item);
        })
        itemDown.addEventListener('click', () => {
            swapDown(item);
        })
    }
});

function swapUp(item) {

    let items = document.getElementById('items');
    if (items.firstElementChild == item) {
        return;
    }
    let previous = item.previousSibling;
    swap_items(item, previous);
}

function swapDown(item) {

    let items = document.getElementById('items'); 
    if (items.lastElementChild == item) {
        return;
    }
    let next = item.nextSibling;
    swap_items(next, item);
}

function tryEditAmount(name, amountInput, editAmountButton) {

    amountInput.removeAttribute('readonly');
    editAmountButton.setAttribute('data-value', 'save');
}

function trySaveAmount(name, amountInput, editAmountButton) {

    quantity = amountInput.value;

    fetch(relative_url + "controllers/edit_item.php",
         { method: 'POST',
           body: JSON.stringify( { name: name, amount: quantity } ),
           headers: { 'Content-Type' : 'application/json' }
         }
    )
    .then(t => {
        if (!t.ok) {
            throw Error();
        }
        quantity_values[name] = quantity;
    })
    .catch(function() {
        addError('Unable to edit quantity of item ' + name + '.');
        amountInput.value = quantity_values[name];
    })
    .finally(function() {
        amountInput.setAttribute('readonly', 'true');
        editAmountButton.setAttribute('data-value', 'edit');
    });
}

function removeItemFromList(item, name) {

    fetch(relative_url + "controllers/remove_item.php",
         { method: 'POST',
           body: JSON.stringify( { name : name } ),
           headers: { 'Content-Type' : 'application/json' } 
         }
    )
    .then(t => {
        if (!t.ok) {
            throw Error();
        }
        let items = document.getElementById('items');
        items.removeChild(item);
    })
    .catch(function() {
        addError('Unable to remove item ' + sanitize(name) + ' in list.');
    });
}

function extractName(id) {
    return id.substring(5);
}

function swap_items(firstItem, secondItem) {
    
    let firstName = extractName(firstItem.getAttribute('data-id'));
    let secondName = extractName(secondItem.getAttribute('data-id'));
    let items = document.getElementById('items');

    fetch(relative_url + "controllers/swap_item.php",
        { method: 'POST',
        body: JSON.stringify( { firstName: firstName, secondName: secondName } ),
        headers: { 'Content-Type' : 'application/json' } 
        }
    )
    .then(t => {
        if (!t.ok) {
            throw Error();
        }
        items.insertBefore(firstItem, secondItem);
    })
    .catch(function() {
        addError('Unable to swap items in list.');
    });
}