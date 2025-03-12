<?php
// 配置项
$localVerificationCode = "aabb33"; // 本地验证码对照值
$logFilePath = 'log/express.txt'; // 日志文件路径

// 获取请求参数
$receivedVerificationCode = $_GET['key'] ?? ''; // 验证码
$KDId = $_GET['id'] ?? ''; // 快递单号

// 定义函数：检查 KDId 是否在日志文件中存在
function checkKDIdExistsInLog($KDId, $filePath) {
    if (!file_exists($filePath)) {
        return false; // 文件不存在，视为未记录
    }

    $fileContent = file_get_contents($filePath);
    // 搜索 KDId 是否存在于文件内容中
    return strpos($fileContent, $KDId) !== false;
}

// 定义函数：将 KDId 记录到日志文件中
function recordKDIdInLog($KDId, $filePath) {
    $logEntry = "{$KDId}\t" .  date("Y-m-d H:i:s") . "\r\n";
    if (file_put_contents($filePath, $logEntry, FILE_APPEND | LOCK_EX) === false) {
        return "写入失败";
    }
    return "成功";
}

// 验证请求
if ($receivedVerificationCode !== $localVerificationCode) {
    header('HTTP/1.1 400 Bad Request');
    exit('BAD REQUEST'); // 返回纯文本错误信息
}

// 确保目录存在
$dirPath = dirname($logFilePath);
if (!is_dir($dirPath)) {
    if (!mkdir($dirPath, 0777, true)) {
        exit("无法创建目录: {$dirPath}"); // 返回纯文本错误信息
    }
}

// 检查 KDId 是否已存在于日志中，并进行相应操作
if (checkKDIdExistsInLog($KDId, $logFilePath)) {
    echo "✖✖✖重复✖✖✖";
} else {
    $result = recordKDIdInLog($KDId, $logFilePath);
    echo $result; // 输出“成功”或“写入失败”
}
?>