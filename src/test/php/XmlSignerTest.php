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

use PHPUnit\Framework\TestCase;

class XmlSignerTest extends TestCase
{
    /**
     * @var XmlSigner
     */
    private $signer;

    /**
     * @var XmlVerifier
     */
    private $verifier;

    private $certificateAndPrivateKey;

    protected function setUp()
    {
        $this->signer = new XmlSigner();
        $this->verifier = new XmlVerifier(array("src/test/resources/certs/ca.pem"));
        $this->certificateAndPrivateKey = file_get_contents("src/test/resources/certs/signing-certificate.pem");
    }

    /**
     * @expectedException \Example\XmlSignatureException
     * @expectedExceptionMessage Failed signing XML
     */
    public function testSignInvalidXmlThrowsException()
    {
        $this->signer->sign("Not a XML document.", $this->certificateAndPrivateKey);
    }

    public function testSignXmlSucceeds()
    {
        $xmlString = '<?xml version="1.0" encoding="UTF-8"?><document><content>Do not manipulate!</content></document>';


        $signedDoc = $this->signer->sign($xmlString, $this->certificateAndPrivateKey);
        $verifiedDoc = $this->verifier->verify($signedDoc->saveXML());

        self::assertNotNull($verifiedDoc, "XML signature is invalid");
    }
}
