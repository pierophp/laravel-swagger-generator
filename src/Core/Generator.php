<?php

namespace LaravelSwaggerGenerator\Core;

class Generator
{
    protected $route;

    protected $reflectionMethod;

    protected $description;

    public function generate()

    {
        $api = app('Dingo\Api\Routing\Router');
        $routes = $api->getRoutes();
        $i = 0;
        foreach($routes['v1'] as $dingoRoute) {
            $this->route = $dingoRoute->getOriginalRoute();
            $i++;

            $classExplode = explode('@', $this->route->action['uses']);
            $className = $classExplode[0];
            $methodName = $classExplode[1];
            
            $reflectionClass = new \ReflectionClass($className);
            $this->reflectionMethod = $reflectionClass->getMethod($methodName);
            $filename = $reflectionClass->getFileName();
            
            $originalComment = $this->reflectionMethod->getDocComment();;
            $newComment = $this->parseComment($originalComment);

            $fileContent = file_get_contents($filename);
            $fileContent = str_replace($originalComment, $newComment, $fileContent);

            // file_put_contents($filename, $fileContent);
            
            if ($i === 10) {
                exit;
            }
        }
    }

    protected function parseComment($originalComment) 
    {
        $newComment = '';
        $lines = explode("\n", $originalComment);   
        $i = 0;
        $docComments = '';
        $returnComments = '';
        $docCommentsOpen = false;
        $totalParenthesis = 0;

        $this->description = '';

        foreach($lines as $line) {
            $i++;

            $parsedLine = trim($line, ' */');
            $parseStartString = '@SWG\\';

            if (substr($parsedLine, 0, strlen($parseStartString)) === $parseStartString) {
                $docCommentsOpen = true;
            }

            $parseStartString = '@return';
            if (substr($parsedLine, 0, strlen($parseStartString)) === $parseStartString) {
                $returnComments = $line;
            }

            $parseStartString = '@';
            if (substr($parsedLine, 0, strlen($parseStartString)) !== $parseStartString && !$this->description) {
                $this->description = $parsedLine;
            }

            if ($docCommentsOpen) {
                $docComments .= $line . "\n";
                $startParenthesis = substr_count($parsedLine, '(');
                $endParenthesis = substr_count($parsedLine, ')');
                $totalParenthesis += $startParenthesis;
                $totalParenthesis -= $endParenthesis;
                if ($totalParenthesis === 0) {
                    $docCommentsOpen = false;
                }
            }

            $newComment .= $line;

            if($i < count($lines)){
                $newComment .= "\n";
            }
        }

        $docComments = trim($docComments, "\n");
        $newDocComments = $this->generateComment();

        if ($docComments) {
            $newComment = str_replace($docComments, $newDocComments, $newComment);
        } else if ($returnComments){
            $newComment = str_replace($returnComments, $newDocComments . "\n" . $returnComments, $newComment);
        } else {

        }
        

        dump($newComment);

        return $newComment;
    }

    protected function generateComment()
    {
        $operationId = '';
        if (!empty($this->route->action['as'])) {
            $operationId = $this->route->action['as'];
        }
        
        $params = [
            'path' => $this->route->uri,
            'summary' => $this->description,
            'description' => $this->description,
            'operationId' => $operationId,
            'consumes' => '{"application/json"}',
            'produces' => '{"application/json"}',
        ];

        $method = '\\' . ucfirst(strtolower($this->route->methods[0]));
        $comments = '';
        $comments .= "     * @SWG{$method}(' . \n";
        foreach ($params as $key => $val){
            $comments .= "     *     {$key}=\"{$val}\",\n";
        } 

        foreach ($this->reflectionMethod->getParameters() as $parameter) {
            if ($parameter->getType() && in_array($parameter->getType(), [
                'Illuminate\Http\Request',
            ])) {
                continue;
            }

            $required = 'true';
            if ($parameter->isOptional()) {
                $required = 'false';
            }
            $comments .= "     *     @SWG\Parameter(\n";
            $comments .= "     *         in=\"path\",\n";
            $comments .= "     *         name=\"{$parameter->name}\",\n";
            $comments .= "     *         description=\"\",\n";
            if ($parameter->getType()) {
                $comments .= "     *         type=\"{$parameter->getType()}\",\n";
            }
       
            $comments .= "     *         required={$required}\n";
            $comments .= "     *     ),\n";
        }


        $comments = trim($comments, ",\n") . "\n";
        $comments .= '     * )';
        return $comments;
    }
}
