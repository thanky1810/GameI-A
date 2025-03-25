function handleLetGo() {
    let typePlay = document.getElementById("list-type-play").value;

    if (typePlay === "") {
        alert("Vui lòng chọn kiểu chơi");
        return;
    }
    window.location.href = "./caro.html?type=" + typePlay;
}