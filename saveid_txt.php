<?php
class LogisticsLogger {
    private const LOCAL_VERIFICATION_CODE = "aabb33";
    private const LOG_FILE_PATH = 'log/monitor.txt';
    
    private $kdId;
    private $logFilePath;

    public function __construct(string $receivedKey, string $kdId) {
        $this->validateRequest($receivedKey);
        $this->kdId = trim($kdId);
        $this->logFilePath = self::LOG_FILE_PATH;
        $this->ensureLogDirectoryExists();
    }

    private function validateRequest(string $receivedKey): void {
        if ($receivedKey !== self::LOCAL_VERIFICATION_CODE) {
            header('HTTP/1.1 400 Bad Request');
            exit('BAD REQUEST');
        }
    }

    private function ensureLogDirectoryExists(): void {
        $dirPath = dirname($this->logFilePath);
        if (!is_dir($dirPath) {
            if (!mkdir($dirPath, 0777, true)) {
                exit("无法创建目录: {$dirPath}");
            }
        }
    }

    public function process(): string {
        if ($this->isDuplicateEntry()) {
            return "✖✖✖重复✖✖✖";
        }
        
        return $this->recordEntry();
    }

    private function isDuplicateEntry(): bool {
        if (!file_exists($this->logFilePath)) {
            return false;
        }

        $fileContent = file_get_contents($this->logFilePath);
        return strpos($fileContent, $this->kdId) !== false;
    }

    private function recordEntry(): string {
        $logEntry = "{$this->kdId}\t" . date("Y-m-d H:i:s") . "\r\n";
        if (file_put_contents($this->logFilePath, $logEntry, FILE_APPEND | LOCK_EX) === false) {
            return "写入失败";
        }
        return "成功";
    }
}

// 使用示例
$logger = new LogisticsLogger($_GET['key'] ?? '', $_GET['id'] ?? '');
echo $logger->process();
?>
