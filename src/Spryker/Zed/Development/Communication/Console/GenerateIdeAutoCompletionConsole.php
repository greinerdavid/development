<?php
/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Communication\Console;

use Spryker\Zed\Console\Business\Model\Console;
use Spryker\Zed\Development\Business\DevelopmentFacadeInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method DevelopmentFacadeInterface getFacade()
 */
class GenerateIdeAutoCompletionConsole extends Console
{

    const COMMAND_NAME = 'dev:generate-ide-auto-completion';

    /**
     * @return void
     */
    protected function configure()
    {
        parent::configure();

        $this->setName(static::COMMAND_NAME);
        $this->setDescription('Generate locator auto-completion files for the IDE');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $dependingCommands = [
            GenerateZedIdeAutoCompletionConsole::COMMAND_NAME,
            GenerateClientIdeAutoCompletionConsole::COMMAND_NAME,
            GenerateServiceIdeAutoCompletionConsole::COMMAND_NAME,
        ];

        foreach ($dependingCommands as $commandName) {
            $this->runDependingCommand($commandName);

            if ($this->hasError()) {
                return $this->getLastExitCode();
            }
        }

        return $this->getLastExitCode();
    }

}
