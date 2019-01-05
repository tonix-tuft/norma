<?php

/* 
 * Copyright (c) 2019 Anton Bagdatyev (Tonix-Tuft)
 * 
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 * 
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Norma\Core\Env;

use Norma\Core\Runtime\RuntimeEnum;
use Norma\Core\Runtime\RuntimeInterface;
use Norma\Core\Runtime\CLIApplicationRuntime;
use Norma\Core\Runtime\WebApplicationRuntime;
use Norma\Core\Oops\ErrorCapturerInterface;
use Norma\Core\Oops\ErrorCapturer;
use Composer\Autoload\ClassLoader;

/**
 * A class which has static as well as instance helper methods used by the framework.
 * 
 * Client's code SHOULD NOT rely on this component within their own implementations, because it SHOULD only be used by the framework.
 * However, the client's code MAY use the methods provided by this component within the environment configuration in order to expose environment variables
 * to the {@link Norma\Core\Env\EnvInterface} component and use that component within the other configuration files.
 * 
 * @author Anton Bagdatyev (Tonix-Tuft) <antonytuft@gmail.com>
 */
class Norma {
    
    /**
     * Returns the name of the Norma application's environment.
     * 
     * @return string A string identifying the environment of the Norma application.
     */
    public static function environment() {
        static $environment = null;
        if (is_null($environment)) {
            if (file_exists(static::appDir() . '/norma/environment.private.php')) {
                $environment = require static::appDir() . '/norma/environment.private.php';
            }
            else {
                $environment = require static::appDir() . '/norma/environment.php';
            }   
        }
        return $environment;
    }
    
    /**
     * Returns the runtime identifier of the Norma application.
     * 
     * @return string A string identifying the runtime of the Norma application.
     */
    public static function runtime() {
        static $runtime = null;
        if (is_null($runtime)) {
            if (static::isCLI()) {
                $runtime = RuntimeEnum::CLI;
            }
            else {
                $runtime = RuntimeEnum::WEB;
            }
        }
        return $runtime;
    }

    /**
     * A framework's helper method used to determine the base path of the Norma application.
     * 
     * @param array|null $server Server parameters. Will default to `$_SERVER` if NULL is given.
     * @return string The Norma application's base path.
     */
    public static function appBasePath($server = NULL) {
        static $appBasePath = null;
        if (is_null($appBasePath)) {
            $serverParams = $server ?? $_SERVER ?? [];
            if (array_key_exists('NORMA_APP_URI_BASE_PATH', $serverParams)) {
                return $serverParams['NORMA_APP_URI_BASE_PATH'] ?? '';
            }

            $appDir = static::appDir();
            $docRoot = realpath($serverParams['DOCUMENT_ROOT']) ?? '';
            $appBasePath = '';
            if (strpos($appDir, $docRoot) === 0) {
                $appBasePath = substr($appDir, strlen($docRoot));
            }   
        }
        return $appBasePath;
    }
    
    /**
     * Returns the absolute path of the application's directory.
     * 
     * @return string The absolute path of the application's directory.
     */
    public static function appDir() {
        static $appDir = null;
        if (is_null($appDir)) {
            $currentDir = __DIR__;
            while (!file_exists($currentDir . '/vendor')) {
                $currentDir = dirname($currentDir);
            }
            $appDir = $currentDir;
        }
        return $appDir;
    }
    
    /**
     * A framework's helper method used to determine the absolute path of the `env.php` file.
     * 
     * @return string The `env.php` absolute path.
     */
    public static function envPath() {
        return static::appDir() . '/norma/app/config/env.php';
    }
    
    /**
     * Tests whether PHP is running from the command line.
     * 
     * @return bool TRUE if PHP is running from the command line, FALSE otherwise (running on a web server).
     */
    public static function isCLI() {
        return http_response_code() === false;
    }
    
