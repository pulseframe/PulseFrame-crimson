<?php
$inMultiLineComment = false;
$totalLines = count($fileContent);
$padding = strlen($totalLines);
$formatString = "%0{$padding}d";

$highlightLinesForCurrentFile = array_filter($stackTraces, fn($trace) => $trace['file'] === $fileToView);
$highlightLines = array_column($highlightLinesForCurrentFile, 'line');

foreach ($fileContent as $lineNumber => $line) {
  $actualLineNumber = $lineNumber + 1;
  $trimmedLine = trim($line);

  if (strpos($trimmedLine, '/*') !== false) {
    $inMultiLineComment = true;
  }

  $isHighlighted = in_array($actualLineNumber, $highlightLines);
  $class = ($inMultiLineComment || preg_match('/^\s*(\/\/.*|#.*|\/*.*\*\/)$/', $trimmedLine) || preg_match('/\s+(\/\/.*|#.*|\/*.*\*\/)$/', $trimmedLine))
    ? 'text-green-500'
    : ($isHighlighted ? 'bg-blue-500 rounded-sm text-white' : 'text-gray-300');

  if (strpos($trimmedLine, '*/') !== false) {
    $inMultiLineComment = false;
  }

  $formattedLineNumber = sprintf($formatString, $actualLineNumber);

  echo '<span id="line-' . $actualLineNumber . '" class="' . $class . '">';
  echo '<span class="line-number bg-gray-900" style="user-select: none;">' . $formattedLineNumber . ': </span>';
  echo htmlspecialchars(rtrim($line));
  echo '</span><br>';
}
