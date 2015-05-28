<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 12/03/15
 * Time: 15:03
 */

namespace UCI\Boson\IntegratorBundle\Command;


use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UCI\Boson\IntegratorBundle\Exception\IntegratorException;

/**
 * Esta clase se encarga de cachear el mapa de conexiones entre servicios y dependencias.
 * Class MapBuildCommand
 * @package UCI\Boson\IntegratorBundle\Command
 */
class MapBuildCommand extends ContainerAwareCommand {

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('integrator:map:build')
            ->setDescription('Cachea el mapa de servicios y dependencias')
            ->setDefinition(array())
            ->setHelp(<<<EOT
The <info>integrator:map:build</info> Este comando cachea las aplicaciones del sistema y obtiene sus representaciones
EOT
            );
    }



    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $kernel = $container->get('integrator.kernel');
        $array = array();

        try {

            $array = $kernel->getRecursos();
            $kernel->buildMap();
        }
        catch(ClientException $guzzlex)
        {
            $container->get('logger')->addCritical('Error de conectividad '.$guzzlex->getMessage());
            $output->writeln(sprintf('Excepcion de cliente de conexion:'.$guzzlex->getMessage()));
        }
        catch(\Exception $error)
        {
            $output->writeln(sprintf('Ha ocurrido un error grave en la recuperacion de las representaciones.'.$error->getMessage()));
        }

        $output->writeln(sprintf('Las aplicaciones  han sido cacheadas.'));
    }


} 