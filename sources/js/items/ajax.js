const itemsSuccess = document.getElementById("items-success");
const itemsError = document.getElementById("items-error");
const tableBody = document.querySelector("tbody");

function validateRequiredFields() {
    const requiredFields = [
        { id: 'item_name', name: 'Название товара' },
        { id: 'item_cost', name: 'Себестоимость' },
        { id: 'item_retail', name: 'Розничная цена' },
        { id: 'item_remain', name: 'Остаток' }
    ];

    const missingFields = [];

    for (const field of requiredFields) {
        const element = document.getElementById(field.id);
        const value = element ? element.value : null;
        
        if (!value || value.trim() === '') {
            missingFields.push(field.name);
            element.style.borderColor = 'red';
        } else {
            element.style.borderColor = '';
        }
    }

    return missingFields;
}

function resetFieldHighlight() {
    const fields = ['item_name', 'item_cost', 'item_retail', 'item_remain'];
    fields.forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element) {
            element.style.borderColor = '';
        }
    });
}

function addValidationListeners() {
    const fields = ['item_name', 'item_cost', 'item_retail', 'item_remain'];
    fields.forEach(fieldId => {
        const element = document.getElementById(fieldId);
        if (element) {
            element.addEventListener('focus', () => {
                element.style.borderColor = '';
            });
        }
    });
}

function addRowToTable(itemData) {
    const firstRow = tableBody.querySelector('tr');
    if (firstRow) {
        const emptyMessage = firstRow.querySelector('td[colspan="10"]');
        if (emptyMessage) {
            tableBody.innerHTML = '';
        }
    }

    const newRow = document.createElement('tr');
    newRow.setAttribute('ondblclick', 'this.classList.toggle("highlight")');
    newRow.style.cursor = 'pointer';

    newRow.innerHTML = `
        <td>${escapeHtml(itemData.id)}</td>
        <td>${escapeHtml(itemData.item_name || '—')}</td>
        <td>${escapeHtml(itemData.item_art || '—')}</td>
        <td>${escapeHtml(itemData.item_category || '—')}</td>
        <td>${escapeHtml(itemData.item_remain || '—')}</td>
        <td>${escapeHtml(itemData.item_retail || '—')}</td>
        <td>${escapeHtml(itemData.item_cost || '—')}</td>
        <td>${escapeHtml(itemData.item_manufacturer || '—')}</td>
        <td>${escapeHtml(itemData.item_unit || '—')}</td>
        <td>${escapeHtml(itemData.item_status || '—')}</td>
        <td>${escapeHtml(itemData.item_description || '—')}</td>
    `;

    tableBody.insertBefore(newRow, tableBody.firstChild);
    
    setTimeout(() => {
        newRow.classList.add('highlight');
        setTimeout(() => {
            newRow.classList.remove('highlight');
        }, 5000);
    }, 100);
}

