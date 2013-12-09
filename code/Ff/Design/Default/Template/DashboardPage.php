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
                <div uiType="menu" id="topmenu" resource="content/menu" dataIdentity="topmenu"></div>
            </div>
            <div id="page-content" class="content row">
                <div id="page-left" class="col-md-4">

                </div>
                <div id="page-middle" class="jumbotron col-md-8">
                    <div uiType="form" id="dashboard" resource="dashboard"></div>
                </div>
            </div>
            <div id="page-footer" class="panel-footer"></div>
        </div>
    </form>
    <script type="text/javascript" src="/jquery.js"></script>
    <script type="text/javascript" src="/bootstrap.js"></script>
    </body>
</html>