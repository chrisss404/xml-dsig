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

class SignatureValue
{
    private $value;
    private $validated = false;
    private $validationState;

    /**
     * XmlSignatureValue constructor.
     * @param $signatureValueElement \DOMElement
     */
    public function __construct($signatureValueElement)
    {
        if (empty($signatureValueElement)) {
            throw new \InvalidArgumentException("signatureValue is empty");
        }
        $this->value = base64_decode($signatureValueElement->textContent);
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
            $signature = $validateContext->getSignature();
            $signatureMethod = $signature->getSignedInfo()->getSignatureMethod();
            $validationKey = $validateContext->getKeySelector()->select($signature->getKeyInfo());
            $signedInfo = $signature->getSignedInfo();

            $this->validationState = $signatureMethod->isValid($validationKey, $signedInfo, $this->value);
            $this->validated = true;
            return $this->validationState;
        }
    }
}
