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

package com.chrisss404.example;

import org.w3c.dom.Document;
import org.w3c.dom.NodeList;

import javax.xml.crypto.MarshalException;
import javax.xml.crypto.dsig.Reference;
import javax.xml.crypto.dsig.XMLSignature;
import javax.xml.crypto.dsig.XMLSignatureException;
import javax.xml.crypto.dsig.XMLSignatureFactory;
import javax.xml.crypto.dsig.dom.DOMValidateContext;
import java.io.IOException;
import java.io.InputStream;
import java.security.KeyStore;
import java.security.KeyStoreException;
import java.security.NoSuchAlgorithmException;
import java.security.cert.CertificateException;
import java.util.List;

/**
 * The XmlVerifier class is responsible for validating a given xml document.
 */
public class XmlVerifier {

    private static XMLSignatureFactory sf = XMLSignatureFactory.getInstance("DOM");

    private final KeyStore keyStore;

    /**
     * Constructs a XmlVerifier instance with all its dependencies.
     *
     * @param keyStore The keystore holds the trusted certificates.
     */
    public XmlVerifier(KeyStore keyStore) {
        this.keyStore = keyStore;
    }

    /**
     * Verifies that a given XML document is valid.
     *
     * @param xmlToVerify The XML document to verify.
     * @return The verified XML document.
     */
    public Document verify(InputStream xmlToVerify) {
        try {
            Document docToVerify = Utility.loadXmlDocument(xmlToVerify);
            validateSignature(docToVerify, keyStore);
            return docToVerify;
        } catch (CertificateException | InvalidDocumentException | IOException | KeyStoreException | MarshalException |
                NoSuchAlgorithmException | XMLSignatureException e) {
            throw new XmlVerificationException(String.format("Signature verification failed: %s", e.getMessage()), e);
        }
    }

    private static void validateSignature(Document doc, KeyStore keyStore) throws XMLSignatureException, MarshalException, CertificateException, NoSuchAlgorithmException, KeyStoreException, IOException {
        NodeList nl = doc.getElementsByTagNameNS(XMLSignature.XMLNS, "Signature");
        if (nl.getLength() == 0) {
            throw new XmlVerificationException("Cannot find Signature element");
        }

        DOMValidateContext dvc = new DOMValidateContext(new X509KeySelector(keyStore), nl.item(0));
        XMLSignature signature = sf.unmarshalXMLSignature(dvc);

        if (!signature.validate(dvc)) {
            boolean sv = signature.getSignatureValue().validate(dvc);
            StringBuilder sb = new StringBuilder();
            sb.append("Signature validation status: ").append(sv);
            if (!sv) {
                List references = signature.getSignedInfo().getReferences();
                for (int i = 0, length = references.size(); i < length; i++) {
                    boolean refValid = ((Reference) references.get(i)).validate(dvc);
                    sb.append("\n").append(" - ref[").append(i).append("] validity status: ").append(refValid);
                }
            }
            throw new XmlVerificationException(sb.toString());
        }
    }

}