    /**
     * Asserts the runtime of the Norma application matches its effective runtime.
     * 
     * @param string $runtimeToCheck The runtime to check.
     * @throws \RuntimeException If the runtime is not the effective runtime the application is being executed with.
     */
    protected static function assertRuntime($runtimeToCheck) {
        $effectiveRuntime = static::runtime();
        if ($runtimeToCheck !== $effectiveRuntime) {
            throw new \RuntimeException(sprintf('Could not execute the Norma application with the runtime "%s" because the effective runtime of the Norma application is "%s".', $runtimeToCheck, $effectiveRuntime));
        }
    }
    
    /**
     * Requires a file if it exists and returns the file's returned value.
     * 
     * @param string $file The file to require.
     * @return mixed The file's returned value or NULL if nothing is returned.
     */
    protected static function requireIfFileExists($file) {
        if (file_exists($file)) {
            return require $file;
        }
        return null;
    }
    
    /**
     * A factory method which creates a {@link Norma\Core\Runtime\RuntimeInterface}
     * runtime given a runtime identifier and an environment.
     * 
     * @param string $runtime The identifier of the runtime to create.
     * @param ClassLoader $autoloader An autoloader.
     * @return RuntimeInterface The created runtime.
     * @throws \RuntimeException If the runtime to create is not the runtime the application is being executed with.
     */
    public function makeRuntime($runtime, ClassLoader $autoloader): RuntimeInterface {
        static::assertRuntime($runtime);
        
        $env = $this->makeEnv();
        $errorHandler = $this->makeErrorCapturer();
        
        if ($runtime === RuntimeEnum::CLI) {
            return new CLIApplicationRuntime($env, $autoloader, $errorHandler);
        }
        else {
            return new WebApplicationRuntime($env, $autoloader, $errorHandler);
        }
    }
    
    /**
     * A factory method which creates the {@link Norma\Core\Env\EnvInterface}
     * environment for the application.
     * 
     * @return EnvInterface The environment of the application.
     */
    public function makeEnv(): EnvInterface {
        $envPath = Norma::envPath();
        $envDir = dirname($envPath);
        
        $env = static::requireIfFileExists($envPath) ?? [];
        
        // `NORMA_ENV` is kinda special, because it cannot be overridden by the environment variables defined in `env` files.
        // It can be changed only by modifying the special file `environment.php` and `environment.private.php`.
        $environment = $env['NORMA_ENV'] = static::environment();
        
        // Same goes for `NORMA_RUNTIME`.
        $runtime = $env['NORMA_RUNTIME'] = static::runtime();
        
        // Private, local overrides.
        $env = array_merge($env, static::requireIfFileExists($envDir . '/env.private.php') ?? []);
        
        // Environment overrides.
        $env = array_merge($env, static::requireIfFileExists($envDir . "/env.$environment.php") ?? []);
        
        // Private, local environment overrides.
        $env = array_merge($env, static::requireIfFileExists($envDir . "/env.$environment.private.php") ?? []);
        
        // Runtime overrides.
        $env = array_merge($env, static::requireIfFileExists($envDir . "/env.runtime-$runtime.php") ?? []);
        
        // Private, local runtime overrides.
        $env = array_merge($env, static::requireIfFileExists($envDir . "/env.runtime-$runtime.private.php") ?? []);
        
        // Runtime and environment overrides.
        $env = array_merge($env, static::requireIfFileExists($envDir . "/env.runtime-$runtime.$environment.php") ?? []);
        
        // Runtime, local environment overrides.
        $env = array_merge($env, static::requireIfFileExists($envDir . "/env.runtime-$runtime.$environment.private.php") ?? []);
        
        $env['NORMA_ENV'] = $environment;
        $env['NORMA_RUNTIME'] = $runtime;
        
        return new Env($env);
    }
    
    /**
     * A factory method which creates an error capturer of type {@link Norma\Core\Oops\ErrorCapturerInterface}.
     * 
     * @return ErrorCapturerInterface The error capturer.
     */
    public function makeErrorCapturer(): ErrorCapturerInterface {
        return new ErrorCapturer();
    }
    
}