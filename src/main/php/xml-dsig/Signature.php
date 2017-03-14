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

class Signature
{
    const KEY_INFO = "KeyInfo";
    const SIGNATURE_VALUE = "SignatureValue";
    const SIGNED_INFO = "SignedInfo";
    const XMLNS = "http://www.w3.org/2000/09/xmldsig#";

    private $signatureValue;
    private $keyInfo;
    private $signedInfo;
    private $validationState;
    private $validated = false;

    /**
     * XmlSignature constructor.
     * @param $signElem \DOMElement
     */
    public function __construct($signElem)
    {
        $this->signedInfo = $this->extractSignedInfo($signElem);
        $this->signatureValue = $this->extractSignatureValue($signElem);
        $this->keyInfo = $signElem->getElementsByTagNameNS(static::XMLNS, static::KEY_INFO);
    }

    /**
     * @param $element \DOMElement
     * @return SignedInfo
     */
    private function extractSignedInfo($element)
    {
        $nodeList = $element->getElementsByTagNameNS(static::XMLNS, static::SIGNED_INFO);
        if ($nodeList->length === 0) {
            throw new \InvalidArgumentException("Cannot find SignedInfo element");
        }
        return new SignedInfo($nodeList->item(0));
    }

    /**
     * @param $element \DOMElement
     * @return SignatureValue
     */
    private function extractSignatureValue($element)
    {
        $nodeList = $element->getElementsByTagNameNS(static::XMLNS, static::SIGNATURE_VALUE);
        if ($nodeList->length === 0) {
            throw new \InvalidArgumentException("Cannot find SignatureValue element");
        }
        return new SignatureValue($nodeList->item(0));
    }

    /**
     * @param $validateContext ValidateContext
     * @return bool
     */
    public function isValid($validateContext)
    {
        if (empty($validateContext)) {
            throw new \InvalidArgumentException("validateContext is empty");
        } elseif ($this->validated) {
            return $this->validationState;
        } else {
            $sigValidity = $this->signatureValue->isValid($validateContext);

            if (!$sigValidity) {
                $this->validationState = false;
                $this->validated = true;
                return false;
            } else {
                $references = $this->signedInfo->getReferences();

                $this->validationState = true;
                for ($i = 0; $i < $references->length; $i++) {
                    $ref = new Reference($references->item($i));
                    $this->validationState = $this->validationState && $ref->isValid($validateContext);
                }

                $this->validated = true;
                return $this->validationState;
            }
        }
    }

    /**
     * @return SignedInfo
     */
    public function getSignedInfo()
    {
        return $this->signedInfo;
    }

    /**
     * @return \DOMNodeList
     */
    public function getKeyInfo()
    {
        return $this->keyInfo;
    }
}
