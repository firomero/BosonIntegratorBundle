<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 21/05/15
 * Time: 15:43
 */

namespace UCI\Boson\IntegratorBundle\Command;



use UCI\Boson\IntegratorBundle\Security\YamlSecurityLoader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegisterClientCommand extends ContainerAwareCommand{

    const SHA521 = 'sha512';
    const MD5 = 'md5';
    const HAVAL = 'haval160,4';

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('integrator:client:register')
            ->setDescription('Registra un Cliente de servicios')
            ->setDefinition(array(
                new InputArgument('name', InputArgument::REQUIRED, 'Identificador'),
                new InputArgument('host', InputArgument::REQUIRED, 'Direccion Web Cliente'),
                new InputArgument('remote', InputArgument::REQUIRED, 'IP Cliente'),
                new InputArgument('port', InputArgument::OPTIONAL, 'Puerto Cliente'),
                new InputArgument('ttl', InputArgument::OPTIONAL, 'Timepo de Vida'),

            ))
            ->setHelp(<<<EOT
The <info>integrator:aplication:add</info> Este comando permite adicionar aplicaciones clientes para los servicios:

  <info>php app/console integrator:aplication:add http://myappserver.uci.cu/api/rest 9898 12.25.48.102</info>
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $host = $input->getArgument('host');
        $port = $input->getArgument('port')==null?'80':$input->getArgument('port');
        $ttl = $input->getArgument('ttl')==null?'365780':$input->getArgument('ttl');
        $remote = $input->getArgument('remote');
        $plain = $host.'@'.$port.'@'.$remote.'@'.$ttl;
        $seed = microtime();
        $key = hash(self::SHA521,json_encode(array($plain,$seed)));
        $file = __DIR__.'/../Security/data/appAuth.yml';
        $yml = new YamlSecurityLoader(array('uri'=>$file));
        $configs = $yml->read($file);
        $configs['application'][$name] = array();
        $configs['application'][$name]=array(
            'host'=>$host,
            'port'=>$port,
            'ttl'=>$ttl,
            'remote'=>$remote,
            'key'=>$key
        );
        $yml->write($configs);
        $output->writeln(sprintf('La aplicacion "%s" ha sido agregada.', $name));
        $output->writeln(sprintf('La llave "%s" ha sido creada.', $key));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('name')) {
            $uri = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Introduzca nombre  de la aplicacion: ',
                function($uri) {
                    if (empty($uri)) {
                        throw new \Exception('malformed.uri');
                    }

                    return $uri;
                }
            );
            $input->setArgument('name', $uri);
        }

        if (!$input->getArgument('host')) {
            $host = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Introduzca host  de la aplicacion cliente: ',
                function($host) {
                    if (empty($host)) {
                        throw new \Exception('malformed.host');
                    }

                    return $host;
                }
            );
            $input->setArgument('host', $host);
        }

        if (!$input->getArgument('remote')) {
            $remote = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Introduzca IP  de la aplicacion cliennte: ',
                function($remote) {
                    if (empty($remote)) {
                        throw new \Exception('malformed.port');
                    }

                    return $remote;
                }
            );
            $input->setArgument('remote', $remote);
        }

        if (!$input->getArgument('port')) {
            $port = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Introduzca puerto  de la aplicacion cliennte: ',
                function($port) {

                    return $port;
                }
            );
            $input->setArgument('port', $port);
        }

        if (!$input->getArgument('ttl')) {
            $ttl = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Introduzca TTL  de la aplicacion cliennte: ',
                function($ttl) {

                    return $ttl;
                }
            );
            $input->setArgument('ttl', $ttl);
        }
    }
} 