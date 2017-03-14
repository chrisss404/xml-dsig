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

class Reference
{
    const ALGORITHM = "Algorithm";
    const DIGEST_METHOD = "DigestMethod";
    const DIGEST_VALUE = "DigestValue";
    const ENVELOPED_SIGNATURE = "http://www.w3.org/2000/09/xmldsig#enveloped-signature";
    const SIGNATURE = "Signature";
    const TRANSFORM = "Transform";
    const URI = "URI";
    const XMLENC_SHA_256 = "http://www.w3.org/2001/04/xmlenc#sha256";
    const XMLENC_SHA_512 = "http://www.w3.org/2001/04/xmlenc#sha512";

    private $uri;
    private $digestMethod;
    private $digestValue;
    private $transforms;

    /**
     * XmlReference constructor.
     * @param $reference \DOMElement
     */
    public function __construct($reference)
    {
        if (empty($reference)) {
            throw new \InvalidArgumentException("reference is empty");
        }
        $this->uri = $reference->getAttribute(static::URI);

        $digestValue = $reference->getElementsByTagNameNS(Signature::XMLNS, static::DIGEST_VALUE);
        if ($digestValue->length === 0) {
            throw new \InvalidArgumentException("digestValue is missing");
        }
        $this->digestValue = $digestValue->item(0)->textContent;

        $digestMethod = $reference->getElementsByTagNameNS(Signature::XMLNS, static::DIGEST_METHOD);
        if ($digestMethod->length === 0) {
            throw new \InvalidArgumentException("digestMethod is missing");
        }
        $this->digestMethod = static::unmarshalDigestMethod($digestMethod->item(0)->getAttribute(static::ALGORITHM));

        $transforms = $reference->getElementsByTagNameNS(Signature::XMLNS, static::TRANSFORM);
        if ($digestMethod->length === 0) {
            throw new \InvalidArgumentException("transforms are missing");
        }
        $this->transforms = $transforms;
    }

    /**
     * @param $validateContext ValidateContext
     * @return bool
     */
    public function isValid($validateContext)
    {
        $doc = $this->dereference($validateContext);
        $data = $this->transform($doc);
        $calculatedDigestValue = base64_encode(hex2bin(hash($this->digestMethod, $data)));
        return hash_equals($this->digestValue, $calculatedDigestValue);
    }

    /**
     * @param $validateContext ValidateContext
     * @return \DOMDocument
     */
    private function dereference($validateContext)
    {
        if ($this->uri === "") {
            $doc = new \DOMDocument();
            $doc->loadXML($validateContext->getDoc()->saveXML());
            return $doc;
        }

        throw new \DomainException("Unsupported reference uri");
    }

    /**
     * @param $doc \DOMDocument
     * @return string
     */
    private function transform($doc)
    {
        for ($i = 0; $i < $this->transforms->length; $i++) {
            if ($this->transforms->item($i)->getAttribute(static::ALGORITHM) === static::ENVELOPED_SIGNATURE) {
                $signatureNode = $doc->getElementsByTagNameNS(Signature::XMLNS, static::SIGNATURE)->item(0);
                $doc->documentElement->removeChild($signatureNode);
            }
        }

        return $doc->C14N();
    }

    /**
     * @param $digestMethod string
     * @return string
     */
    private static function unmarshalDigestMethod($digestMethod)
    {
        switch ($digestMethod) {
            case static::XMLENC_SHA_256:
                return "SHA256";
            case static::XMLENC_SHA_512:
                return "SHA512";
            default:
                throw new \DomainException("Unknown algorithm");
        }
    }
}
