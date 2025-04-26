/*Admin 2 */
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("search");

    searchInput.addEventListener("keyup", function () {
        const query = searchInput.value;
        console.log("Tìm kiếm:", query);
        // Bạn có thể thêm mã xử lý tìm kiếm ở đây.
    });
});
