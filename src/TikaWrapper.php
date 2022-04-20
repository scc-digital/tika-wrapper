<?php

// TikaWrapper.php

/**
 * This file is part of the Zapoyok project.
 *
 * (c) Jérôme Fix <jerome.fix@zapoyok.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Zapoyok\Tika;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use Symfony\Component\Process\Process;
use Zapoyok\Tika\Exception\CommandException;

class TikaWrapper implements TikaWrapperInterface
{
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

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return string
     */
    public function getJavaBinary()
    {
        return $this->javaBinary;
    }

    /**
     * @param string $javaBinary
     *
     * @return TikaWrapper
     */
    public function setJavaBinary($javaBinary)
    {
        $this->javaBinary = $javaBinary;

        return $this;
    }

    /**
     * @return string
     */
    public function getOutputEncoding()
    {
        return $this->outputEncoding;
    }

    /**
     * @param string $outputEncoding
     *
     * @return TikaWrapper
     */
    public function setOutputEncoding($outputEncoding)
    {
        $this->outputEncoding = $outputEncoding;

        return $this;
    }

    /**
     * @return string
     */
    public function getOutputFormat()
    {
        return $this->outputFormat;
    }

    /**
     * @param string $outputFormat
     *
     * @return TikaWrapper
     */
    public function setOutputFormat($outputFormat)
    {
        if (!in_array($outputFormat, self::getOutputFormats(), true)) {
            throw new Exception\UnsupportedOutputFormatException(sprintf('The "%s" format is not valid, use one of these: %s', $outputFormat, implode(', ', self::getOutputFormats())));
        }

        $this->outputFormat = $outputFormat;

        return $this;
    }

    /**
     * Options.
     */

    /**
     * @return string
     */
    public function getBinary()
    {
        return $this->binary;
    }

    /**
     * @param string $binary
     *
     * @return TikaWrapper
     */
    public function setBinary($binary)
    {
        $this->binary = $binary;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     *
     * @return TikaWrapper
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function buildCommand()
    {
        $arguments = $this->buildCommandArguments();

        \array_unshift($arguments, $this->binary);
        \array_unshift($arguments, '-jar');
        \array_unshift($arguments, $this->javaBinary);


        return (new Process($arguments))->getCommandLine();
    }

    public function buildCommandArguments(): array
    {
        $arguments = [];

        // Output Format
        $arguments[] = '--' . $this->getOutputFormat();

        // Output Encoding
        $arguments[] = sprintf('--encoding=%s',  $this->getOutputEncoding());

        // Document
        $arguments[] = $this->getFile()->getPathname();

        return $arguments;
    }

    /**
     * @return \SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param \SplFileInfo $file
     *
     * @throws \Zapoyok\Tika\Exception\InvalidFileException
     *
     * @return $this
     */
    public function setFile(\SplFileInfo $file)
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
     * @param array $arguments
     *
     * @throws \Exception
     *
     * @return string
     */
    protected function execute(array $arguments)
    {
        \array_unshift($arguments, $this->binary);
        \array_unshift($arguments, '-jar');
        \array_unshift($arguments, $this->javaBinary);

        $process = new Process($arguments);
        $process->setTimeout($this->timeout);
        $process->run();

        $this->logger->debug($process->getCommandLine());

        if (!$process->isSuccessful()) {
            throw CommandException::factory($process);
        }

        $output = $process->getOutput() ?:  null;  

        if (preg_match('/produced error: Exception in thread "main"/', $output)) {
            throw CommandException::factory($process);
        }

        return $output;
    }

    final public function extract(): string
    {
        $arguments = $this->buildCommandArguments();
        $output    = $this->execute($arguments);

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
                self::OUTPUT_FORMAT_XMP ,
                self::OUTPUT_FORMAT_JSONRECURSIVE,
                self::OUTPUT_FORMAT_LANGUAGE ,
                self::OUTPUT_FORMAT_DETECT,
        ];
    }
}
