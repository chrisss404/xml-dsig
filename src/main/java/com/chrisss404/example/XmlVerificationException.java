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
 * Signals that a verification exception of some sort has occurred. This class is the general class of exceptions
 * produced by the verification of invalid xml documents.
 */
public class XmlVerificationException extends RuntimeException {
    private static final long serialVersionUID = 98765678987656789L;

    /**
     * Constructs a XmlVerificationException with null as its error detail message.
     */
    public XmlVerificationException() {
        super();
    }

    /**
     * Constructs a XmlVerificationException with the specified detail message.
     *
     * @param message The detail message (which is saved for later retrieval by the <link>Throwable.getMessage()</link> method)
     */
    public XmlVerificationException(String message) {
        super(message);
    }

    /**
     * Constructs a XmlVerificationException with the specified detail message and cause.
     *
     * Note that the detail message associated with cause is not automatically incorporated into this exception's
     * detail message.
     *
     * @param message The detail message (which is saved for later retrieval by the <link>Throwable.getMessage()</link> method)
     * @param cause The cause (which is saved for later retrieval by the <link>Throwable.getCause()</link> method).
     *              (A null value is permitted, and indicates that the cause is nonexistent or unknown.)

     */
    public XmlVerificationException(String message, Throwable cause) {
        super(message, cause);
    }

    /**
     * Constructs a XmlVerificationException with the specified cause and a detail message of
     * (cause==null ? null : cause.toString() (which typically contains the class and detail message of cause).
     * This constructor is useful for XmlVerificationException that are little more than wrappers for other throwables.
     *
     * @param cause The cause (which is saved for later retrieval by the <link>Throwable.getCause()</link> method).
     *              (A null value is permitted, and indicates that the cause is nonexistent or unknown.)
     */
    public XmlVerificationException(Throwable cause) {
        super(cause);
    }
}
