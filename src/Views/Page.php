<?php
require_once(ROOT_DIR . "/vendor/autoload.php");

use PulseFrame\Crimson\StackTraceHandler;

new StackTraceHandler($exception);
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
    <h1 class="text-4xl font-bold mb-4 flex"><?php echo $appName; ?>&nbsp;-&nbsp;
      <p class="text-red-500">Error <?php echo $statusCode; ?></p>
    </h1>

    <?php include 'Navbar.php'; ?>

    <div class="flex h-screen">
      <?php include 'Sidebar.php'; ?>
      <div class="w-3/4 pl-4 mb-44">
        <?php include 'ContentView.php'; ?>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      if (window.history.state === null) {
        window.history.replaceState(null, '', window.location.pathname);
      }

      const preElement = document.querySelector('pre');
      const lineToScroll = <?php echo json_encode($_GET['line'] ?? $lineToScroll ?? null); ?>;

      if (lineToScroll) {
        const lineElement = document.getElementById('line-' + lineToScroll);
        if (lineElement) {
          const lineRect = lineElement.getBoundingClientRect();
          const preRect = preElement.getBoundingClientRect();
          const scrollTop = lineRect.top - preRect.top + preElement.scrollTop - (preElement.clientHeight / 2) + (lineElement.clientHeight / 2);

          preElement.scrollTo({
            top: scrollTop,
            behavior: 'smooth'
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
          window.history.replaceState(null, '', '?file=<?php echo urlencode($fileToView); ?>&line=' + currentLine);
        }
      });
    });
  </script>
</body>

</html>