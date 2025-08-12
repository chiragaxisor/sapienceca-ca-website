// Initialize CKEditor for description fields
    let addEditor, editEditor;

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize CKEditor for add modal
        if (document.getElementById('description')) {
            ClassicEditor
                .create(document.getElementById('description'))
                .then(editor => {
                    addEditor = editor;
                })
                .catch(error => {
                    console.error(error);
                });
        }

        // Initialize CKEditor for edit modal
        if (document.getElementById('edit_description')) {
            ClassicEditor
                .create(document.getElementById('edit_description'))
                .then(editor => {
                    editEditor = editor;
                })
                .catch(error => {
                    console.error(error);
                });
        }
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

        if (editEditor) {
            editEditor.setData(member.description || '');
        }

        new bootstrap.Modal(document.getElementById('editMemberModal')).show();
    }

    // View member function
    function viewMember(id) {
        // You can implement a view modal here or redirect to a detail page
        // For now, we'll just show an alert with the member ID
        alert('View member with ID: ' + id +
            '\n\nThis function can be expanded to show detailed member information in a modal or separate page.');
    }

    // Delete member function
    function deleteMember(id, name) {
        document.getElementById('delete_member_id').value = id;
        document.getElementById('delete_member_name').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteMemberModal')).show();
    }

    // Clear form when add modal is closed
    document.getElementById('addMemberModal').addEventListener('hidden.bs.modal', function() {
        this.querySelector('form').reset();
        if (addEditor) {
            addEditor.setData('');
        }
    });

    // Clear form when edit modal is closed
    document.getElementById('editMemberModal').addEventListener('hidden.bs.modal', function() {
        this.querySelector('form').reset();
        if (editEditor) {
            editEditor.setData('');
        }
    });

    // Search and filter functionality
    document.getElementById('searchInput').addEventListener('input', filterTeamMembers);
    document.getElementById('positionFilter').addEventListener('change', filterTeamMembers);

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

            if (matchesSearch && matchesPosition) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
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

        // Update stats to show filtered count
        const statsNumber = document.querySelector('.stats-number');
        if (statsNumber) {
            statsNumber.textContent = totalVisible;
        }
    }

    function exportTeamData() {
        const visibleRows = document.querySelectorAll('#teamMembersTable tbody tr:not([style*="display: none"])');
        const exportData = [];

        visibleRows.forEach(row => {
            const member = {
                name: row.querySelector('td:nth-child(2) .fw-bold').textContent,
                position: row.querySelector('td:nth-child(3) .badge').textContent,
                title: row.querySelector('td:nth-child(4) span').textContent,
                email: row.querySelector('td:nth-child(5) a[href^="mailto:"]')?.textContent.replace(/^.*?@/,
                    '@') || '',
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

    // Auto-refresh team data every 5 minutes
    setInterval(() => {
        // You can implement AJAX refresh here if needed
        // For now, just update the timestamp
        const now = new Date();
        console.log('Team data last updated:', now.toLocaleTimeString());
    }, 300000);