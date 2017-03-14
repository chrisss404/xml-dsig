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

import javax.xml.crypto.AlgorithmMethod;
import javax.xml.crypto.KeySelector;
import javax.xml.crypto.KeySelectorException;
import javax.xml.crypto.KeySelectorResult;
import javax.xml.crypto.XMLCryptoContext;
import javax.xml.crypto.dsig.keyinfo.KeyInfo;
import javax.xml.crypto.dsig.keyinfo.X509Data;
import java.security.Key;
import java.security.PublicKey;
import java.security.cert.X509Certificate;

/**
 * Selects the public key of a given XML document.
 */
public class X509KeySelector extends KeySelector {

    /**
     *
     * @param keyInfo
     * @param purpose
     * @param method
     * @param context
     * @return
     * @throws KeySelectorException
     */
    @Override
    public KeySelectorResult select(KeyInfo keyInfo, KeySelector.Purpose purpose, AlgorithmMethod method,
                                    XMLCryptoContext context) throws KeySelectorException {

        for (Object keyInfoObject : keyInfo.getContent()) {
            if (!(keyInfoObject instanceof X509Data)) {
                continue;
            }

            for (Object x509DataObject : ((X509Data) keyInfoObject).getContent()) {
                if (!(x509DataObject instanceof X509Certificate)) {
                    continue;
                }

                PublicKey publicKey = ((X509Certificate) x509DataObject).getPublicKey();
                if (isAlgorithmSupported(publicKey.getAlgorithm(), method.getAlgorithm())) {
                    return buildKeySelectorResult(publicKey);
                }
            }
        }

        throw new KeySelectorException("No key found!");
    }

    private static KeySelectorResult buildKeySelectorResult(final PublicKey publicKey) {
        return new KeySelectorResult() {
            @Override
            public Key getKey() {
                return publicKey;
            }
        };
    }

    private static boolean isAlgorithmSupported(String name, String uri) {
        return SignatureMethod.fromValues(name, uri) != null;
    }

}
