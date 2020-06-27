function toggleEmployeeTab(status) {

    let workersTab = document.getElementById("workersTab");
    let managersTab = document.getElementById("managersTab");
    let workersBtn = document.getElementById("btnWorkers");
    let managersBtn = document.getElementById("btnManagers");

    if (status === "workers") {
        workersTab.style.display = 'block';
        managersTab.style.display = 'none';
        workersBtn.classList.remove("btn-outline-info");
        workersBtn.classList.add("btn-info");
        managersBtn.classList.remove("btn-info");
        managersBtn.classList.add("btn-outline-info");
    }
    if (status === "managers") {
        workersTab.style.display = 'none';
        managersTab.style.display = 'block';
        workersBtn.classList.remove("btn-info");
        workersBtn.classList.add("btn-outline-info");
        managersBtn.classList.remove("btn-outline-info");
        managersBtn.classList.add("btn-info");
    }
}