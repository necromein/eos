// перейти ко всем пользователям
function showAll() {
    window.location.href = 'adminpanel.php';
}

// отобразить форму добавления пользователя
function addUser() {
    document.getElementById('addUserForm').style.display = 'block';
}

// редактировать данные пользователя
function editUser(button, userId) {
    var row = button.parentNode.parentNode;

    // сделать все ячейки редактируемыми
    for (var i = 1; i < row.cells.length - 1; i++) {
        row.cells[i].contentEditable = true;
        row.cells[i].classList.add('editable');
    }

    // изменить кнопку "Редактировать" на "Сохранить" и добавить кнопку "Отмена"
    button.innerHTML = "Сохранить";
    button.setAttribute("onclick", "saveUser(this, " + userId + ")");

    var cancelButton = document.createElement("button");
    cancelButton.innerHTML = "Отмена";
    cancelButton.setAttribute("onclick", "cancelEdit(this, " + userId + ")");
    cancelButton.classList.add("button-box");
    row.cells[row.cells.length - 1].appendChild(cancelButton);
}

// сохранить отредактированные данные пользователя
function saveUser(button, userId) {
    var row = button.parentNode.parentNode;
    var formData = new FormData();
    formData.append('update_user', true);
    formData.append('id', userId);
    formData.append('surname', row.cells[1].innerText);
    formData.append('name', row.cells[2].innerText);
    formData.append('patronymic', row.cells[3].innerText);
    formData.append('email', row.cells[4].innerText);
    formData.append('login', row.cells[5].innerText);
    formData.append('password', row.cells[6].innerText);
    formData.append('role', row.cells[7].innerText);

    if (confirm("Вы уверены, что хотите сохранить изменения?")) {
        fetch('adminpanel.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
                // восстановить кнопки
                row.cells[row.cells.length - 1].innerHTML = "<button class='button-box' onclick='editUser(this, " + userId + ")'>Редактировать</button><a href='?delete_user=" + userId + "' onclick='return confirmDelete()'><button class='button-box'>Удалить</button></a>";

                for (var i = 1; i < row.cells.length - 1; i++) {
                    row.cells[i].contentEditable = false;
                    row.cells[i].classList.remove('editable');
                }
            })
            .catch((error) => {
                console.error('Error during fetch:', error);
            });
    }
}

// отменить редактирование
function cancelEdit(button, userId) {
    var row = button.parentNode.parentNode;

    for (var i = 1; i < row.cells.length - 1; i++) {
        row.cells[i].contentEditable = false;
        row.cells[i].classList.remove('editable');
    }

    // восстановить кнопки
    row.cells[row.cells.length - 1].innerHTML = "<button class='button-box' onclick='editUser(this, " + userId + ")'>Редактировать</button><a href='?delete_user=" + userId + "'><button class='button-box'>Удалить</button></a>";
}

function confirmDelete() {
    return confirm("Вы уверены, что хотите удалить этого пользователя?");
}

function confirmAdd() {
    return confirm("Новый пользователь успешно добавлен");
}

// отобразить модальное окно
function showAddUserForm() {
    document.getElementById('addUserForm').style.display = 'block';
    document.getElementById('modalBackdrop').style.display = 'block';
}

// закрыть модальное окно
function closeAddUserForm() {
    document.getElementById('addUserForm').style.display = 'none';
    document.getElementById('modalBackdrop').style.display = 'none';
}
