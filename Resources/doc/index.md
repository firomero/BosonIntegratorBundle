Comenzando con BosonIntegratorBundle
=====================================

## Instalación
1)
$ composer require boson/integrator-bundle

2)
Habilitar el bundle en el AppKernel

 ``` php
 <?php
 // app/AppKernel.php

 public function registerBundles()
 {
     $bundles = array(
         // ...
        new UCI\Boson\IntegratorBundle\IntegratorBundle(),

     );
 }
 ```
 ## ¿Cómo seguir?
 2. dependencias.md
 3. httpcodes.md
 4. uris.md
 5. seguridad.md
 6. comandos.md

