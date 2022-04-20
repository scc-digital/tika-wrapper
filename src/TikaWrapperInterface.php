<?php

declare(strict_types=1);

/*
 * This file is part of the Zapoyok project.
 *
 * (c) Jérôme Fix <jerome.fix@zapoyok.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This file is part of the Zapoyok project.
 *
 * (c) Jérôme Fix <jerome.fix@zapoyok.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zapoyok\Tika;

interface TikaWrapperInterface
{
    public const DEFAULT_TIMEOUT = 60;
    public const JAVA_BINARY = 'java';
    public const TIKA_BINARY = '/usr/local/bin/tika-app.jar';

    public const OUTPUT_FORMAT_XML = 'xml'; //           Output XHTML content (default)
    public const OUTPUT_FORMAT_HTML = 'html'; //          Output HTML content
    public const OUTPUT_FORMAT_TEXT = 'text'; //          Output plain text content
    public const OUTPUT_FORMAT_TEXT_MAIN = 'text-main'; //     Output plain text content (main content only)
    public const OUTPUT_FORMAT_METADATA = 'metadata'; //      Output only metadata
    public const OUTPUT_FORMAT_JSON = 'json'; //          Output metadata in JSON
    public const OUTPUT_FORMAT_XMP = 'xmp'; //           Output metadata in XMP
    public const OUTPUT_FORMAT_JSONRECURSIVE = 'jsonRecursive'; // Output metadata and content from all
    public const OUTPUT_FORMAT_LANGUAGE = 'language'; //      Output only language
    public const OUTPUT_FORMAT_DETECT = 'detect'; //        Detect document type

    public function setFile(\SplFileInfo $file): self;

    public function setOutputFormat(string $outputFormat): self;

    public function extract(): string;
}
