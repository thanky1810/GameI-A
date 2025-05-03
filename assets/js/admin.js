/*Admin 2 */
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search");

    searchInput.addEventListener("keyup", function () {
        const query = searchInput.value;
        console.log("Tìm kiếm:", query);
        // Bạn có thể thêm mã xử lý tìm kiếm ở đây.
    });
});
function openEditModal(id, username, password, role, score, sumWin) {
    document.getElementById('editModal').style.display = 'block';
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_password').value = password;
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_score').value = score;
    document.getElementById('edit_sumWin').value = sumWin;
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

function openAddUserModal() {
    document.getElementById('addUserModal').style.display = 'block';
}

function closeAddUserModal() {
    document.getElementById('addUserModal').style.display = 'none';
}