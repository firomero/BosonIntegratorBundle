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
    static::executeBuildResourceDir($webDir);
}

    public static function buildDefinitionSchema(CommandEvent $commandEvent)
    {
        $options = self::getOptions($commandEvent);
        $webDir = $options['symfony-web-dir'];
        if (!is_dir($webDir)) {
            echo 'La symfony-web-dir ('.$webDir.') especificada in composer.json no fue hallada  en '.getcwd().PHP_EOL;

            return;
        }

        static::executeDefinitionSchema($webDir);
    }

    protected static function executeDefinitionSchema($webDir)
    {
        if (file_exists(__DIR__.'/definition.xsd')) {
            copy(__DIR__.'/definition.xsd',$webDir.'/definition.xsd');
        }
    }

    protected static function executeBuildResourceDir($webDir, $timeout = 300)
    {
        $webDir = escapeshellarg($webDir);
        $command= sprintf("mkdir %s/definition",$webDir);

        $process = new Process($command,$timeout);
        $process->run(function ($type, $buffer) { echo $buffer; });
        if (!$process->isSuccessful()) {
            throw new \RuntimeException('Ha ocurrido un error creando el directorio de  definiciones.');
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