function escapeHtml(text) {
    if (text === null || text === undefined) return '—';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function clearForm() {
    document.getElementById("item_name").value = '';
    document.getElementById("item_description").value = '';
    document.getElementById("item_art").value = '';
    document.getElementById("item_category").value = '';
    document.getElementById("item_cost").value = '';
    document.getElementById("item_retail").value = '';
    document.getElementById("item_manufacturer").value = '';
    document.getElementById("item_remain").value = '';
    document.getElementById("item_unit").value = '';
    document.getElementById("item_status").value = '';
}

async function createItem() {
    const missingFields = validateRequiredFields();
    
    if (missingFields.length > 0) {
        itemsError.innerHTML = `Заполните обязательные поля: ${missingFields.join(', ')}`;
        itemsError.style.display = "block";

        setTimeout(() => {
            itemsError.style.display = "none";
        }, 3000);
        return;
    }

    const item_name = document.getElementById("item_name").value ?? null;
    const item_description = document.getElementById("item_description").value ?? null;
    const item_art = document.getElementById("item_art").value ?? null;
    const item_category = document.getElementById("item_category").value ?? null;
    const item_cost = document.getElementById("item_cost").value ?? null;
    const item_retail = document.getElementById("item_retail").value ?? null;
    const item_manufacturer = document.getElementById("item_manufacturer").value ?? null;
    const item_remain = document.getElementById("item_remain").value ?? null;
    const item_unit = document.getElementById("item_unit").value ?? null;
    const item_status = document.getElementById("item_status").value ?? null;

    if (isNaN(item_cost) || isNaN(item_retail) || isNaN(item_remain)) {
        itemsError.innerHTML = "Себестоимость, розничная цена и остаток должны быть числами";
        itemsError.style.display = "block";
        
        setTimeout(() => {
            itemsError.style.display = "none";
        }, 3000);
        return;
    }

    try {
        const response = await fetch(`http://localhost/busync/api/v1/createItem/`, {
            method: 'POST',
            headers: {
                'API-key': API_KEY,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                "fields": {
                    "item_name": item_name,
                    "item_description": item_description,
                    "item_art": item_art,
                    "item_category": item_category,
                    "item_cost": item_cost,
                    "item_retail": item_retail,
                    "item_manufacturer": item_manufacturer,
                    "item_remain": item_remain,
                    "item_unit": item_unit,
                    "item_status": item_status
                }
            })
        });

        const result = await response.json();

        if (result.success) {
            resetFieldHighlight();
            
            itemsSuccess.innerHTML = "Успешно!";
            itemsSuccess.style.display = "block";

            addRowToTable({
                id: result.data?.id || 'Новый',
                item_name: item_name,
                item_art: item_art,
                item_category: item_category,
                item_remain: item_remain,
                item_retail: item_retail,
                item_cost: item_cost,
                item_manufacturer: item_manufacturer,
                item_unit: item_unit,
                item_status: item_status,
                item_description: item_description
            });

            setTimeout(() => {
                itemsSuccess.style.display = "none";
            }, 2000);

            clearForm();

        } else {
            let error = "Неизвестная ошибка";

            switch (result.error.message) {
                case "Unauthorized": 
                    error = "Неавторизован";
                    break;
                case "Invalid API-key": 
                    error = "Неверный API-key"; 
                    break;
                case "Unauthorized session": 
                    error = "Авторизуйте сессию"; 
                    break;
                case "Server error": 
                    error = "Ошибка сервера. Попробуйте позже"; 
                    break;
                case "invalid_user_id": 
                    error = "Неверный user_id"; 
                    break;
                case "Invalid JSON format": 
                    error = "Ошибка сервера. Попробуйте позже";
                    break;
                case "Empty fields": 
                    error = "Ошибка сервера. Попробуйте позже";
                    break;
                case "Insert error": 
                    error = "Ошибка сервера. Попробуйте позже";
                    break;
                default: 
                    error = "Поля имеют запрещенные символы или не соответствуют требованиям";
            } 

            itemsError.innerHTML = error;
            itemsError.style.display = "block";

            setTimeout(() => {
                itemsError.style.display = "none";
            }, 2000);
            console.log(result.error.message);
        }
    } catch (error) {
        itemsError.style.display = "block";
        itemsError.innerHTML = "Ошибка сервера. Попробуйте позже";

        setTimeout(() => {
            itemsError.style.display = "none";
        }, 2000);
        console.log(error.message);
    }
}

function createItem() {
    const itemData = {
        item_name: document.getElementById('item_name').value,
        item_description: document.getElementById('item_description').value,
        item_art: document.getElementById('item_art').value,
        item_category: document.getElementById('item_category').value,
        item_cost: document.getElementById('item_cost').value,
        item_retail: document.getElementById('item_retail').value,
        item_manufacturer: document.getElementById('item_manufacturer').value,
        item_remain: document.getElementById('item_remain').value,
        item_unit: document.getElementById('item_unit').value,
        item_status: document.getElementById('item_status').value
    };

    fetch('http://localhost/busync/api/v1/createItem/', {
        method: 'POST',
        headers: {
            'API-key': API_KEY,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ fields: itemData })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Товар успешно создан!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showNotification('Ошибка: ' + (data.error?.message || 'Неизвестная ошибка'), 'error');
        }
    })
    .catch(error => {
        showNotification('Ошибка соединения с сервером', 'error');
        console.error('Error:', error);
    });
}

function updateItem() {
    const itemId = document.getElementById('edit_item_id').value;
    const itemData = {
        id: itemId,
        item_name: document.getElementById('edit_item_name').value,
        item_description: document.getElementById('edit_item_description').value,
        item_art: document.getElementById('edit_item_art').value,
        item_category: document.getElementById('edit_item_category').value,
        item_cost: document.getElementById('edit_item_cost').value,
        item_retail: document.getElementById('edit_item_retail').value,
        item_manufacturer: document.getElementById('edit_item_manufacturer').value,
        item_remain: document.getElementById('edit_item_remain').value,
        item_unit: document.getElementById('edit_item_unit').value,
        item_status: document.getElementById('edit_item_status').value
    };

    fetch('http://localhost/busync/api/v1/editItem/', {
        method: 'POST',
        headers: {
            'API-key': API_KEY,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ fields: itemData })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Товар успешно обновлен!', 'success');
            closeEditModal();
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showNotification('Ошибка: ' + (data.error?.message || 'Неизвестная ошибка'), 'error');
            
            if (data.error?.code === 404) {
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        }
    })
    .catch(error => {
        showNotification('Ошибка соединения с сервером', 'error');
        console.error('Error:', error);
    });
}

function showNotification(message, type) {
    const notification = document.getElementById('items-' + type);
    if (notification) {
        notification.textContent = message;
        notification.style.display = 'block';
        
        setTimeout(() => {
            notification.style.display = 'none';
        }, 3000);
    }
}

document.addEventListener('DOMContentLoaded', addValidationListeners);