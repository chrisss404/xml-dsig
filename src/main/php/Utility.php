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

use TheSeer\fDOM\fDOMDocument;
use TheSeer\fDOM\fDOMException;

final class Utility
{
    /**
     * @param $xmlString string
     * @return fDOMDocument
     */
    public static function loadXmlDocument($xmlString)
    {
        try {
            if (empty($xmlString)) {
                throw new \InvalidArgumentException("Input cannot be empty");
            }

            $domDocument = new fDOMDocument();
            $domDocument->loadXML($xmlString);

            return $domDocument;
        } catch (fDOMException $exception) {
            throw new InvalidDocumentException("Invalid XML input", $exception->getCode(), $exception);
        } catch (\InvalidArgumentException $exception) {
            throw new InvalidDocumentException("Invalid XML input", $exception->getCode(), $exception);
        }
    }
}
