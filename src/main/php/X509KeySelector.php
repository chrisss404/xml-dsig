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

use Example\XmlDSig\KeySelector;
use Example\XmlDSig\Signature;

class X509KeySelector implements KeySelector
{

    private $trustedCAs;

    /**
     * X509KeySelector constructor.
     * @param $trustedCAs array
     */
    public function __construct($trustedCAs)
    {
        $this->trustedCAs = $trustedCAs;
    }

    /**
     * @param $keyInfo \DOMNodeList
     * @return string
     */
    public function select($keyInfo)
    {
        $cert = $keyInfo->item(0)->getElementsByTagNameNS(Signature::XMLNS, "X509Certificate");

        $cert = str_replace(array("\r", "\n"), "", $cert->item(0)->textContent);
        $cert = chunk_split($cert, 64, "\n");

        $cert = sprintf("%s\n%s%s\n", KeySelector::BEGIN_CERT, $cert, KeySelector::END_CERT);
        return $this->validateCertificate($cert);
    }

    /**
     * @param $cert string
     * @return string
     */
    private function validateCertificate($cert)
    {
        if (!openssl_x509_checkpurpose($cert, X509_PURPOSE_SMIME_SIGN, $this->trustedCAs)) {
            throw new XmlVerificationException("Invalid certificate");
        }
        return $cert;
    }
}
