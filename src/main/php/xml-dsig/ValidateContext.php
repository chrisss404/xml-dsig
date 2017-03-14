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

class ValidateContext
{
    const SIGNATURE = "Signature";

    private $doc;
    private $keySelector;
    private $signature;

    /**
     * ValidateContext constructor.
     * @param $doc \DOMDocument
     * @param $keySelector KeySelector
     */
    public function __construct($doc, $keySelector)
    {
        if (empty($doc)) {
            throw new \InvalidArgumentException("doc is empty");
        }
        $this->doc = $doc;

        if (empty($keySelector)) {
            throw new \InvalidArgumentException("keySelector is empty");
        }
        $this->keySelector = $keySelector;
        $this->signature = $this->extractSignature($doc);
    }

    /**
     * @param $doc \DOMDocument
     * @return Signature
     */
    private function extractSignature($doc)
    {
        $signatureNode = $doc->getElementsByTagNameNS(Signature::XMLNS, static::SIGNATURE);
        if ($signatureNode->length === 0) {
            throw new \InvalidArgumentException("Cannot find Signature element");
        }

        $node = $signatureNode->item(0);
        $node->normalize();

        $element = $node;
        if ($node->nodeType == XML_DOCUMENT_NODE) {
            $element = $node->ownerDocument;
        }
        if ($node->nodeType != XML_ELEMENT_NODE) {
            throw new \InvalidArgumentException("Signature element is not a proper node");
        }

        $tag = $element->localName;
        if ($tag === static::SIGNATURE) {
            return new Signature($element);
        } else {
            throw new \InvalidArgumentException("invalid signature tag: " . $tag);
        }
    }

    public function isValid()
    {
        return $this->getSignature()->isValid($this);
    }

    /**
     * @return \DOMDocument
     */
    public function getDoc()
    {
        return $this->doc;
    }

    /**
     * @return KeySelector
     */
    public function getKeySelector()
    {
        return $this->keySelector;
    }

    /**
     * @return Signature
     */
    public function getSignature()
    {
        return $this->signature;
    }
}
