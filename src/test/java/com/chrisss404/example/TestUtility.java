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

import javax.xml.transform.Transformer;
import javax.xml.transform.TransformerException;
import javax.xml.transform.TransformerFactory;
import javax.xml.transform.dom.DOMSource;
import javax.xml.transform.stream.StreamResult;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.security.KeyStore;
import java.security.KeyStoreException;
import java.security.NoSuchAlgorithmException;
import java.security.UnrecoverableEntryException;
import java.security.cert.CertificateException;

public class TestUtility {

    public static KeyStore loadKeyStore(String keyStore, String password) throws CertificateException, NoSuchAlgorithmException, IOException, KeyStoreException {
        InputStream is = TestUtility.class.getClassLoader().getResourceAsStream(keyStore);
        KeyStore ks = KeyStore.getInstance("JKS");
        ks.load(is, password.toCharArray());
        return ks;
    }

    public static KeyStore.PrivateKeyEntry loadPrivateKeyFromKeyStore(KeyStore keyStore, String alias, String password) throws IOException, CertificateException, NoSuchAlgorithmException, UnrecoverableEntryException, KeyStoreException {
        return (KeyStore.PrivateKeyEntry) keyStore.getEntry(alias, new KeyStore.PasswordProtection(password.toCharArray()));
    }

    public static void saveDocumentToFile(Document doc, String filename) throws IOException, TransformerException {
        OutputStream os = new FileOutputStream(filename);

        TransformerFactory tf = TransformerFactory.newInstance();
        Transformer trans = tf.newTransformer();
        trans.transform(new DOMSource(doc), new StreamResult(os));
        os.close();
    }

    public static InputStream getInputStreamFromDocument(Document doc) throws TransformerException, IOException {
        ByteArrayOutputStream baos = new ByteArrayOutputStream();

        TransformerFactory tf = TransformerFactory.newInstance();
        Transformer trans = tf.newTransformer();
        trans.transform(new DOMSource(doc), new StreamResult(baos));

        InputStream is = new ByteArrayInputStream(baos.toByteArray());
        baos.close();

        return is;
    }
}
