<html lang="$(system/lang)">
    <head>
        <meta charset="$(system/charset)" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    </head>
    <body>
        <div id="page-header"></div>
        <div id="page-content" class="content">
            <div id="page-left" class="left"></div>
            <div id="page-middle" class="jumbotron">
                <data ui-type="Title" class="panel-title">$(page/data/name)</data>
                <data ui-type="Text" class="panel">$(page/data/content)</data>
            </div>
            <div id="page-right" class="right"></div>
        </div>
        <div id="page-footer" class="panel-footer"></div>
    </body>
</html>