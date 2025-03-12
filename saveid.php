<?php

// 配置项
$localVerificationCode = "aabb33"; // 本地验证码对照值
$dbFilePath = 'log/express.db'; // SQLite3 数据库文件路径

// 获取请求参数
$receivedVerificationCode = $_GET['key']; // 验证码
$KDId = $_GET['id']; // ID

// 验证请求
if ($receivedVerificationCode !== $localVerificationCode) {
    exit('BAD REQUEST'); // 不合法请求，直接终止执行
}

// 创建或打开 SQLite3 数据库
$db = new SQLite3($dbFilePath);

// 创建日志表（如果不存在）
$db->exec("CREATE TABLE IF NOT EXISTS logs (id TEXT PRIMARY KEY, timestamp TEXT)");

// 检查 KDId 是否已存在于数据库中
if (checkKDIdExistsInDB($KDId, $db)) {
    $ShuChu = "✖✖重复✖✖";
    $messageClass = "error"; // 设置消息框的样式类
} else {
    // 处理逻辑，记录日志
    $stmt = $db->prepare("INSERT INTO logs (id, timestamp) VALUES (:id, :timestamp)");
    $stmt->bindValue(':id', $KDId, SQLITE3_TEXT);
    $stmt->bindValue(':timestamp', date("Y-m-d H:i:s"), SQLITE3_TEXT);
    $stmt->execute();
    $ShuChu = "成功";
    $messageClass = "success"; // 设置消息框的样式类
}

// 函数：检查 KDId 是否在数据库中存在
function checkKDIdExistsInDB($KDId, $db) {
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM logs WHERE id = :id");
    $stmt->bindValue(':id', $KDId, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);
    return $row['count'] > 0;
}

echo $ShuChu;
?>