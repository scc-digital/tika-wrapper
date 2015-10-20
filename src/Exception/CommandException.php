<?php

// CommandExeption.php

/**
 * This file is part of the Zapoyok project.
 *
 * (c) Jérôme Fix <jerome.fix@zapoyok.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Zapoyok\Tika\Exception;

use Symfony\Component\Process\Process;

class CommandException extends \RuntimeException
{
    const ERROR_ENCODING = 'java.io.UnsupportedEncodingException';

    public function __construct(Process $process)
    {
        parent::__construct(
            sprintf(
                'Command %s produced error: %s',
                $process->getCommandLine(),
                $process->getErrorOutput()
            )
        );
    }

    public static function factory(Process $process)
    {
        $error = $process->getOutput() . "\n" . $process->getErrorOutput();

        if (false !== stripos($error, self::ERROR_ENCODING)) {
            return new Command\UnsupportedEncodingException($process);
        } else {
            return new self($process);
        }
    }
}
