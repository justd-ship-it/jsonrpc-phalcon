<?php

namespace ShortUrl\Lib;


/**
 * Class HashId
 *
 * Используется для генерации и расшифровки кодов по ID
 */
class HashId
{
    /** @var string Алфавит используемый для создания ключей */
    const ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /** @var array Текущий ключ для шифровки/расшифровки */
    protected $key;

    /**
     * Генерирует Json ключ для шифровки
     * @return false|string
     */
    public function generateKey()
    {
        $arr = str_split(self::ALPHABET);

        $key = [];

        foreach ($arr as $char) {
            $key[$char] = str_shuffle(self::ALPHABET);
        }

        return json_encode($key);
    }

    /**
     * Устанавливает Ключ для шифровки/дешифровки Id
     * @param string $key
     */
    public function setKey(string $key)
    {
        $this->key = json_decode($key, 1);
    }

    /**
     * Возвращает случайный ключ из набора ключей
     * @return mixed
     */
    public function getRandomKey()
    {
        $keys = array_keys($this->key);
        return $keys[rand(0, count($keys) - 1)];
    }

    /**
     * @param $id
     * @param null $key
     * @return array
     */
    public function createHash($id, $key = null)
    {
        $currentKey = $key ?: $this->getRandomKey();

        $chars = str_split($this->key[$currentKey]);
        $alphabetCount = count($chars);

        $hash = '';
        while ($id > $alphabetCount - 1) {
            $hash = $chars[intval(fmod($id, $alphabetCount))] . $hash;
            $id = (int)floor($id / $alphabetCount);
        }

        $hash = $currentKey . $this->key[$currentKey][$id] . $hash;
        return [
            'key' => (string)$currentKey,
            'hash' => $hash
        ];
    }

    /**
     * Расшифровывает хэш для дальнейших проверок
     * @param $hash
     * @return array
     * @throws Exception
     */
    public function decryptHash($hash)
    {
        $stack = str_split($hash);
        $itemId = null;

        $currentKey = array_shift($stack);

        if (!isset($this->key[$currentKey])) {
            throw new Exception();
        }

        $alphabetCount = strlen($this->key[$currentKey]);

        $reverse = array_reverse($stack);

        $multiplier = 0;
        while (count($reverse)) {
            $pos = strpos($this->key[$currentKey], array_shift($reverse));

            if ($pos === false) {
                throw new Exception($pos);
            }

            $itemId += $pos * pow($alphabetCount, $multiplier);

            $multiplier++;
        }

        return [
            'key' => $currentKey,
            'id' => $itemId
        ];
    }
}

