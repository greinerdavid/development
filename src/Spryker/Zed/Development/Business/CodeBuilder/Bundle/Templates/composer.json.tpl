{
  "name": "spryker/{bundleDashed}",
  "description": "{bundle} module",
  "license": "proprietary",
  "require": {
  },
  "require-dev": {
    "spryker/code-sniffer": "*",
    "spryker/testify": "*"
  },
  "autoload": {
    "psr-4": {
      "Spryker\\": "src/Spryker/"
    }
  },
  "autoload-dev": {
    "psr-4": {
      "SprykerTest\\": "tests/SprykerTest/"
    }
  },
  "minimum-stability": "dev",
  "prefer-stable": true,
  "extra": {
    "branch-alias": {
      "dev-master": "1.0.x-dev"
    }
  },
  "config": {
    "sort-packages": true
  }
}