<?php
// savid_Class.php 快递公司划分
require_once "./savid_Class.php";

class LogisticsService {
    private const LOCAL_VERIFICATION_CODE = "aabb33";
    private const DB_FILE_PATH = 'log/monitor.db';
    
    private $db;
    private $kdId;
    private $companyName;
    private $message;
    private $messageClass;

    public function __construct(string $receivedVerificationCode, string $kdId) {
        $this->validateRequest($receivedVerificationCode);
        $this->kdId = trim($kdId);
        $this->initializeDatabase();
        $this->processLogistics();
    }

    private function validateRequest(string $receivedVerificationCode): void {
        if ($receivedVerificationCode !== self::LOCAL_VERIFICATION_CODE) {
            exit('BAD REQUEST');
        }
    }

    private function initializeDatabase(): void {
        $this->db = new SQLite3(self::DB_FILE_PATH);
        $this->db->exec("CREATE TABLE IF NOT EXISTS logs (
            id TEXT PRIMARY KEY, 
            timestamp TEXT, 
            company_name TEXT
        )");
    }

    private function processLogistics(): void {
        $identifier = new LogisticsIdentifier($this->kdId);
        $this->companyName = $identifier->identify();
        
        if ($this->isDuplicate()) {
            $this->message = "✖重复✖" . $this->companyName;
            $this->messageClass = "error";
        } else {
            $this->saveToDatabase();
            $this->message = $this->companyName ?: "成功-名称未知";
            $this->messageClass = "success";
        }
    }

    private function isDuplicate(): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM logs WHERE id = :id");
        $stmt->bindValue(':id', $this->kdId, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row['count'] > 0;
    }

    private function saveToDatabase(): void {
        $stmt = $this->db->prepare("INSERT INTO logs (id, timestamp, company_name) 
                                   VALUES (:id, :timestamp, :company_name)");
        $stmt->bindValue(':id', $this->kdId, SQLITE3_TEXT);
        $stmt->bindValue(':timestamp', date("Y-m-d H:i:s"), SQLITE3_TEXT);
        $stmt->bindValue(':company_name', $this->companyName, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function getMessageClass(): string {
        return $this->messageClass;
    }

    public function getKdId(): string {
        return $this->kdId;
    }
}

// 使用示例
$service = new LogisticsService($_GET['key'] ?? '', $_GET['id'] ?? '');
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>操作反馈</title>
    <style>
        body { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            background-color: #f7f7f7; 
            font-family: Arial, sans-serif; 
        }
        #messageBox { 
            text-align: center; 
            padding: 20px; 
            border-radius: 10px; 
            box-shadow: 0 4px 8px rgba(0,0,0,.1); 
            background-color: white; 
            transition: transform .3s ease-in-out; 
        }
        #messageBox.success { color: #28a745; }
        #messageBox.error { color: #dc3545; }
        @keyframes fadeIn { 
            from { opacity: 0; transform: scale(.9); } 
            to { opacity: 1; transform: scale(1); } 
        }
        #messageBox { animation: fadeIn .5s; }
    </style>
</head>
<body onclick="copyKDId()">
    <div id="messageBox" class="<?= htmlspecialchars($service->getMessageClass()) ?>">
        <p style="font-size: 24px;">操作结果：</p>
        <p style="font-size: 48px;"><?= htmlspecialchars($service->getMessage()) ?></p>
        <p>单号: <span id="kdIdSpan"><?= htmlspecialchars($service->getKdId()) ?></span></p>
    </div>

    <script>
    function copyKDId() {
        const kdIdSpan = document.getElementById('kdIdSpan');
        const tempInput = document.createElement('input');
        tempInput.value = kdIdSpan.innerText;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        alert('单号 已复制到剪贴板');
    }
    </script>
</body>
</html>
