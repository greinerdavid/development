<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Development\Business\Dependency;

use Spryker\Zed\Development\Business\DependencyTree\Finder;
use Spryker\Zed\Development\DevelopmentConfig;

class BundleParser
{

    const CONFIG_FILE = 'bundle_config.json';
    const ENGINE = 'engine';

    /**
     * @var array
     */
    protected $coreBundleNamespaces = ['Spryker', 'Orm', 'Client', 'Shared', 'Generated'];

    /**
     * @var \Spryker\Zed\Development\DevelopmentConfig
     */
    protected $config;

    /**
     * @var array
     */
    protected $bundleConfig;

    /**
     * @param \Spryker\Zed\Development\DevelopmentConfig $config
     */
    public function __construct(DevelopmentConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $bundleName
     *
     * @return array
     */
    public function parseOutgoingDependencies($bundleName)
    {
        $allFileDependencies = $this->parseDependencies($bundleName);
        $allFileDependencies = $this->filterCoreClasses($allFileDependencies);
        $bundleDependencies = $this->filterBundleDependencies($allFileDependencies, $bundleName);

        return $bundleDependencies;
    }

    /**
     * We only detect dependencies which are declared in the class' use statement
     *
     * @param string $bundle
     *
     * @return array
     */
    protected function parseDependencies($bundle)
    {
        $files = $this->findAllFilesOfBundle($bundle);

        $dependencies = [];
        foreach ($files as $file) {
            $content = $file->getContents();

            $matches = [];
            preg_match_all('#use (.*);#', $content, $matches);

            $dependencies[$file->getPath() . '/' . $file->getFilename()] = $matches[1];
        }

        return $dependencies;
    }

    /**
     * @param string $bundle
     *
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    protected function findAllFilesOfBundle($bundle)
    {
        $finder = new Finder($this->config->getBundleDirectory(), '*', $bundle);

        return $finder->getFiles();
    }

    /**
     * @param array $dependencies
     *
     * @return array
     */
    protected function filterCoreClasses(array $dependencies)
    {
        $reducedDependenciesPerFile = [];
        foreach ($dependencies as $fileName => $fileDependencies) {
            $reducedDependencies = [];
            foreach ($fileDependencies as $fileDependency) {
                $fileDependencyParts = explode('\\', $fileDependency);
                $bundleNamespace = $fileDependencyParts[0];

                if (in_array($bundleNamespace, $this->coreBundleNamespaces)) {
                    $reducedDependencies[] = $fileDependency;
                }
            }
            $reducedDependenciesPerFile[$fileName] = $reducedDependencies;
        }

        return $reducedDependenciesPerFile;
    }

    /**
     * @param array $allFileDependencies
     * @param string $bundle
     *
     * @return array
     */
    protected function filterBundleDependencies(array $allFileDependencies, $bundle)
    {
        $bundleDependencies = [];
        foreach ($allFileDependencies as $fileDependencies) {
            foreach ($fileDependencies as $fileDependency) {
                $expl = explode('\\', $fileDependency);
                $foreignBundle = $expl[2];
                if ($bundle !== $foreignBundle) {
                    if (array_key_exists($foreignBundle, $bundleDependencies) === false) {
                        $bundleDependencies[$foreignBundle] = 0;
                    }
                    $bundleDependencies[$foreignBundle]++;
                }
            }
        }

        ksort($bundleDependencies);

        return $bundleDependencies;
    }

}
