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

namespace Example;

use DomainException;
use Example\XmlDSig\ValidateContext;
use InvalidArgumentException;

class XmlVerifier
{
    const SIGNATURE_IS_INVALID = "Signature is invalid";
    const SIGNATURE_IS_INVALID_BECAUSE_OF = "Signature is invalid: %s";

    /**
     * @param $xmlString string
     * @return \SimpleXMLElement
     */
    public function verify($xmlString)
    {
        try {
            $doc = Utility::loadXmlDocument($xmlString);
            $validateContext = new ValidateContext($doc, new X509KeySelector());

            if (!$validateContext->isValid()) {
                throw new XmlVerificationException(static::SIGNATURE_IS_INVALID);
            }
            return simplexml_import_dom($doc);
        } catch (DomainException $exception) {
            throw new XmlVerificationException(
                sprintf(static::SIGNATURE_IS_INVALID_BECAUSE_OF, $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        } catch (InvalidArgumentException $exception) {
            throw new XmlVerificationException(
                sprintf(static::SIGNATURE_IS_INVALID_BECAUSE_OF, $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        } catch (InvalidDocumentException $exception) {
            throw new XmlVerificationException(
                sprintf(static::SIGNATURE_IS_INVALID_BECAUSE_OF, $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }
}
