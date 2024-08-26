<?php
//session_start();
require_once __DIR__ . '/../dbconnect.php';

function fetchUsers()
{
    global $connect;
    $fetchUsersQuery = "SELECT id_user, surname, name, patronymic, email, login, password, role FROM users";
    $stmt = $connect->query($fetchUsersQuery);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function updateUser($id_user, $data)
{
    global $connect;

    $setClauses = [];
    foreach ($data as $field => $value) {
        $setClauses[] = "$field=:$field";
    }

    $updateUserQuery = "UPDATE users SET " . implode(', ', $setClauses) . " WHERE id_user=:id_user";
    $stmt = $connect->prepare($updateUserQuery);
    $data['id_user'] = $id_user;
    return $stmt->execute($data);
}

function deleteUser($id_user)
{
    global $connect;

    $deleteUserQuery = "DELETE FROM users WHERE id_user=:id_user";
    $stmt = $connect->prepare($deleteUserQuery);
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    return $stmt->execute();
}

function insertUser($data)
{
    global $connect;

    $columns = implode(',', array_keys($data));
    $placeholders = ':' . implode(',:', array_keys($data));
    $insertUserQuery = "INSERT INTO users ($columns) VALUES ($placeholders)";
    $stmt = $connect->prepare($insertUserQuery);
    return $stmt->execute($data);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // обновление данных юзеров
    if (isset($_POST['update_user'])) {
        $id_user = $_POST['update_user'];
        $data = [];

        foreach ($_POST as $key => $value) {
            if ($key !== 'update_user' && $key !== 'data-field') {
                $data[$key] = $value;
            }
        }

        $success = updateUser($id_user, $data);

        if ($success) {
            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['success' => false, 'error' => $connect->errorInfo()]);
            exit;
        }
    }

    // удаление юзера
    if (isset($_POST['delete_user'])) {
        $id_user = $_POST['delete_user'];

        $success = deleteUser($id_user);

        if ($success) {
            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['success' => false, 'error' => $connect->errorInfo()]);
            exit;
        }
    }

    // добавление юзера
    if (isset($_POST['insert_user'])) {
        $data = [
            'surname' => $_POST['surname'],
            'name' => $_POST['name'],
            'patronymic' => $_POST['patronymic'],
            'email' => $_POST['email'],
            'login' => $_POST['login'],
            'password' => $_POST['password'],
            'role' => $_POST['role'],
        ];

        $success = insertUser($data);

        if ($success) {
            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['success' => false, 'error' => $connect->errorInfo()]);
            exit;
        }
    }
}
?>
