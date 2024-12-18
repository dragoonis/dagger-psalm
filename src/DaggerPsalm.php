<?php

declare(strict_types=1);

namespace DaggerModule;

use Dagger\Attribute\DaggerFunction;
use Dagger\Attribute\DaggerObject;
use Dagger\Attribute\Doc;
use Dagger\Container;
use Dagger\Directory;
use Dagger\Terminal;
use InvalidArgumentException;

use function Dagger\dag;

#[DaggerObject]
#[Doc('Run psalm against your php codebase')]
class DaggerPsalm
{
    // dagger call analyze --php-version=8.4 --source=repo_url#branch --path-to-test=src stdout
    // dagger call analyze --source=https://github.com/dragoonis/Sylius#2.0 --php-version=8.3 --path-to-test=src stdout
    #[DaggerFunction('psalm')]
    public function psalm(
        string $phpVersion,
        Directory $source,
        string $pathToTest,
    ): Container {

        if(!in_array($phpVersion, ['7.1', '7.2', '7.3', '7.4','8.0', '8.1', '8.2', '8.3', '8.4'])) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid PHP version specified'
            ));
        }

        $dockerTag = sprintf("php%s-alpine", $phpVersion);

        return dag()->container()
            ->from("jakzal/phpqa:$dockerTag")
            ->withMountedDirectory('/tmp/app', $source)
            ->withWorkDir('/tmp/app')
            ->withExec(['mkdir', 'vendor'])
            ->withExec(['touch', 'vendor/autoload.php'])
            ->withExec(['psalm']);

    }

}


