<?php
	// 类判断物流公司
class LogisticsIdentifier {
    private $text;

    public function __construct($text) {
        $this->text = $text;
    }

    private function getTextLength() {
        return mb_strlen($this->text, 'UTF-8');
    }

    private function getTextLeft($length) {
        return mb_substr($this->text, 0, $length, 'UTF-8');
    }

    public function identifyLogisticsCompany() {
        $cd = $this->getTextLength();
        $zuo1 = $this->getTextLeft(1);
        $zuo2 = $this->getTextLeft(2);

        if ($cd == 15) {
            if ($zuo1 == "7") return "申通";
            elseif ($zuo1 == "3" || $zuo1 == "4") return "韵达";
            elseif ($zuo1 == "6") return "丹鸟";
        } elseif ($cd == 14) {
            if ($zuo1 == "7") return "中通";
        } elseif ($cd == 13) {
            if ($zuo1 == "9") return "邮政";
        }

        if ($zuo2 == "JD") return "京东";
        elseif ($zuo2 == "JT") return "极兔";
        elseif ($zuo2 == "SF") return "顺丰";
        elseif ($zuo2 == "YT") return "圆通";

        return ""; // 如果没有匹配的物流公司
    }
}
?>
