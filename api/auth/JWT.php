<?php
namespace Auth;

class JWT
{
    private $headers;

    private $secret;

    private $exp;

    public function __construct()
    {
        $this->headers = [
            'alg' => 'HS256', // SHA256 as the algorithm
            'typ' => 'JWT', // JSON Web Token as the type
            'iss' => $_ENV['JWT_ISS'], // token issuer
            'aud' => $_ENV['JWT_AUD'] // token audience
        ];
        $this->secret = $_ENV['JWT_SECRET'];
    }

    private function encode(string $str): string
    {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '='); // base64 encode string
    }

    public function generate(array $payload): string
    {
        $headers = $this->encode(json_encode($this->headers)); // encode headers
        $this->exp = time() + 60;
        $payload['exp'] = $this->exp;
        $payload = $this->encode(json_encode($payload)); // encode payload
        $signature = hash_hmac('SHA256', "$headers.$payload", $this->secret, true); // create SHA256 signature
        $signature = $this->encode($signature); // encode signature

        return "$headers.$payload.$signature";
    }

    public function getExp() {
        return date('c', $this->exp);
        // return strtotime(date('c', $this->exp));
    }

    public function is_valid(string $jwt): bool
    {
        $token = explode('.', $jwt); // explode token based on JWT breaks

        if (!isset ($token[1]) && !isset ($token[2])) {
            return false; // fails if the payload and signature is not set
        }

        $headers = base64_decode($token[0]); // decode the header
        $payload = base64_decode($token[1]); // decode the payload
        $clientSignature = $token[2]; // assign the signature

        if (!json_decode($payload)) {
            return false; // fails if payload does not decode
        }

        if ((json_decode($payload)->exp - time()) < 0) {
            return false; // fails if expiration is greater than 0
        }

        if (isset (json_decode($payload)->iss)) {
            if (json_decode($headers)->iss != json_decode($payload)->iss) {
                return false; // fails if issuers are not the same
            }
        } else {
            return false; // fails if issuer is not set 
        }

        if (isset (json_decode($payload)->aud)) {
            if (json_decode($headers)->aud != json_decode($payload)->aud) {
                return false; // fails if audiences are not the same
            }
        } else {
            return false; // fails if audience is not set
        }

        $base64_header = $this->encode($headers);
        $base64_payload = $this->encode($payload);

        $signature = hash_hmac('SHA256', $base64_header . "." . $base64_payload, $this->secret, true);
        $base64_signature = $this->encode($signature);

        return ($base64_signature === $clientSignature);
    }
}
