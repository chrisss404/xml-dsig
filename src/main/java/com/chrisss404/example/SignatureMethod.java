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

/**
 * Holds supported signature methods.
 */
public enum SignatureMethod {

    RSA_SHA256("RSA", "http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"),
    RSA_SHA512("RSA", "http://www.w3.org/2001/04/xmldsig-more#rsa-sha512");

    private final String name;

    private final String uri;

    SignatureMethod(String name, String uri) {
        this.name = name;
        this.uri = uri;
    }

    public String getName() {
        return name;
    }

    public String getUri() {
        return uri;
    }

    /**
     * Finds the corresponding enum from the given values.
     *
     * @param name The name of the wanted algorithm.
     * @param uri The URI of the wanted algorithm.
     * @return The corresponding method.
     */
    public static SignatureMethod fromValues(String name, String uri) {
        for (SignatureMethod method : SignatureMethod.values()) {
            if (name.equalsIgnoreCase(method.getName()) && uri.equalsIgnoreCase(method.getUri())) {
                return method;
            }
        }
        return null;
    }
}
