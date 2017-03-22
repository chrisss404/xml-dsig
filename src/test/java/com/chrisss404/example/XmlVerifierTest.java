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

import java.io.ByteArrayInputStream;
import java.io.InputStream;
import java.nio.charset.StandardCharsets;

public class XmlVerifierTest extends TestCase {

    private static XmlVerifier xmlVerifier;

    @Override
    protected void setUp() throws Exception {
        super.setUp();
        xmlVerifier = new XmlVerifier();
    }

    public void testVerifyingNullAsXmlInputThrowsException() throws Exception {
        try {
            xmlVerifier.verify(null);
            fail("InputStream that is null should cause exception");
        } catch (IllegalArgumentException iae) {
            assertEquals("Invalid exception message", "InputStream cannot be null", iae.getMessage());
        }
    }

    public void testVerifyingEmptyAsXmlInputThrowsException() throws Exception {
        try {
            xmlVerifier.verify(new ByteArrayInputStream("".getBytes(StandardCharsets.UTF_8)));
            fail("InputStream with empty string should cause exception");
        } catch (XmlVerificationException xve) {
            assertEquals("Invalid exception message", "Signature verification failed: Invalid XML input", xve.getMessage());
        }
    }

    public void testVerifyingUnsupportedInputRepresentationThrowsException() throws Exception {
        try {
            xmlVerifier.verify(new ByteArrayInputStream("Not a XML document".getBytes(StandardCharsets.UTF_8)));
            fail("InputStream with invalid XML should cause exception");
        } catch (XmlVerificationException xve) {
            assertEquals("Invalid exception message", "Signature verification failed: Invalid XML input", xve.getMessage());
        }
    }

    public void testVerifyingManipulatedXmlThrowsException() throws Exception {
        try {
            InputStream xmlInputStream = getClass().getClassLoader().getResourceAsStream("manipulated.xml");
            xmlVerifier.verify(xmlInputStream);
            fail("Manipulated XML should cause verification exception");
        } catch (XmlVerificationException xve) {
            assertEquals("Invalid exception message", "Signature validation status: true", xve.getMessage());
        }
    }

    public void testVerifyingXmlWithInvalidSignatureThrowsException() throws Exception {
        try {
            InputStream xmlInputStream = getClass().getClassLoader().getResourceAsStream("invalid-signature.xml");
            xmlVerifier.verify(xmlInputStream);
            fail("Manipulated XML should cause verification exception");
        } catch (XmlVerificationException xve) {
            assertEquals("Invalid exception message", "Signature verification failed: java.security.SignatureException: Signature length not correct: got 31 but was expecting 256", xve.getMessage());
        }
    }

    public void testVerifyingXmlWithValidDigestAndInvalidSignatureThrowsException() throws Exception {
        try {
            InputStream xmlInputStream = getClass().getClassLoader().getResourceAsStream("valid-digest-but-invalid-signature.xml");
            xmlVerifier.verify(xmlInputStream);
            fail("Manipulated XML should cause verification exception");
        } catch (XmlVerificationException xve) {
            assertEquals("Invalid exception message", "Signature validation status: false\n" +
                    " - ref[0] validity status: true", xve.getMessage());
        }
    }

    public void testVerifyingXmlWithUnsupportedSignatureAlgorithmThrowsException() throws Exception {
        try {
            InputStream xmlInputStream = getClass().getClassLoader().getResourceAsStream("unsupported-signature-algorithm.xml");
            xmlVerifier.verify(xmlInputStream);
            fail("Manipulated XML should cause verification exception");
        } catch (XmlVerificationException xve) {
            assertEquals("Invalid exception message", "Signature verification failed: cannot find validation key", xve.getMessage());
        }
    }

    public void testVerifyingNotSignedXmlThrowsException() throws Exception {
        try {
            InputStream xmlInputStream = getClass().getClassLoader().getResourceAsStream("not-signed.xml");
            xmlVerifier.verify(xmlInputStream);
            fail("Not signed XML should cause verification exception");
        } catch (XmlVerificationException xve) {
            assertEquals("Invalid exception message", "Cannot find Signature element", xve.getMessage());
        }
    }

    public void testVerifyingXmlSignedWithUnverifiableCertificateAndWrongXMLMessageThrowsException() throws Exception {
        try {
            InputStream xmlInputStream = getClass().getClassLoader().getResourceAsStream("signed-with-unverifiable-certificate.xml");
            xmlVerifier.verify(xmlInputStream);
            fail("Unverifiable certificate should cause verification exception");
        } catch (XmlVerificationException xve) {
            assertEquals("Invalid exception message", "Signature verification failed: cannot find validation key", xve.getMessage());
        }
    }

    public void testVerifyingValidXmlSucceeds() throws Exception {
        InputStream xmlInputStream = getClass().getClassLoader().getResourceAsStream("valid.xml");
        assertNotNull("XML signature is invalid", xmlVerifier.verify(xmlInputStream));
    }

}
