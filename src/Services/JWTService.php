<?php
namespace App\Services;

use \Firebase\JWT\JWT;

/**
 * Class JWTService
 * @package App\Services
 */
class JWTService
{
    const HS256 = 'HS256';
    const RS256 = 'RS256';

    /**  @var JWT */
    public $jwt;

    /** @var string */
    public $privateKey;

    /** @var string */
    public $publicKey;

    /** @var array */
    public $jwtConf;

    /**
     * TokenService constructor.
     * @param $jwtConf
     */
    public function __construct($jwtConf)
    {
        $this->jwtConf = $jwtConf;
        $this->jwt = new JWT();

        $this->privateKey = file_get_contents($this->jwtConf['secret_key']);
        $this->publicKey  = file_get_contents($this->jwtConf['public_key']);
    }


    public function test()
    {
        $data = ['id'=> '1', 't_1' => 1];

        $token = $this->generateToken($data);
        echo $token;

        $valid = $this->validate($token);
        dump($valid);

        $res = $this->decode($token);

        dump($res);
        exit;
    }

    /**
     * @param array $data
     * @return string
     */
    public function generateToken(array $data):string
    {
        $time = time()+$this->jwtConf['token_ttl'];
        $payload = [
            "alg" => self::RS256,
            "typ" => "JWT",
//            "iss" => "example.com", // (issuer) — определяет приложение, из которого отправляется токен.
//            "aud" => "example.com",
            "iat" => time(),
            "nbf" => time(),
            "data" => json_encode($data),
            "sub" => $time, "sub", // sub (subject) — определяет тему токена.
            "exp" => $time, // exp (expiration time) — время жизни токена.
        ];

        $token =  $this->jwt->encode($payload, $this->privateKey, self::RS256);

        return $token;
    }

    /**
     * @param $token
     * @return bool
     */
    public function validate($token):bool
    {
        try {
            $res = $this->jwt->decode($token, $this->publicKey, [self::RS256]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $token
     * @return array|null
     */
    public function decode($token)
    {
        try {
            $decoded = (array) $this->jwt->decode($token, $this->publicKey, [self::RS256]);
            if($decoded['data']) {
                $decoded['data'] = json_decode($decoded['data'], true);
            }
            return $decoded;
        } catch (\Exception $e) {
            // dump('Выброшено исключение: ',  $e->getMessage());
            return null;
        }
    }

}