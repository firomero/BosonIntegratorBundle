<?php
/**
 * Created by PhpStorm.
 * User: firomero
 * Date: 13/02/15
 * Time: 9:52
 */

namespace UCI\Boson\IntegratorBundle\Command;
use \UCI\Boson\IntegratorBundle\Manager\YamlConfiguration;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Esta clase tiene la responsabilidad de introducir aplicaciones en el entorno.
 * Class AddAplicationCommand
 * @package UCI\Boson\IntegratorBundle\Command
 */
class AddAplicationCommand extends ContainerAwareCommand{

    const PATTERN = '~^
            (http)://                                 # protocol
            (([\pL\pN-]+:)?([\pL\pN-]+)@)?          # basic auth
            (
                ([\pL\pN\pS-\.])+(\.?([\pL]|xn\-\-[\pL\pN-]+)+\.?) # a domain name
                    |                                              # or
                \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}                 # a IP address
                    |                                              # or
                \[
                    (?:(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){6})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:::(?:(?:(?:[0-9a-f]{1,4})):){5})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){4})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,1}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){3})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,2}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){2})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,3}(?:(?:[0-9a-f]{1,4})))?::(?:(?:[0-9a-f]{1,4})):)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,4}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,5}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,6}(?:(?:[0-9a-f]{1,4})))?::))))
                \]  # a IPv6 address
            )
            (:[0-9]+)?                              # a port (optional)
            (/?|/\S+)                               # a /, nothing or a / with something
        $~ixu';
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('integrator:application:add')
            ->setDescription('Registra una Aplicacion en el ecosistema')
            ->setDefinition(array(
                new InputArgument('uri', InputArgument::REQUIRED, 'The Application URL'),
            ))
            ->setHelp(<<<EOT
The <info>integrator:aplication:add</info> Este comando permite adicionar aplicaciones al ecosistema:

  <info>php app/console integrator:aplication:add http://myappserver.uci.cu/api/rest</info>
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uri = $input->getArgument('uri');
        $file = __DIR__.'/../Resources/config/integrator.yml';
        $yml = new YamlConfiguration($this->getContainer()->get("translator"));
        $configs = $yml->fileAsArray($file);
        array_push($configs['integrator']['server']['app_list'],$uri);
        $yml->arrayAsFile($configs,$file);
        $output->writeln(sprintf('La aplicacion "%s" ha sido agregada.', $uri));
    }

    /**
     * @see Command
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('uri')) {
            $uri = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Introduzca la uri de la aplicacion: ',
                function($uri) {
                    if (empty($uri)) {
                        throw new \Exception('malformed.uri');
                    }
                    $pattern = '/^(http|ftp|https)\:\/\/+[a-z0-9\.\_-]+$/i';
                    $pattern = '/^(http|ftp|https)\:\/\/+[a-z0-9\.*\_-]+\[/[a-z0-9\.\_-]]*$/i';

                    if (!preg_match($pattern,$uri)) {
                        $translator = $this->getContainer()->get('translator');
                        $messageE5 =$translator->trans('integrator.excepciones.E5.mensaje', array(), 'translatesexcepciones');
                        throw new IntegratorException($messageE5);

                    }


                    return $uri;
                }
            );
            $input->setArgument('uri', $uri);
        }
    }

} 