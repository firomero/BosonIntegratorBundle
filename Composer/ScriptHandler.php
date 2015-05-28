<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 16/12/2014
 * Time: 23:05
 */

namespace IntegratorBundle\Composer;
use Symfony\Component\ClassLoader\ClassCollectionLoader;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;
use Composer\Script\CommandEvent;
/**
 * Este componente s eencarga de las tareas de post-instalacion
 * @author Felix Ivan Romero Rodríguez <firomero@uci.cu>
 */

class ScriptHandler {

    /**
     * @param CommandEvent $commandEvent
     */
public static function buildResourceDir(CommandEvent $commandEvent)
{
    $options = self::getOptions($commandEvent);
    $webDir = $options['symfony-web-dir'];
    if (!is_dir($webDir)) {
        echo 'La symfony-web-dir ('.$webDir.') especificada in composer.json no fue hallada  en '.getcwd().PHP_EOL;

        return;
    }

}

    /**
     * @param CommandEvent $commandEvent
     */
    public  static function buildMap(CommandEvent $event)
{
    $options = self::getOptions($event);
    $appDir = $options['symfony-app-dir'];

    if (!is_dir($appDir)) {
        echo 'The symfony-app-dir ('.$appDir.') specified in composer.json was not found in '.getcwd().', can not clear the cache.'.PHP_EOL;

        return;
    }
    static::executeCommand($event, $appDir, 'integrator:map:build', $options['process-timeout']);

}

    protected static function executeCommand(CommandEvent $event, $appDir, $cmd, $timeout = 300)
    {
        $php = escapeshellarg(self::getPhp());
        $console = escapeshellarg($appDir.'/console');
        if ($event->getIO()->isDecorated()) {
            $console .= ' --ansi';
        }

        $process = new Process($php.' '.$console.' '.$cmd, null, null, null, $timeout);
        $process->run(function ($type, $buffer) { echo $buffer; });
        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('An error occurred when executing the "%s" command.', escapeshellarg($cmd)));
        }
    }




    /**
     * @param CommandEvent $event
     * @return array
     */
    protected static function getOptions(CommandEvent $event)
    {
        $options = array_merge(array(
            'symfony-app-dir' => 'app',
            'symfony-web-dir' => 'web',
            'symfony-assets-install' => 'hard'
        ), $event->getComposer()->getPackage()->getExtra());

        $options['symfony-assets-install'] = getenv('SYMFONY_ASSETS_INSTALL') ?: $options['symfony-assets-install'];

        $options['process-timeout'] = $event->getComposer()->getConfig()->get('process-timeout');

        return $options;
    }

} 