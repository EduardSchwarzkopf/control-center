<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use DateInterval;
use DateTimeImmutable;

// generate a a key pair with: 
// openssl genrsa -out storage/private.key 2048 && openssl rsa -in storage/private.key -outform PEM -pubout -out storage/public.key

class JWTService
{

    public $issuer = "";
    public $issuedAt = null;
    public $subject = "";
    public $permittedFor = "";
    public $expires = null;
    public $data = [];
    public $token = null;

    public function __construct(
        string $issuer = "",
        string $permittedFor = "",
        string $subject = "",
        int $expiresAfter = 60,
    ) {
        $this->issuedBy($issuer);
        $this->issuedAt();
        $this->expiresAfter($expiresAfter);
        $this->subject($subject);
        $this->permittedFor($permittedFor);
    }

    public function expiresAfter(int $seconds = 60): JWTService
    {
        $this->expires = new DateTimeImmutable();
        if (!$seconds instanceof DateInterval) {
            $seconds = new DateInterval(sprintf('PT%sS', $seconds));
        }
        $this->expires = $this->expires->add($seconds)->getTimestamp();
        return $this;
    }

    private function issuedBy(string $issuer = ''): JWTService
    {
        $this->issuer = empty($issuer) ? config('app.url') : $issuer;

        return $this;
    }

    public function permittedFor(string $url = ''): JWTService
    {
        $this->permittedFor = empty($url) ? config('app.url') : $url;

        return $this;
    }

    public function subject(string $subject = ''): JWTService
    {
        $this->subject = empty($subject) ? config('app.name') : $subject;

        return $this;
    }

    public function payload(array $payload = []): JWTService
    {
        $this->data = $payload;

        return $this;
    }

    private function issuedAt(): JWTService
    {

        $date = CarbonImmutable::now()->getTimestamp();
        $this->issuedAt = $date;

        return $this;
    }

    public static function base64url_encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64url_decode(string $data): string
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    public function createToken(array $payloadData = []): string
    {

        $keyPrivatePassword = config('app.key');
        $keyPrivatePath =  storage_path() . '/private.key';
        $cryptMaxCharsValue = 245;

        // Definition of header

        $header = [
            'alg' => 'RSA',
            'typ' => 'JWT'
        ];

        $header = json_encode($header);
        $header = JWTService::base64url_encode($header);

        $this->issuedAt();

        $payload = [
            'iss' => $this->issuer, // The issuer of the token
            'iat' => $this->issuedAt, // When was the token issued
            'sub' => $this->subject, // The subject of the token
            'exp' => $this->expires, // This will define the expiration in NumericDate value. The expiration MUST be after the current date/time.
            'data' => $payloadData
        ];


        $payload = json_encode($payload);
        $payload = JWTService::base64url_encode($payload);

        // START ENCRYPT SIGN JWT

        $data = $header . "." . $payload;

        // Open private path and return this in string format

        $fp = fopen($keyPrivatePath, "r");
        $keyPrivateString = fread($fp, 8192);
        fclose($fp);

        // Open private key string and return 'resourse'

        if (isset($keyPrivatePassword)) {
            $resPrivateKey = openssl_get_privatekey($keyPrivateString, $keyPrivatePassword);
        } else {
            $resPrivateKey = openssl_get_privatekey($keyPrivateString);
        }


        // Crypt data in parts if necessary. When char limit of data is upper than 'cryptMaxCharsValue'.

        $rawDataSource = $data;

        $partialData = '';
        $encodedData = '';
        $split = str_split($rawDataSource, $cryptMaxCharsValue);
        foreach ($split as $part) {
            openssl_private_encrypt($part, $partialData, $resPrivateKey);

            $encodedData .= (strlen($encodedData) > 0 ? '.' : '') . JWTService::base64url_encode($partialData);
        }


        // Encode base64 again to remove dots (Dots are used in JWT syntaxe)

        $encodedData = JWTService::base64url_encode($encodedData);

        $signature = $encodedData;

        $JWTToken = $header . "." . $payload . "." . $signature;

        $this->token = $JWTToken;

        return $JWTToken;
    }
}
