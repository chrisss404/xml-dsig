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

class XmlSigner
{
    /**
     * @param $xmlString string
     * @param $cert string
     * @return \SimpleXMLElement
     */
    public function sign($xmlString, $cert)
    {
        try {
            $doc = Utility::loadXmlDocument($xmlString);

            $signedInfoNode = $this->buildSignedInfo($doc);
            $keyInfoNode = $this->buildKeyInfo($doc, $cert);
            $signatureNode = $this->buildSignature($doc, $signedInfoNode, $keyInfoNode);
            $doc->documentElement->appendChild($signatureNode);

            $privateKey = openssl_pkey_get_private($cert);
            $signed = openssl_sign($signedInfoNode->C14N(), $signature, $privateKey, OPENSSL_ALGO_SHA256);
            openssl_pkey_free($privateKey);

            if ($signed) {
                $signatureValueNode = $doc->createElementNS(
                    Signature::XMLNS,
                    "SignatureValue",
                    base64_encode($signature)
                );
                $signatureNode->appendChild($signatureValueNode);
                $doc->documentElement->appendChild($signatureNode);
            } else {
                throw new XmlSignatureException("Failed signing XML " . openssl_error_string());
            }
        } catch (InvalidDocumentException $exception) {
            throw new XmlSignatureException("Failed signing XML", $exception->getCode(), $exception);
        }

        return simplexml_import_dom($doc);
    }

    /**
     * @param $doc \DOMDocument
     * @param $signedInfo \DOMElement
     * @param $keyInfo \DOMElement
     * @return \DOMElement
     */
    private function buildSignature($doc, $signedInfo, $keyInfo)
    {
        $signatureNode = $doc->createElementNS(Signature::XMLNS, "Signature");
        $signatureNode->appendChild($signedInfo);
        $signatureNode->appendChild($keyInfo);
        return $signatureNode;
    }

    /**
     * @param $doc \DOMDocument
     * @return \DOMElement
     */
    private function buildSignedInfo($doc)
    {
        $transformsNode = $doc->createElementNS(Signature::XMLNS, "Transforms");
        $transformsNode->appendChild($this->createAlgorithmNode(
            $doc,
            "Transform",
            "http://www.w3.org/2000/09/xmldsig#enveloped-signature"
        ));

        $referenceNode = $doc->createElementNS(Signature::XMLNS, "Reference");
        $referenceNode->appendChild($doc->createAttribute("URI"));
        $referenceNode->appendChild($transformsNode);
        $referenceNode->appendChild($this->createAlgorithmNode(
            $doc,
            "DigestMethod",
            "http://www.w3.org/2001/04/xmlenc#sha256"
        ));
        $referenceNode->appendChild($doc->createElementNS(
            Signature::XMLNS,
            "DigestValue",
            base64_encode(hex2bin(hash("sha256", $doc->C14N())))
        ));

        $signedInfoNode = $doc->createElementNS(Signature::XMLNS, "SignedInfo");
        $signedInfoNode->appendChild($this->createAlgorithmNode(
            $doc,
            "CanonicalizationMethod",
            "http://www.w3.org/TR/2001/REC-xml-c14n-20010315"
        ));
        $signedInfoNode->appendChild($this->createAlgorithmNode(
            $doc,
            "SignatureMethod",
            "http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"
        ));
        $signedInfoNode->appendChild($referenceNode);

        return $signedInfoNode;
    }

    /**
     * @param $doc \DOMDocument
     * @param $cert string
     * @return \DOMElement
     */
    private function buildKeyInfo($doc, $cert)
    {
        $certInfo = openssl_x509_parse($cert);
        $x509SubjectNameNode = $doc->createElementNS(Signature::XMLNS, "X509SubjectName", $certInfo["name"]);

        openssl_x509_export($cert, $exportedCert);
        $valuesToReplace = array("\r", "\n", KeySelector::BEGIN_CERT, KeySelector::END_CERT);
        $exportedCert = str_replace($valuesToReplace, "", $exportedCert);
        $x509CertificateNode = $doc->createElementNS(Signature::XMLNS, "X509Certificate", $exportedCert);

        $x509DataNode = $doc->createElementNS(Signature::XMLNS, "X509Data");
        $x509DataNode->appendChild($x509SubjectNameNode);
        $x509DataNode->appendChild($x509CertificateNode);

        $keyInfoNode = $doc->createElementNS(Signature::XMLNS, "KeyInfo");
        $keyInfoNode->appendChild($x509DataNode);

        return $keyInfoNode;
    }

    /**
     * @param $doc \DOMDocument
     * @param $elementName string
     * @param $value string
     * @return \DOMElement
     */
    private function createAlgorithmNode($doc, $elementName, $value)
    {
        $node = $doc->createElementNS(Signature::XMLNS, $elementName);
        $attribute = $doc->createAttribute("Algorithm");
        $attribute->value = $value;
        $node->appendChild($attribute);

        return $node;
    }
}
