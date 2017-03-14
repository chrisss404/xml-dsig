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

class SignedInfo
{
    const REFERENCE = "Reference";
    const SIGNATURE_METHOD = "SignatureMethod";

    private $canonicalData;
    private $references;
    private $signatureMethod;

    /**
     * SignedInfo constructor.
     * @param $signedInfo \DOMElement
     */
    public function __construct($signedInfo)
    {
        if (empty($signedInfo)) {
            throw new \InvalidArgumentException("signedInfo is empty");
        }

        $this->canonicalData = $signedInfo->C14N();
        $this->references = $signedInfo->getElementsByTagNameNS(Signature::XMLNS, static::REFERENCE);
        $this->signatureMethod = $this->extractSignatureMethod($signedInfo);
    }

    /**
     * @param $element \DOMElement
     * @return SignatureMethod
     */
    private function extractSignatureMethod($element)
    {
        $nodeList = $element->getElementsByTagNameNS(Signature::XMLNS, static::SIGNATURE_METHOD);
        if ($nodeList->length === 0) {
            throw new \InvalidArgumentException("Cannot find SignatureMethod element");
        }
        return new SignatureMethod($nodeList->item(0));
    }

    /**
     * @return \DOMNodeList
     */
    public function getReferences()
    {
        return $this->references;
    }

    /**
     * @return SignatureMethod
     */
    public function getSignatureMethod()
    {
        return $this->signatureMethod;
    }

    /**
     * @return string
     */
    public function getCanonicalData()
    {
        return $this->canonicalData;
    }
}
