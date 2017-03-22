<?php
/*
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
 */

namespace Example\XmlDSig;

class SignatureMethod
{
    const ALGORITHM = "Algorithm";
    const RSA_SHA_1 = "http://www.w3.org/2000/09/xmldsig#rsa-sha1";
    const RSA_SHA_256 = "http://www.w3.org/2001/04/xmldsig-more#rsa-sha256";
    const RSA_SHA_512 = "http://www.w3.org/2001/04/xmldsig-more#rsa-sha512";

    private $method;

    /**
     * SignatureMethod constructor.
     * @param $signatureMethod \DOMElement
     */
    public function __construct($signatureMethod)
    {
        if (empty($signatureMethod)) {
            throw new \InvalidArgumentException("signatureMethod is empty");
        }

        $this->method = static::unmarshalSignatureMethod($signatureMethod->getAttribute(static::ALGORITHM));
    }

    /**
     * @param $validationKey string
     * @param $signedInfo SignedInfo
     * @param $signature string
     * @return bool
     */
    public function isValid($validationKey, $signedInfo, $signature)
    {
        $publicKey = openssl_get_publickey($validationKey);
        $validationState = openssl_verify($signedInfo->getCanonicalData(), $signature, $publicKey, $this->method) === 1;
        openssl_pkey_free($publicKey);

        return $validationState;
    }

    /**
     * @param $signatureMethod string
     * @return int
     */
    private static function unmarshalSignatureMethod($signatureMethod)
    {
        switch ($signatureMethod) {
            case static::RSA_SHA_1:
                return OPENSSL_ALGO_SHA1;
            case static::RSA_SHA_256:
                return OPENSSL_ALGO_SHA256;
            case static::RSA_SHA_512:
                return OPENSSL_ALGO_SHA512;
            default:
                throw new \DomainException("Unknown algorithm");
        }
    }
}
