<?php
session_start();
$config = require_once 'config.php';

$host = $config['db_host'];
$dbname = $config['db_name'];
$username = $config['db_user'];
$password = $config['db_pass'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("set names utf8");
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage();
    die();
}
 
if (!$_SESSION['user']) {
    header('Location: index.php');
}
$userRole = $_SESSION['user']['role'];
?>
<?php
// добавление нового пользователя
if (isset($_POST['add_user'])) {
    $surname = $_POST['surname'];
    $name = $_POST['name'];
    $patronymic = $_POST['patronymic'];
    $email = $_POST['email'];
    $login = $_POST['login'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $sql = "INSERT INTO users (surname, name, patronymic, email, login, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$surname, $name, $patronymic, $email, $login, $password, $role]);
    if ($stmt->rowCount() > 0) {
        //     echo "Новый пользователь успешно добавлен";
    } else {
        echo "Ошибка: " . $sql . "<br>" . $e->getMessage();
    }
}

// обновление данных пользователя
if (isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $surname = $_POST['surname'];
    $name = $_POST['name'];
    $patronymic = $_POST['patronymic'];
    $email = $_POST['email'];
    $login = $_POST['login'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET surname=?, name=?, patronymic=?, email=?, login=?, password=?, role=? WHERE id_user=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$surname, $name, $patronymic, $email, $login, $password, $role, $id]);
    if ($stmt->rowCount() > 0) {
        $data = array('success' => true);
    } else {
        $data = array('success' => false, 'error' => $e->getMessage());
    }

    echo json_encode($data);
    exit;
}

// обработка удаления пользователя
if (isset($_GET['delete_user'])) {
    $id = $_GET['delete_user'];
    $sql = "DELETE FROM users WHERE id_user=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
}

// запрос на получение данных из таблицы
$sql = "SELECT * FROM users";
$result = $pdo->query($sql);

// запрос на получение данных из таблицы с учетом поискового запроса
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM users";

if (!empty($search)) {
    $sql .= " WHERE 
            id_user LIKE '%$search%' OR
            surname LIKE '%$search%' OR
            name LIKE '%$search%' OR
            patronymic LIKE '%$search%' OR
            email LIKE '%$search%' OR
            login LIKE '%$search%' OR
            role LIKE '%$search%'";
}

$result = $pdo->query($sql);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- <link rel="stylesheet" href="css/auth.css"> -->
    <link rel="stylesheet" href="css/panel.css">
    <link rel="shortcut icon" href="img/star.svg" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<?php include 'header.php'; ?>

<body>
    <section class="panel">

        <div class="topic-title">
            <img class="star-img" src="img/star.svg">
            <h2 class="regtext">панель администратора</h2>
            <!-- <h2 class="inverse">администратора</h2> -->
        </div>
        <div class="crud-panel">
            <!-- добавление нового пользователя -->
            <!-- <button class="button-box" onclick="addUser()">Добавить пользователя</button> -->
            <button class="button-box" onclick="showAddUserForm()">Добавить пользователя</button>

            <!-- поиск -->
            <form method="get" action="" class="search-container">
                <input class="input-box" type="text" name="search" value="<?= $search ?>" placeholder="Поиск">
                <input class="button-box" type="submit" value="Искать">

            </form>
        </div>

        <table class="admin-tbl">
            <tr>
                <th>ID</th>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Отчество</th>
                <th>Email</th>
                <th>Логин</th>
                <th>Пароль</th>
                <th>Роль</th>
                <th>Действия</th>
            </tr>
            <?php
            if ($result->rowCount() > 0) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>{$row['id_user']}</td>";
                    echo "<td contenteditable='false' onBlur='updateUser(this,\"surname\",{$row['id_user']})'>{$row['surname']}</td>";
                    echo "<td contenteditable='false' onBlur='updateUser(this,\"name\",{$row['id_user']})'>{$row['name']}</td>";
                    echo "<td contenteditable='false' onBlur='updateUser(this,\"patronymic\",{$row['id_user']})'>{$row['patronymic']}</td>";
                    echo "<td contenteditable='false' onBlur='updateUser(this,\"email\",{$row['id_user']})'>{$row['email']}</td>";
                    echo "<td contenteditable='false' onBlur='updateUser(this,\"login\",{$row['id_user']})'>{$row['login']}</td>";
                    echo "<td contenteditable='false' onBlur='updateUser(this,\"password\",{$row['id_user']})'>{$row['password']}</td>";
                    echo "<td contenteditable='false' onBlur='updateUser(this,\"role\",{$row['id_user']})'>{$row['role']}</td>";
                    echo "<td><button class='button-box' onclick='editUser(this, {$row['id_user']})'>Редактировать</button><a onclick='return confirmDelete()' href='?delete_user={$row['id_user']}'><button class='button-box'>Удалить</button></a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<td colspan='9' class='no-res' style='text-align: center'>Ничего не найдено.</td>";
            }
            ?>

        </table>
        <div class="show-all-data">
            <?php if (!empty($search)): ?>
                <button class="button-box" type="button" onclick="showAll()">Показать все</button>
            <?php endif; ?>
        </div>

        <div id="modalBackdrop" class="modal-backdrop"></div>

        <div id="addUserForm">
            <h3>Добавить нового пользователя</h3>
            <form method="post" action="">
                <div class="add-container">
                    <div class="add-input">
                        <input class="input-box" type="text" name="surname" placeholder="Фамилия"><br>
                        <input class="input-box" type="text" name="name" placeholder="Имя"><br>
                        <input class="input-box" type="text" name="patronymic" placeholder="Отчество"><br>
                        <input class="input-box" type="email" name="email" placeholder="Email"><br>
                        <input class="input-box" type="text" name="login" placeholder="Логин"><br>
                        <input class="input-box" type="password" name="password" placeholder="Пароль"><br>
                        <!-- <input class="input-box" type="text" name="role" placeholder="Роль"><br> -->
                        <?php
                        $str = 'SELECT id_role, role_u FROM role';
                        $query = $pdo->query($str);
                        ?>
                        <select class="input-box" name="role" id="role">
                            <?php while ($rows = $query->fetch(PDO::FETCH_ASSOC)) { ?>
                                <option value="<?php echo $rows['id_role']; ?>">
                                    <?php echo $rows['role_u']; ?>
                                </option>
                            <?php } ?>
                        </select>
                        <input class="button-box" type="submit" name="add_user" onclick="confirmAdd()"
                            style="margin-top:20px" value='Сохранить'>
                        <button class="button-box" type="button" onclick="closeAddUserForm()"
                            style="margin-top:10px">Отмена</button>
                    </div>
                    <!-- <div>
                        <img src="img/abstract-modal.svg" alt="">
                    </div> -->
                </div>

            </form>
        </div>
    </section>
    <?php include 'footer.php'; ?>

    <script src="js/script.js"></script>
    <script src="js/adminpanel.js"></script>

</body>

</html>
