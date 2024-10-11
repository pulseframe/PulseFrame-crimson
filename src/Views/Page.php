<?php
require_once(ROOT_DIR . "/vendor/autoload.php");

use PulseFrame\Crimson\StackTraceHandler;

$stackTraceHandler = new StackTraceHandler($exception);
$fileContent = $stackTraceHandler->getFileContent();
$stackTraces = $stackTraceHandler->getStackTraces();
$fileToView = $stackTraceHandler->getFileToView();
$highlightLines = $stackTraceHandler->getHighlightLines();
$exceptionName = $stackTraceHandler->getExceptionName();

$logoPath = __DIR__ . '/../Assets/logo.png';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Error - <?php echo $statusCode; ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-900 text-gray-300 m-0 p-0">
  <div class="container mx-auto p-6 h-screen overflow-hidden">
    <div class="flex items-center select-none drag">
      <h1 class="text-3xl font-bold mb-4 w-max flex"><?php echo $appName; ?></h1>
      <div class="ml-auto flex items-center text-base text-center">
        <img src="<?php echo 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)); ?>" alt="Logo" class="my-2 w-32">
      </div>
    </div>

    <?php include 'Navbar.php'; ?>

    <div class="flex h-screen">
      <?php include 'Sidebar.php'; ?>
      <div class="w-2/3 pl-4 mb-44">
        <?php include 'ContentView.php'; ?>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const preElement = document.querySelector('pre');
      const lineToScroll = getCookie('line');

      if (lineToScroll) {
        const lineElement = document.getElementById('line-' + lineToScroll);
        if (lineElement) {
          const lineRect = lineElement.getBoundingClientRect();
          const preRect = preElement.getBoundingClientRect();
          const scrollTop = lineRect.top - preRect.top + preElement.scrollTop - (preElement.clientHeight / 2) + (lineElement.clientHeight / 2);

          preElement.scrollTo({
            top: scrollTop
          });
        }
      }

      preElement.addEventListener('scroll', function() {
        const lines = preElement.querySelectorAll('span[id^="line-"]');
        let currentLine = null;

        lines.forEach(line => {
          const rect = line.getBoundingClientRect();
          const preRect = preElement.getBoundingClientRect();
          if (rect.top >= preRect.top && rect.bottom <= preRect.bottom) {
            currentLine = line.id.split('-')[1];
          }
        });

        if (currentLine) {
          setCookie('line', currentLine, 1);
        }
      });

      function setCookie(name, value, days) {
        const d = new Date();
        d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "expires=" + d.toUTCString();
        document.cookie = name + "=" + value + ";" + expires + ";path=/";
      }

      function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
          let c = ca[i];
          while (c.charAt(0) == ' ') c = c.substring(1, c.length);
          if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
      }
    });
  </script>
</body>

</html>