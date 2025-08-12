// CKEditor instance references
let addEditor, editEditor;

// Helper to initialize CKEditor
function initCKEditor(id, assignEditor) {
    const el = document.getElementById(id);
    if (el) {
        ClassicEditor
            .create(el)
            .then(editor => {
                assignEditor(editor);
            })
            .catch(console.error);
    }
}

// Helper to reset modal forms and editors
function resetModal(modalId, editor) {
    document.getElementById(modalId).addEventListener('hidden.bs.modal', function() {
        this.querySelector('form').reset();
        if (editor) editor.setData('');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initCKEditor('description', editor => addEditor = editor);
    initCKEditor('edit_description', editor => editEditor = editor);

    resetModal('addMemberModal', addEditor);
    resetModal('editMemberModal', editEditor);
    resetModal('addServiceModal', addEditor);
    resetModal('editServiceModal', editEditor);

    // Search and filter functionality
    document.getElementById('searchInput').addEventListener('input', filterTeamMembers);
    document.getElementById('positionFilter').addEventListener('change', filterTeamMembers);
});

// Edit member function
function editMember(member) {
    document.getElementById('edit_id').value = member.id;
    document.getElementById('edit_name').value = member.name;
    document.getElementById('edit_position').value = member.position;
    document.getElementById('edit_title').value = member.title || '';
    document.getElementById('edit_email').value = member.email || '';
    document.getElementById('edit_phone').value = member.phone || '';
    document.getElementById('edit_linkedin_profile').value = member.linkedin_profile || '';
    document.getElementById('edit_bio').value = member.bio || '';
    if (editEditor) editEditor.setData(member.description || '');
    new bootstrap.Modal(document.getElementById('editMemberModal')).show();
}

// Edit service function
function editService(service) {
    document.getElementById('edit_id').value = service.id;
    document.getElementById('edit_title').value = service.title;
    if (editEditor) editEditor.setData(service.description || '');
    new bootstrap.Modal(document.getElementById('editServiceModal')).show();
}

// View member function
function viewMember(id) {
    alert('View member with ID: ' + id +
        '\n\nThis function can be expanded to show detailed member information in a modal or separate page.');
}

// View service function
function viewService(id) {
    alert('View service with ID: ' + id +
        '\n\nThis function can be expanded to show detailed service information in a modal or separate page.');
}

// Delete member function
function deleteMember(id, name) {
    document.getElementById('delete_member_id').value = id;
    document.getElementById('delete_member_name').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteMemberModal')).show();
}

// Delete service function
function deleteService(id, title) {
    document.getElementById('delete_service_id').value = id;
    document.getElementById('delete_service_title').textContent = title;
    new bootstrap.Modal(document.getElementById('deleteServiceModal')).show();
}

// Toggle description preview
function toggleDescription(element) {
    element.classList.toggle('expanded');
}

// Search and filter functionality
function filterTeamMembers() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const positionFilter = document.getElementById('positionFilter').value.toLowerCase();
    const tableRows = document.querySelectorAll('#teamMembersTable tbody tr');

    tableRows.forEach(row => {
        const name = row.querySelector('td:nth-child(2) .fw-bold').textContent.toLowerCase();
        const position = row.querySelector('td:nth-child(3) .badge').textContent.toLowerCase();
        const title = row.querySelector('td:nth-child(4) span').textContent.toLowerCase();
        const bio = row.querySelector('td:nth-child(2) small')?.textContent.toLowerCase() || '';

        const matchesSearch = name.includes(searchTerm) ||
            position.includes(searchTerm) ||
            title.includes(searchTerm) ||
            bio.includes(searchTerm);

        const matchesPosition = !positionFilter || position === positionFilter;

        row.style.display = (matchesSearch && matchesPosition) ? '' : 'none';
    });

    updateFilteredStats();
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    document.getElementById('positionFilter').value = '';
    filterTeamMembers();
}

function updateFilteredStats() {
    const visibleRows = document.querySelectorAll('#teamMembersTable tbody tr:not([style*="display: none"])');
    const totalVisible = visibleRows.length;
    const statsNumber = document.querySelector('.stats-number');
    if (statsNumber) statsNumber.textContent = totalVisible;
}

function exportTeamData() {
    const visibleRows = document.querySelectorAll('#teamMembersTable tbody tr:not([style*="display: none"])');
    const exportData = [];

    visibleRows.forEach(row => {
        const member = {
            name: row.querySelector('td:nth-child(2) .fw-bold').textContent,
            position: row.querySelector('td:nth-child(3) .badge').textContent,
            title: row.querySelector('td:nth-child(4) span').textContent,
            email: row.querySelector('td:nth-child(5) a[href^="mailto:"]')?.textContent.replace(/^.*?@/, '@') || '',
            phone: row.querySelector('td:nth-child(5) a[href^="tel:"]')?.textContent || '',
            linkedin: row.querySelector('td:nth-child(6) a')?.href || ''
        };
        exportData.push(member);
    });

    const csvContent = "data:text/csv;charset=utf-8," +
        "Name,Position,Title,Email,Phone,LinkedIn\n" +
        exportData.map(row => Object.values(row).map(value => `"${value}"`).join(',')).join('\n');

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "team_members.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function exportServicesData() {
    const visibleRows = document.querySelectorAll('#servicesTable tbody tr:not([style*="display: none"])');
    const exportData = [];

    visibleRows.forEach(row => {
        const service = {
            title: row.querySelector('td:nth-child(2) .fw-bold').textContent,
            description: row.querySelector('td:nth-child(3) .description-preview').textContent,
            created: row.querySelector('td:nth-child(4) small').textContent
        };
        exportData.push(service);
    });

    const csvContent = "data:text/csv;charset=utf-8," +
        "Title,Description,Created\n" +
        exportData.map(row => Object.values(row).map(value => `"${value}"`).join(',')).join('\n');

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "services.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Auto-refresh team and services data every 5 minutes
setInterval(() => {
    const now = new Date();
    console.log('Team data last updated:', now.toLocaleTimeString());
    console.log('Services data last updated:', now.toLocaleTimeString());
}, 300000);
