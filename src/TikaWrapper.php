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

namespace Scc\Tika;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Process\Process;
use Scc\Tika\Exception\CommandException;

class TikaWrapper implements TikaWrapperInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    protected $javaBinary = self::JAVA_BINARY;

    /**
     * @var string
     */
    protected $binary = self::TIKA_BINARY;

    /**
     * @var int
     */
    protected $timeout = self::DEFAULT_TIMEOUT;

    /**
     * @var \SplFileInfo
     */
    protected $file;

    /**
     * @var string
     */
    protected $outputFormat = self::OUTPUT_FORMAT_TEXT;

    /**
     * @var string
     */
    protected $outputEncoding = 'UTF8';

    public function getJavaBinary(): string
    {
        return $this->javaBinary;
    }

    /**
     * @return TikaWrapper
     */
    public function setJavaBinary(string $javaBinary): self
    {
        $this->javaBinary = $javaBinary;

        return $this;
    }

    public function getOutputEncoding(): string
    {
        return $this->outputEncoding;
    }

    /**
     * @return TikaWrapper
     */
    public function setOutputEncoding(string $outputEncoding): self
    {
        $this->outputEncoding = $outputEncoding;

        return $this;
    }

    public function getOutputFormat(): string
    {
        return $this->outputFormat;
    }

    /**
     * @return TikaWrapper
     */
    public function setOutputFormat(string $outputFormat): self
    {
        if (!\in_array($outputFormat, self::getOutputFormats(), true)) {
            throw new Exception\UnsupportedOutputFormatException(sprintf('The "%s" format is not valid, use one of these: %s', $outputFormat, implode(', ', self::getOutputFormats())));
        }

        $this->outputFormat = $outputFormat;

        return $this;
    }

    /**
     * Options.
     */
    public function getBinary(): string
    {
        return $this->binary;
    }

    /**
     * @return TikaWrapper
     */
    public function setBinary(string $binary): self
    {
        $this->binary = $binary;

        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @return TikaWrapper
     */
    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function buildCommand()
    {
        $arguments = $this->buildCommandArguments();

        array_unshift($arguments, $this->binary);
        array_unshift($arguments, '-jar');
        array_unshift($arguments, $this->javaBinary);

        return (new Process($arguments))->getCommandLine();
    }

    public function buildCommandArguments(): array
    {
        $arguments = [];

        // Output Format
        $arguments[] = '--' . $this->getOutputFormat();

        // Output Encoding
        $arguments[] = sprintf('--encoding=%s', $this->getOutputEncoding());

        // Document
        $arguments[] = $this->getFile()->getPathname();

        return $arguments;
    }

    public function getFile(): \SplFileInfo
    {
        return $this->file;
    }

    /**
     * @throws \Zapoyok\Tika\Exception\InvalidFileException
     *
     * @return $this
     */
    public function setFile(\SplFileInfo $file): self
    {
        if (!$file->isFile()) {
            throw new Exception\InvalidFileException(sprintf('The supplied file (« %s ») does not exist.', $file->getPathname()));
        }

        if (!$file->isReadable()) {
            throw new Exception\InvalidFileException(sprintf('The supplied file (« %s ») is not readable.', $file->getPathname()));
        }

        $this->file = $file;

        return $this;
    }

    /**
     * Execute command and return output.
     *
     * @throws \Exception
     */
    protected function execute(array $arguments): string
    {
        array_unshift($arguments, $this->binary);
        array_unshift($arguments, '-jar');
        array_unshift($arguments, $this->javaBinary);

        $process = new Process($arguments);
        $process->setTimeout($this->timeout);
        $process->run();

        $this->logger->debug($process->getCommandLine());

        if (!$process->isSuccessful()) {
            throw CommandException::factory($process);
        }

        $output = $process->getOutput() ?: '';

        if (preg_match('/produced error: Exception in thread "main"/', $output)) {
            throw CommandException::factory($process);
        }

        return $output;
    }

    final public function extract(): string
    {
        $arguments = $this->buildCommandArguments();
        $output = $this->execute($arguments);

        return trim($output);
    }

    public static function getOutputFormats(): array
    {
        return [
            self::OUTPUT_FORMAT_XML,
            self::OUTPUT_FORMAT_HTML,
            self::OUTPUT_FORMAT_TEXT,
            self::OUTPUT_FORMAT_TEXT_MAIN,
            self::OUTPUT_FORMAT_METADATA,
            self::OUTPUT_FORMAT_JSON,
            self::OUTPUT_FORMAT_XMP,
            self::OUTPUT_FORMAT_JSONRECURSIVE,
            self::OUTPUT_FORMAT_LANGUAGE,
            self::OUTPUT_FORMAT_DETECT,
        ];
    }
}
