<?php
session_start();

function pdo(): PDO
{
    static $pdo;
    if (!$pdo) {
        if (file_exists(__DIR__ . '/config.php')) {
            $config = include __DIR__ . '/config.php';
        } else {
            $msg = 'Создайте и настройте config.php';
            trigger_error($msg, E_USER_ERROR);
        }
        $dsn = 'mysql:dbname=' . $config['db_name'] . ';host=' . $config['db_host'];
        $pdo = new PDO($dsn, $config['db_user'], $config['db_pass']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    return $pdo;
} 

function flash(?string $message = null)
{
    if ($message) {
        $_SESSION['flash'] = $message;
    } else {
        if (!empty($_SESSION['flash'])) { ?>
            <div class=" ">
                <p>
                    <?= $_SESSION['flash'] ?>
                </p>
            </div>
        <?php }
        unset($_SESSION['flash']);
    }
}
function check_auth(): bool
{
    return !!($_SESSION['user_id'] ?? false);
}
?>