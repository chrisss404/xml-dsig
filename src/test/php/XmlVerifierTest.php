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

class XmlVerifierTest extends TestCase
{
    /**
     * @var XmlVerifier
     */
    private $verifier;

    protected function setUp()
    {
        $this->verifier = new XmlVerifier(array("src/test/resources/certs/ca.pem"));
    }

    /**
     * @expectedException \Example\XmlVerificationException
     * @expectedExceptionMessage Signature is invalid
     */
    public function testVerifyingNullAsXmlInputThrowsException()
    {
        $this->verifier->verify(null);
    }

    /**
     * @expectedException \Example\XmlVerificationException
     * @expectedExceptionMessage Signature is invalid: Invalid XML input
     */
    public function testVerifyingEmptyAsXmlInputThrowsException()
    {
        $this->verifier->verify("");
    }

    /**
     * @expectedException \Example\XmlVerificationException
     * @expectedExceptionMessage Signature is invalid: Invalid XML input
     */
    public function testVerifyingUnsupportedInputRepresentationThrowsException()
    {
        $this->verifier->verify("Not a XML document.");
    }

    /**
     * @expectedException \Example\XmlVerificationException
     * @expectedExceptionMessage Signature is invalid
     */
    public function testVerifyingManipulatedXmlThrowsException()
    {
        $xmlString = file_get_contents("src/test/resources/manipulated.xml");
        $this->verifier->verify($xmlString);
    }

    /**
     * @expectedException \Example\XmlVerificationException
     * @expectedExceptionMessage Signature is invalid
     */
    public function testVerifyingXmlWithInvalidSignatureThrowsException()
    {
        $xmlString = file_get_contents("src/test/resources/invalid-signature.xml");
        $this->verifier->verify($xmlString);
    }

    /**
     * @expectedException \Example\XmlVerificationException
     * @expectedExceptionMessage Signature is invalid
     */
    public function testVerifyingXmlWithValidDigestAndInvalidSignatureThrowsException()
    {
        $xmlString = file_get_contents("src/test/resources/valid-digest-but-invalid-signature.xml");
        $this->verifier->verify($xmlString);
    }

    /**
     * @expectedException \Example\XmlVerificationException
     * @expectedExceptionMessage Signature is invalid: Unknown algorithm
     */
    public function testVerifyingXmlWithUnsupportedSignatureAlgorithmThrowsException()
    {
        $xmlString = file_get_contents("src/test/resources/unsupported-signature-algorithm.xml");
        $this->verifier->verify($xmlString);
    }

    /**
     * @expectedException \Example\XmlVerificationException
     * @expectedExceptionMessage Signature is invalid: Cannot find Signature element
     */
    public function testVerifyingNotSignedXmlThrowsException()
    {
        $xmlString = file_get_contents("src/test/resources/not-signed.xml");
        $this->verifier->verify($xmlString);
    }

    /**
     * @expectedException \Example\XmlVerificationException
     * @expectedExceptionMessage Invalid certificate
     */
    public function testVerifyingXmlSignedWithUnverifiableCertificateThrowsException()
    {
        $xmlString = file_get_contents("src/test/resources/signed-with-unverifiable-certificate.xml");
        $this->verifier->verify($xmlString);
    }

    /**
     * @expectedException \Example\XmlVerificationException
     * @expectedExceptionMessage Signature is invalid: Unknown algorithm
     */
    public function testVerifyingXmlSignedWithUnrecommendedDigestAlgorithmThrowsException()
    {
        $xmlString = file_get_contents("src/test/resources/unrecommended-digest-algorithm.xml");
        $this->verifier->verify($xmlString);
    }

    public function testVerifyingValidXmlSucceeds()
    {
        $xmlString = file_get_contents("src/test/resources/valid.xml");
        self::assertNotNull($this->verifier->verify($xmlString), "XML signature is invalid");
    }
}
