<?php
namespace Utils;

class UUIDGenerator {
    public static function generateV4() {
        $data = random_bytes(16); // Gera 16 bytes aleatórios
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Define versão 4
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Define variante
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}