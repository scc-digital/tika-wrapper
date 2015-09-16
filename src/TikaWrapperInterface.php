<?php

// TikaWrapperInterface.php

/**
 * This file is part of the Jfx project.
 *
 * (c) Jérôme Fix <jerome.fix@zapoyok.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jfx\Tika;

interface TikaWrapperInterface
{
    const DEFAULT_TIMEOUT   = 60;
    const JAVA_BINARY       = 'java';
    const TIKA_BINARY       = __DIR__ . '/../vendor/tika/tika/tika-app-1.10.jar';

    const OUTPUT_FORMAT_XML             = 'xml'; //           Output XHTML content (default)
    const OUTPUT_FORMAT_HTML            = 'html'; //          Output HTML content
    const OUTPUT_FORMAT_TEXT            = 'text'; //          Output plain text content
    const OUTPUT_FORMAT_TEXT_MAIN       = 'text-main'; //     Output plain text content (main content only)
    const OUTPUT_FORMAT_METADATA        = 'metadata'; //      Output only metadata
    const OUTPUT_FORMAT_JSON            = 'json'; //          Output metadata in JSON
    const OUTPUT_FORMAT_XMP             = 'xmp'; //           Output metadata in XMP
    const OUTPUT_FORMAT_JSONRECURSIVE   = 'jsonRecursive'; // Output metadata and content from all
    const OUTPUT_FORMAT_LANGUAGE        = 'language'; //      Output only language
    const OUTPUT_FORMAT_DETECT          = 'detect'; //        Detect document type
}
