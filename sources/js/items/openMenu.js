function openEditModal(item) {
    const modal = document.getElementById('editModal');
    
    document.getElementById('edit_item_id').value = item.id || '';
    document.getElementById('edit_item_name').value = item.item_name || '';
    document.getElementById('edit_item_description').value = item.item_description || '';
    document.getElementById('edit_item_art').value = item.item_art || '';
    document.getElementById('edit_item_category').value = item.item_category || '';
    document.getElementById('edit_item_cost').value = item.item_cost || '';
    document.getElementById('edit_item_retail').value = item.item_retail || '';
    document.getElementById('edit_item_manufacturer').value = item.item_manufacturer || '';
    document.getElementById('edit_item_remain').value = item.item_remain || '';
    document.getElementById('edit_item_unit').value = item.item_unit || '';
    document.getElementById('edit_item_status').value = item.item_status || '';
    
    modal.classList.add('active');
    
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.classList.remove('active');
    
    document.body.style.overflow = '';
    
    document.getElementById('editForm').reset();
}

function previewChanges() {
    const itemData = {
        id: document.getElementById('edit_item_id').value,
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
    
    console.log('Данные для отправки:', itemData);
    alert('Проверь консоль (F12) чтобы увидеть данные');
}

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('editModal');
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeEditModal();
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('active')) {
                closeEditModal();
            }
        });
    }
});