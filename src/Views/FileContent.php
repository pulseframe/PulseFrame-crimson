<?php

$themeConfig = json_decode(file_get_contents(__DIR__ . '/../theme.json'), true);
$syntaxRules = require(__DIR__ . '/../Rules.php');

$highlightLine = isset($_COOKIE['line']) ? intval($_COOKIE['line']) : null;

$inMultiLineComment = false;
$totalLines = count($fileContent);
$padding = strlen($totalLines);
$formatString = "%0{$padding}d";

$highlightLinesForCurrentFile = array_filter($stackTraces, fn($trace) => $trace['file'] === $fileToView);
$highlightLines = array_column($highlightLinesForCurrentFile, 'line');

foreach ($fileContent as $lineNumber => $line) {
  $actualLineNumber = $lineNumber + 1;
  $trimmedLine = trim($line);

  $class = 'text-gray-300'; // Default class

  // Highlight specific lines
  if ($actualLineNumber === $highlightLine || in_array($actualLineNumber, $highlightLines)) {
    $class = 'bg-blue-600/50 !text-white';
  }

  // Check for multi-line comments
  if ($inMultiLineComment) {
    $class = $themeConfig['comments']['colorClass']; // Continue with comment styling
  } elseif (strpos($trimmedLine, '/*') !== false) {
    $inMultiLineComment = true;
    $class = $themeConfig['comments']['colorClass']; // Start of multi-line comment
  }

  // Apply syntax highlighting rules
  // Keywords
  if (preg_match('/' . $syntaxRules['keywords']['pattern'] . '/m', $trimmedLine)) {
    $class .= " " . $themeConfig['keywords']['colorClass'];
  }

  // Strings
  if (preg_match('/' . $syntaxRules['strings']['pattern'] . '/m', $trimmedLine)) {
    $class .= " " . $themeConfig['strings']['colorClass'];
  }

  // Numbers
  if (preg_match('/' . $syntaxRules['numbers']['integer'] . '/', $trimmedLine)) {
    $class .= " " . $themeConfig['numbers']['integer'];
  }
  if (preg_match('/' . $syntaxRules['numbers']['float'] . '/', $trimmedLine)) {
    $class .= " " . $themeConfig['numbers']['float'];
  }

  // Operators
  if (preg_match('/' . $syntaxRules['operators']['pattern'] . '/', $trimmedLine)) {
    $class .= " " . $themeConfig['operators']['colorClass'];
  }

  // Constants
  foreach (['true', 'false', 'null'] as $constant) {
    if (preg_match('/' . $syntaxRules['constants'][$constant] . '/', $trimmedLine)) {
      $class .= " " . $themeConfig['constants'][$constant];
      break;
    }
  }

  // Regex variables, functions, classes
  if (preg_match('/' . $syntaxRules['regex']['variables'] . '/', $trimmedLine)) {
    $class .= " " . $themeConfig['regex']['variables'];
  }
  if (preg_match('/' . $syntaxRules['regex']['functions'] . '/', $trimmedLine)) {
    $class .= " " . $themeConfig['regex']['functions'];
  }
  if (preg_match('/' . $syntaxRules['regex']['classes'] . '/', $trimmedLine)) {
    $class .= " " . $themeConfig['regex']['classes'];
  }

  // Comments
  if (preg_match('/' . $syntaxRules['comments']['single'] . '/', $trimmedLine)) {
    $class = $themeConfig['comments']['colorClass']; // Use comment color
  }
  if (preg_match($syntaxRules['comments']['multi'], $trimmedLine)) {
    $class .= " " . $themeConfig['comments']['colorClass'];
  }

  // End of multi-line comment check
  if ($inMultiLineComment && strpos($trimmedLine, '*/') !== false) {
    $inMultiLineComment = false;
  }

  // Format the line number
  $formattedLineNumber = sprintf($formatString, $actualLineNumber);

  // Output the line
  echo '<span id="line-' . $actualLineNumber . '" class="' . trim($class) . '">';
  echo '<span class="line-number text-teal-500" style="user-select: none;">' . $formattedLineNumber . ': </span>';
  echo htmlspecialchars(rtrim($line));
  echo '</span><br>';
}
