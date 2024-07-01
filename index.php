<!DOCTYPE html>
<html>
<head>
    <title>File Download Progress</title>
</head>
<body>
    <button id="startDownload">Start Download</button>
    <div id="progressContainer" style="display: none;">
        <p>Download Progress: <span id="progress">0</span>%</p>
    </div>

    <script>
        document.getElementById('startDownload').addEventListener('click', function () {
            document.getElementById('progressContainer').style.display = 'block';
            downloadChunk();
        });

        function downloadChunk() {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'download.php', true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    document.getElementById('progress').innerText = response.progress.toFixed(2);

                    if (response.status === 'downloading') {
                        setTimeout(downloadChunk, 1000);
                    } else {
                        alert('Download completed!');
                    }
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>
