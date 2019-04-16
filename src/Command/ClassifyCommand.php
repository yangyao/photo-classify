<?php

namespace Yangyao\PhotoClassify\Command;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClassifyCommand extends Command
{
    protected static $defaultName = 'app:run';

    private $allowExtensions = [
        'jpeg',
        'tif',
        'tiff',
        'gif',
        'png',
        'webp',
        'bmp',
        'jpg',
        'exif',
        'svg',
        'psd',
        'cdr',
        'ai',
        'raw',
    ];

    private $exifExtensions = [
        'jpeg',
        'jpg',
        'tif',
        'tiff',
    ];

    private $conflictSeparator = '&_&';

    protected function configure()
    {
        $this->addArgument('source', InputArgument::REQUIRED, 'Source Directory');
        $this->addArgument('target', InputArgument::REQUIRED, 'Output Directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $source = $input->getArgument('source');
        $target = $input->getArgument('target');
        $filesystem = new Filesystem(new Local($source));

        $contents = $filesystem->listContents('', true);

        collect($contents)->where('type', 'file')->whereIn('extension', $this->allowExtensions)->each(function ($object
        ) use ($filesystem, $source, $target, $output) {
            $folder = date('Y-m', $object['timestamp']);
            $sourceFile = $source . DIRECTORY_SEPARATOR . $object['path'];
            if (in_array($object['extension'], $this->exifExtensions)) {
                $exif = @exif_read_data($sourceFile);
                // use FileDateTime first
                if (isset($exif['FileDateTime'])) {
                    $folder = date('Y-m', $exif['FileDateTime']);
                }
                // if original exist
                if (isset($exif['DateTimeOriginal'])) {
                    $folder = date('Y-m', strtotime($exif['DateTimeOriginal']));
                }
            }
            $targetFile = $target . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $object['filename'] . '.' . $object['extension'];
            if (file_exists($targetFile)) {
                $sourceHash = hash_file('md5', $sourceFile);
                $hashTargetFile = $target . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $sourceHash . $this->conflictSeparator . $object['filename'] . '.' . $object['extension'];
                if (file_exists($hashTargetFile)) {
                    return true;
                }
                $targetHash = hash_file('md5', $targetFile);
                if ($sourceHash == $targetHash) {
                    return true;
                }
                $targetFile = $hashTargetFile;
            }
            @mkdir($target . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR, true);
            @copy($sourceFile, $targetFile);
            $output->writeln("copy $sourceFile to $targetFile");
        });
    }
}
