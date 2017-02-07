<?php
/*
 * This file is part of the Magallanes package.
 *
 * (c) Andrés Montañez <andres@andresmontanez.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mage\Task\BuiltIn\FS;

use Mage\Task\Exception\ErrorException;
use Mage\Task\AbstractTask;

/**
 * File System Task - Abstract Base class for File Operations
 *
 * @author Andrés Montañez <andresmontanez@gmail.com>
 */
abstract class AbstractFileTask extends AbstractTask
{
    /**
     * Returns the Task options
     *
     * @return array
     * @throws ErrorException
     */
    protected function getOptions()
    {
        $mandatory = $this->getParameters();

        foreach ($mandatory as $parameter) {
            if (!array_key_exists($parameter, $this->options)) {
                throw new ErrorException(sprintf('Parameter "%s" is not defined', $parameter));
            }
        }

        return $this->options;
    }

    /**
     * Returns the mandatory parameters
     *
     * @return array
     */
    abstract protected function getParameters();

    /**
     * Returns the default "flags".
     *
     * @return null|string
     */
    protected function getDefaultFlags()
    {
        return null;
    }

    /**
     * Returns the flags for the current command.
     *
     * @return string
     */
    protected function getFlags()
    {
        $options = $this->getOptions();
        $flags = $this->getDefaultFlags();

        if (array_key_exists('flags', $options) && !empty($options['flags'])) {
            $flags = trim($options['flags']);
        }

        return empty($flags) ? '' : $flags.' ';
    }

    /**
     * Returns a file with the placeholders replaced
     *
     * @param string $file
     * @return string
     * @throws ErrorException
     */
    protected function getFile($file)
    {
        $mapping = [
            '%environment%' => $this->runtime->getEnvironment(),
        ];

        if ($this->runtime->getWorkingHost() !== null) {
            $mapping['%host%'] = $this->runtime->getWorkingHost();
        }

        if ($this->runtime->getReleaseId() !== null) {
            $mapping['%release%'] = $this->runtime->getReleaseId();
        }

        $options = $this->getOptions();
        return str_replace(
            array_keys($mapping),
            array_values($mapping),
            $options[$file]
        );
    }
}
