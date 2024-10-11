<?php

return [
  'keywords' => [
    'pattern' => "\\b(break|case|catch|class|const|continue|declare|default|die|do|else|elseif|empty|enddeclare|endfor|endforeach|endif|endswitch|endwhile|exit|for|foreach|function|if|include|include_once|return|require|require_once|switch|try|unset|while|use|as|namespace|trait|final|public|protected|private|static|abstract|interface|extends|instanceof|throw|new|clone|yield|await|echo|global|match)\\b",
  ],
  'strings' => [
    'pattern' => "\"([^\"\\\\]*(\\\\.)*)*|'([^'\\\\]*(\\\\.)*)*'",
  ],
  'numbers' => [
    'integer' => '\\b\\d+\\b',
    'float' => '\\b\\d+\\.\\d+\\b',
  ],
  'operators' => [
    'pattern' => '[=+\-*\/<>!&|^%]+',
  ],
  'constants' => [
    'true' => '\\btrue\\b',
    'false' => '\\bfalse\\b',
    'null' => '\\bnull\\b',
  ],
  'regex' => [
    'variables' => '\\$[a-zA-Z_][a-zA-Z0-9_]*',
    'functions' => '\\bfunction\\s+([a-zA-Z_][a-zA-Z0-9_]*)',
    'classes' => '\\bclass\\s+([a-zA-Z_][a-zA-Z0-9_]*)',
    'namespace' => '\\bnamespace\\s+([a-zA-Z_][a-zA-Z0-9_\\\\]*)',
    'attributes' => '#@([a-zA-Z_][a-zA-Z0-9_]*)#',
  ],
  'comments' => [
    'single' => '(?<!\S)(\/\/.*|#.*)',
    'multi' => '/\/\*.*?\*\//s',
  ],
  'annotations' => [
    'pattern' => '#@([a-zA-Z_][a-zA-Z0-9_]*)#',
  ],
];
