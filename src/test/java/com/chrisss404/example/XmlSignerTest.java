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

import junit.framework.TestCase;
import org.w3c.dom.Document;

import java.io.ByteArrayInputStream;
import java.io.InputStream;
import java.security.KeyStore;

public class XmlSignerTest extends TestCase {

    private static KeyStore.PrivateKeyEntry privateKey;

    private static XmlSigner xmlSigner;

    private static XmlVerifier xmlVerifier;

    @Override
    protected void setUp() throws Exception {
        super.setUp();
        privateKey = TestUtility.loadPrivateKeyFromKeyStore("certs/keystore.jks", "demo", "abc123");
        xmlSigner = new XmlSigner();
        xmlVerifier = new XmlVerifier();
    }

    public void testSignInvalidXmlThrowsException() throws Exception {
        InputStream xmlInputStream = new ByteArrayInputStream("Not a XML document".getBytes());

        try {
            xmlSigner.sign(xmlInputStream, privateKey);
            fail("InputStream with invalid XML should cause exception");
        } catch (XmlSignatureException xve) {
            assertEquals("Invalid exception message", "Failed signing XML", xve.getMessage());
        }
    }

    public void testSignXmlSucceeds() throws Exception {
        InputStream xmlInputStream = new ByteArrayInputStream("<?xml version=\"1.0\" encoding=\"UTF-8\"?><document><content>I owe you 5 dollars.</content></document>".getBytes());

        Document doc = xmlSigner.sign(xmlInputStream, privateKey);
        Document verifiedDocument = xmlVerifier.verify(TestUtility.getInputStreamFromDocument(doc));

        assertNotNull("XML signature is invalid", verifiedDocument);
    }

}