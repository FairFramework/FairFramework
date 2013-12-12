<html lang="[system/lang]">
    <head>
        <meta charset="$(system/charset)" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="/bootstrap.css" />
    </head>
    <body>
    <form id="page-form" enctype="multipart/form-data" action="" method="POST" role="form">
        <div id="page-container" class="container">
            <div id="page-header">
                <ui type="menu" id="topmenu" local_reference_prefix="resource/content/menu/topmenu" />
            </div>
            <div id="page-content" class="content row">
                <div id="page-middle" class="jumbotron col-md-12">
                    <ui type="form" id="dashboard" local_reference_prefix="[system/base_uri]" />
                </div>
            </div>
            <div id="page-footer" class="panel-footer"></div>
        </div>
    </form>
    <script type="text/javascript" src="/jquery.js"></script>
    <script type="text/javascript" src="/bootstrap.js"></script>
    </body>
</html>