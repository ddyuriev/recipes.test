<?php
/**
 * Класс для хеширования паролей
 *
 */

namespace App\Http\Controllers\Utils;

//use core\exceptions\RuntimeException;

/**
 * Класс для хеширования паролей
 *
 * Основан на "PHP password hashing framework", http://www.openwall.com/phpass/
 * Алгоритм вроде остался неизменным, так что пароли должны нормально
 * сравниваться в случае использования исходного класса
 *
 */
class PasswordHasher
{
    const MIN_ITERATIONS_LOG2 = 7;

    const MAX_ITERATIONS_LOG2 = 30;

    const SALT_LENGTH = 6;

    private $_itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    private $_iterationsLog2;

    private $_randomState;

    public function __construct($iterationsLog2 = 12)
    {
        $this->_iterationsLog2 = ($iterationsLog2 < self::MIN_ITERATIONS_LOG2 ||
                                  $iterationsLog2 > self::MAX_ITERATIONS_LOG2)?
                                 12: $iterationsLog2;

        $this->_randomState = \microtime() . \getmypid();
    }


    private function _getRandomBytes($count)
    {
        $output = '';
        if (($fh = @\fopen('/dev/urandom', 'rb'))) {
            $output = \fread($fh, $count);
            \fclose($fh);
        }

        if (\strlen($output) < $count) {
            $output = '';
            for ($i = 0; $i < $count; $i += 16) {
                $this->_randomState = \md5(\microtime() . $this->_randomState);
                $output .= \md5($this->_randomState, true);
            }
            $output = \substr($output, 0, $count);
        }

        return $output;
    }


    private function _generateSalt()
    {
        $random = $this->_getRandomBytes(self::SALT_LENGTH);

        return '$P$' . $this->_itoa64[$this->_iterationsLog2] .
               $this->_encode64($random, self::SALT_LENGTH);
    }


    private function _encode64($input, $count)
    {
        $output = '';
        $i      = 0;
        do {
            $value   = \ord($input[$i++]);
            $output .= $this->_itoa64[$value & 0x3f];
            if ($i < $count) {
                $value |= \ord($input[$i]) << 8;
            }
            $output .= $this->_itoa64[($value >> 6) & 0x3f];
            if ($i++ >= $count) {
                break;
            }
            if ($i < $count) {
                $value |= \ord($input[$i]) << 16;
            }
            $output .= $this->_itoa64[($value >> 12) & 0x3f];
            if ($i++ >= $count) {
                break;
            }
            $output .= $this->_itoa64[($value >> 18) & 0x3f];
        } while ($i < $count);

        return $output;
    }


    private function _crypt($password, $setting)
    {
        if (\substr($setting, 0, 3) != '$P$') {
            return '';
        }

        $count_log2 = \strpos($this->_itoa64, $setting[3]);
        if ($count_log2 < self::MIN_ITERATIONS_LOG2 || $count_log2 > self::MAX_ITERATIONS_LOG2) {
            return '';
        }
        $count = 1 << $count_log2;

        $encodedSaltLength = \intval(self::SALT_LENGTH * 8 / 6);
        if ($encodedSaltLength != \strlen($salt = \substr($setting, 4, $encodedSaltLength))) {
            return '';
        }

        # We're kind of forced to use MD5 here since it's the only
        # cryptographic primitive available in all versions of PHP
        # currently in use.  To implement our own low-level crypto
        # in PHP would result in much worse performance and
        # consequently in lower iteration counts and hashes that are
        # quicker to crack (by non-PHP code).
        $hash = \md5($salt . $password, TRUE);
        do {
            $hash = \md5($hash . $password, TRUE);
        } while (--$count);

        return \substr($setting, 0, 12) . $this->_encode64($hash, 16);
    }


    public function hash($password)
    {
        $hash = $this->_crypt($password, $this->_generateSalt());
        if (\strlen($hash) == 34) {
            return $hash;
        }

//        throw new RuntimeException('Ошибка при хешировании пароля, результат: ' . $hash);
    }


    public function check($password, $storedHash)
    {
        return $this->_crypt($password, $storedHash) == $storedHash;
    }
}

?>
