<?php


require_once 'Lexer.php';
require_once 'Parser.php';
require_once 'Compiler.php';


class TemplateEngine {
    private string $basePath;
    private string $lexer;
    private string $parser;
    private string $compiler;

    public function __construct(string $basePath) {
        $this->basePath = $basePath;
    }

    public function render(string $templatePath, array $parameters = []) {
        $source = file_get_contents($this->basePath.'/'.$templatePath);
        $lexer = new Lexer($source);
        $parser = new Parser($lexer);

        $compiler = new Compiler($parser->parse());
        return $compiler->compile($parameters);
    }
}
