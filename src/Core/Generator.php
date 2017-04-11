<?php

namespace LaravelSwaggerGenerator\Core;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use LaravelSwaggerGenerator\Mapping\Get;

class Generator
{
    public function generate()
    {
        new Get([]);

        $api = app('Dingo\Api\Routing\Router');
        $routes = $api->getRoutes();
        $annotationReader = new AnnotationReader();

        $i = 0;
        foreach($routes['v1'] as $dingoRoute) {
            $route = $dingoRoute->getOriginalRoute();
            $i++;

            $classExplode = explode('@', $route->action['controller']);
            $className = $classExplode[0];
            $methodName = $classExplode[1];
            $reflectionClass = new \ReflectionClass($className);
            $reflectionMethod = $reflectionClass->getMethod($methodName);
            $filename = $reflectionClass->getFileName();
            
            $originalComment = $reflectionMethod->getDocComment();;
            $newComment = $this->parseComment($route, $originalComment);

            dump($filename);
            dd($annotationReader->getMethodAnnotations($reflectionMethod));

            $fileContent = file_get_contents($filename);
            $fileContent = str_replace($originalComment, $newComment, $fileContent);

            // file_put_contents($filename, $fileContent);




            exit;
            dump($route->uri);
            dump($route->methods);
            dump($route->controller);
            dump($route->action);
            dump($route->parameters);
            dump($route->parameterNames);

            
            if ($i === 1) {
                exit;
            }
        }
    }

    protected function parseComment($route, $originalComment) 
    {
        $newComment = '';
        $lines = explode("\n", $originalComment);   
        $i = 0;     
        foreach($lines as $line) {
            $i++;
            if ($i == 2) {
                $newComment .= '     * TESTE' . "\n";    
            }
            $newComment .= $line;

            if($i < count($lines)){
                $newComment .= "\n";
            }
        }

        return $newComment;
    }
}
