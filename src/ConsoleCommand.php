<?php
namespace ImageConsole;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ConsoleCommand
 * @package ImageConsole
 */
class ConsoleCommand extends Command
{
    protected function configure()
    {
        $this->setName("image:store")
            ->setDescription("Cli interface for Image storage")
            ->addArgument('file', InputArgument::REQUIRED, 'What is the path of the image?)')
            ->addArgument('method', InputArgument::REQUIRED, 'What operation would you like to perform?)');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $method = $input->getArgument('method');

        // @todo config vars would be abstracted out to .env
        $resources = __DIR__ . '/../resources/';
        $localPath = $resources . '/local/';
        $remotePath = $resources . '/remote/';
        $localAdapter = new Local($localPath);
        $remoteAdapter = new Local($remotePath);

        $log = new Logger('image:store');
        $log->pushHandler(new StreamHandler($resources . '/logs/log.txt'));

        $store = new ImageStore(
            new Filesystem($localAdapter),
            new Filesystem($remoteAdapter),
            $log,
            new ImageValidate($log)
        );
        $store->setFile($file);

        try {
            $fileOutput = $store->process($method);
            if ($fileOutput) {
                $output->writeln($fileOutput->getPath());
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}