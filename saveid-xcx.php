<?php
// savid_Class.php 快递公司划分
require_once "./savid_Class.php";

class LogisticsProcessor {
    private const LOCAL_VERIFICATION_CODE = "aabb33";
    private const DB_FILE_PATH = 'log/monitor.db';
    
    private $db;
    private $kdId;
    private $companyName;
    private $outputMessage;

    public function __construct(string $receivedKey, string $kdId) {
        $this->validateRequest($receivedKey);
        $this->kdId = trim($kdId);
        $this->initializeDatabase();
        $this->processLogistics();
    }

    private function validateRequest(string $receivedKey): void {
        if ($receivedKey !== self::LOCAL_VERIFICATION_CODE) {
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
        
        if ($this->isDuplicateEntry()) {
            $this->outputMessage = "✖重复✖" . $this->companyName;
        } else {
            $this->saveToDatabase();
            $this->outputMessage = $this->companyName ?: "成功-名称未知";
        }
    }

    private function isDuplicateEntry(): bool {
        $stmt = $this->db->prepare("SELECT 1 FROM logs WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $this->kdId, SQLITE3_TEXT);
        $result = $stmt->execute();
        return (bool)$result->fetchArray();
    }

    private function saveToDatabase(): void {
        $stmt = $this->db->prepare("INSERT INTO logs (id, timestamp, company_name) 
                                   VALUES (:id, :timestamp, :company_name)");
        $stmt->bindValue(':id', $this->kdId, SQLITE3_TEXT);
        $stmt->bindValue(':timestamp', date("Y-m-d H:i:s"), SQLITE3_TEXT);
        $stmt->bindValue(':company_name', $this->companyName, SQLITE3_TEXT);
        $stmt->execute();
    }

    public function getOutput(): string {
        return $this->outputMessage;
    }
}

// 使用示例
$processor = new LogisticsProcessor($_GET['key'] ?? '', $_GET['id'] ?? '');
echo $processor->getOutput();
?>