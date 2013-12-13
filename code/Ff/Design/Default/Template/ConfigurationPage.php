<html lang="$(system/lang)">
    <head>
        <meta charset="$(system/charset)" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="/bootstrap.css" />
    </head>
    <body>
    <form id="page-form" enctype="multipart/form-data" action="" method="POST" role="form">
        <div id="page-container" class="container">
            <div id="page-header">
                <ui type="menu" id="topmenu" data="menu:resource/content/menu/topmenu" />
            </div>
            <div id="page-content" class="content row">
                <div id="page-left" class="left col-md-4">
                    <ui type="tree" id="configuration-tree" data="tree:resource/configuration"></ui>
                </div>
                <div id="page-middle" class="middle jumbotron col-md-8">
                    <ui type="form" id="configuration-form" data="form:[system/base_uri]" />
                </div>
            </div>
            <div id="page-footer" class="panel-footer"></div>
        </div>
    </form>
    <script type="text/javascript" src="/jquery.js"></script>
    <script type="text/javascript" src="/bootstrap.js"></script>
    </body>
</html>