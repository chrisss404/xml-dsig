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

import javax.xml.crypto.MarshalException;
import javax.xml.crypto.dsig.CanonicalizationMethod;
import javax.xml.crypto.dsig.DigestMethod;
import javax.xml.crypto.dsig.Reference;
import javax.xml.crypto.dsig.SignedInfo;
import javax.xml.crypto.dsig.Transform;
import javax.xml.crypto.dsig.XMLSignature;
import javax.xml.crypto.dsig.XMLSignatureException;
import javax.xml.crypto.dsig.XMLSignatureFactory;
import javax.xml.crypto.dsig.dom.DOMSignContext;
import javax.xml.crypto.dsig.keyinfo.KeyInfo;
import javax.xml.crypto.dsig.keyinfo.KeyInfoFactory;
import javax.xml.crypto.dsig.keyinfo.X509Data;
import javax.xml.crypto.dsig.spec.C14NMethodParameterSpec;
import javax.xml.crypto.dsig.spec.TransformParameterSpec;
import java.io.InputStream;
import java.security.InvalidAlgorithmParameterException;
import java.security.KeyStore;
import java.security.NoSuchAlgorithmException;
import java.security.cert.X509Certificate;
import java.util.Arrays;
import java.util.Collections;
import java.util.List;

/**
 * The XmlSigner class is responsible for signing a given xml document.
 */
public class XmlSigner {

    private static XMLSignatureFactory sf = XMLSignatureFactory.getInstance("DOM");

    /**
     * Signs the given xml document.
     *
     * @param xmlToSign       The XML document to sign.
     * @param privateKeyEntry The private key used to sign the XML document.
     * @return The signed XML document.
     */
    public Document sign(InputStream xmlToSign, KeyStore.PrivateKeyEntry privateKeyEntry) {
        try {
            Document docToSign = Utility.loadXmlDocument(xmlToSign);

            SignedInfo si = buildSignedInfo();
            KeyInfo ki = buildKeyInfo(privateKeyEntry);
            XMLSignature signature = sf.newXMLSignature(si, ki);

            DOMSignContext dsc = new DOMSignContext(privateKeyEntry.getPrivateKey(), docToSign.getDocumentElement());
            signature.sign(dsc);

            return docToSign;
        } catch (InvalidAlgorithmParameterException | InvalidDocumentException | MarshalException |
                NoSuchAlgorithmException | XMLSignatureException e) {
            throw new XmlSignatureException("Failed signing XML", e);
        }
    }

    private static SignedInfo buildSignedInfo() throws InvalidAlgorithmParameterException, NoSuchAlgorithmException {
        Reference ref = sf.newReference(
                "", sf.newDigestMethod(DigestMethod.SHA256, null),
                Collections.singletonList(sf.newTransform(Transform.ENVELOPED, (TransformParameterSpec) null)),
                null, null
        );

        return sf.newSignedInfo(
                sf.newCanonicalizationMethod(CanonicalizationMethod.INCLUSIVE, (C14NMethodParameterSpec) null),
                sf.newSignatureMethod(SignatureMethod.RSA_SHA256.getUri(), null),
                Collections.singletonList(ref)
        );
    }

    private static KeyInfo buildKeyInfo(KeyStore.PrivateKeyEntry privateKeyEntry) {
        X509Certificate cert = (X509Certificate) privateKeyEntry.getCertificate();
        List x509Content = Arrays.asList(cert.getSubjectX500Principal().getName(), cert);

        KeyInfoFactory kif = sf.getKeyInfoFactory();
        X509Data xd = kif.newX509Data(x509Content);

        return kif.newKeyInfo(Collections.singletonList(xd));
    }

}
